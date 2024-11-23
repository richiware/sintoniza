<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../inc/StatisticsCache.php';

$db = new DB(DB_HOST, DB_NAME, DB_USER, DB_PASS);
$cache = new StatisticsCache($db);
$cache->generateCache();

echo "Statistics cache updated successfully at " . date('Y-m-d H:i:s') . "\n";
