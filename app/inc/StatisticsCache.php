<?php

class StatisticsCache {
    private string $cacheFile;
    private DB $db;

    public function __construct(DB $db) {
        $this->db = $db;
        $this->cacheFile = __DIR__ . '/../cache/statistics.json';
    }

    public function generateCache(): void {
        if (!is_dir(__DIR__ . '/../cache')) {
            mkdir(__DIR__ . '/../cache', 0755, true);
        }

        $stats = [
            'timestamp' => time(),
            'total_users' => $this->db->firstColumn("SELECT COUNT(*) FROM users"),
            'total_devices' => $this->db->firstColumn("SELECT COUNT(*) FROM devices"),
            'top_feeds' => $this->db->all("
                SELECT 
                    f.title,
                    f.feed_url,
                    f.url,
                    COUNT(s.id) as subscription_count
                FROM feeds f
                INNER JOIN subscriptions s ON s.feed = f.id AND s.deleted = 0
                GROUP BY f.id
                ORDER BY subscription_count DESC
                LIMIT 10
            "),
            'top_played' => $this->db->all("
                SELECT 
                    e.title,
                    e.url as episode_url,
                    f.url as feed_url,
                    f.title as feed_title,
                    COUNT(DISTINCT ea.user) as play_count
                FROM episodes e
                INNER JOIN feeds f ON e.feed = f.id
                INNER JOIN episodes_actions ea ON ea.episode = e.id AND ea.action = 'play'
                GROUP BY e.id
                ORDER BY play_count DESC
                LIMIT 10
            ")
        ];

        file_put_contents($this->cacheFile, json_encode($stats, JSON_PRETTY_PRINT));
    }

    public function getCachedStats(): ?object {
        if (!file_exists($this->cacheFile)) {
            return null;
        }

        $stats = json_decode(file_get_contents($this->cacheFile), false);
        return $stats;
    }
}
