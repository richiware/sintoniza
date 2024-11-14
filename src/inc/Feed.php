<?php

class Feed
{
	public ?string $feed_url = null;
	public ?string $image_url = null;
	public ?string $url = null;
	public ?string $language = null;
	public ?string $title = null;
	public ?string $description = null;
	public ?\DateTime $pubdate = null;
	public int $last_fetch;
	protected array $episodes = [];

	public function __construct(string $url)
	{
		$this->feed_url = $url;
	}

	public function load(\stdClass $data): void
	{
		foreach ($data as $key => $value) {
			if ($key === 'id') {
				continue;
			}
			elseif ($key === 'pubdate' && $value) {
				$this->$key = new \DateTime($value);
			}
			else {
				$this->$key = $value;
			}
		}
	}

	public function sync(DB $db): void
    {
        $db->exec('START TRANSACTION');
        
        try {
            // Inserir/atualizar feed
            $db->upsert('feeds', $this->export(), ['feed_url']);
            $feed_id = $db->firstColumn('SELECT id FROM feeds WHERE feed_url = ?', $this->feed_url);
            
            // Atualizar subscrições
            $db->simple('UPDATE subscriptions SET feed = ? WHERE url = ?', $feed_id, $this->feed_url);

            // Inserir/atualizar episódios
            foreach ($this->episodes as $episode) {
                $episode = (array) $episode;
                $episode['pubdate'] = $episode['pubdate'] ? $episode['pubdate']->format('Y-m-d H:i:s') : null;
                $episode['feed'] = $feed_id;
                
                $db->upsert('episodes', $episode, ['feed', 'media_url']);
                
                // Atualizar referências nas ações
                $id = $db->firstColumn('SELECT id FROM episodes WHERE media_url = ? AND feed = ?', 
                    $episode['media_url'], 
                    $feed_id
                );
                
                if ($id) {
                    $db->simple('UPDATE episodes_actions SET episode = ? WHERE url = ?', 
                        $id, 
                        $episode['media_url']
                    );
                }
            }

            $db->exec('COMMIT');
        }
        catch (Exception $e) {
            $db->exec('ROLLBACK');
            throw $e;
        }
    }

	public function fetch(): bool
	{
		if (function_exists('curl_exec')) {
			$ch = curl_init($this->feed_url);
			curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
			curl_setopt($ch, CURLOPT_HTTPHEADER, ['User-Agent: oPodSync']);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			$body = @curl_exec($ch);

			if (false === $body) {
				$error = curl_error($ch);
			}

			curl_close($ch);
		}
		else {
			$ctx = stream_context_create([
				'http' => [
					'header'          => 'User-Agent: oPodSync',
					'max_redirects'   => 5,
					'follow_location' => true,
					'timeout'         => 30,
					'ignore_errors'   => true,
				],
				'ssl'  => [
					'verify_peer'       => true,
					'verify_peer_name'  => true,
					'allow_self_signed' => true,
					'SNI_enabled'       => true,
				],
			]);

			$body = @file_get_contents($this->feed_url, false, $ctx);
		}

		$this->last_fetch = time();

		if (!$body) {
			return false;
		}

		$xml = simplexml_load_string($body);
		$xml->registerXPathNamespace('itunes', 'http://www.itunes.com/dtds/podcast-1.0.dtd');

		foreach ($xml->channel->item as $item) {

			$title = isset($item->title) ? trim((string) $item->title) : null;
			if(isset($item->description)) {
				$description = trim((string) $item->description);
			} elseif(isset($item->{'content:encoded'})) {
				$description = trim((string) $item->{'content:encoded'});
			} else {
				$description = null;
			}
			$link = isset($item->link) ? trim((string) $item->link) : null;
			$pubDate = isset($item->pubDate) ? trim((string) $item->pubDate) : null;
			$audioUrl = isset($item->enclosure['url']) ? trim((string) $item->enclosure['url']) : null;

			if (isset($item->enclosure['length']) && ctype_digit((string) $item->enclosure['length'])) {
				$duration = (int) $item->enclosure['length'];
			} elseif (isset($item->xpath('itunes:duration')[0])) {
				$duration = $this->getDuration((string) $item->xpath('itunes:duration')[0]);
			} else {
				$duration = null;
			}

			if(isset($item->xpath('itunes:image/@href')[0])) {
				$imageUrl = trim((string) $item->xpath('itunes:image/@href')[0]);
			} elseif(isset($item->{'media:content'}['url'])) {
				$imageUrl = trim((string) $item->{'media:content'}['url']);
			} else {
				$imageUrl = null;
			}

			$guid = isset($item->guid) ? trim((string) $item->guid) : null;
			$creator = isset($item->{'dc:creator'}) ? trim((string) $item->{'dc:creator'}) : null;
			$episodeType = isset($item->xpath('itunes:episodeType')[0]) ? trim((string) $item->xpath('itunes:episodeType')[0]) : 'Tipo de episódio não disponível';

			$this->episodes[] = (object) [
				'image_url'   => $imageUrl,
				'url'         => $link,
				'media_url'   => $audioUrl,
				'pubdate'     => $pubDate ? new \DateTime($pubDate) : null,
				'title'       => $title,
				'description' => $description,
				'duration'    => $duration,
			];
		}

		$pubdate = $xml->channel->lastBuildDate;
		$language = $xml->channel->language;

		$this->title = $xml->channel->title;

		if (!$this->title) {
			return false;
		}

		$this->url = $xml->channel->link;
		$this->description = $xml->channel->description;
		$this->language = $language ? substr($language, 0, 2) : null;

		if(isset($xml->channel->{'itunes:image'}['href'])) {
			$imageUrl = trim((string) $xml->channel->{'itunes:image'}['href']);
		} elseif(isset($xml->channel->image->url)) {
			$imageUrl = trim((string) $xml->channel->image->url);
		} else {
			$imageUrl = null;
		}

		$this->image_url = $imageUrl;
		$this->pubdate = $pubdate ? new \DateTime($pubdate) : null;

		return true;
	}

	protected function getDuration(?string $str): ?int
	{
		if (!$str) {
			return null;
		}

		if (false !== strpos($str, ':')) {
			$parts = explode(':', $str);
			$duration = ($parts[2] ?? 0) * 3600 + ($parts[1] ?? 0) * 60 + $parts[0] ?? 0;
		}
		else {
			$duration = (int) $str;
		}

		// Duration is less than 20 seconds? probably an error
		if ($duration <= 20) {
			return null;
		}

		return $duration;
	}

	public function export(): array
	{
		$out = get_object_vars($this);
		$out['pubdate'] = $out['pubdate'] ? $out['pubdate']->format('Y-m-d H:i:s \U\T\C') : null;
		unset($out['episodes']);
		return $out;
	}

	public function listEpisodes(): array
	{
		return $this->episodes;
	}
}
