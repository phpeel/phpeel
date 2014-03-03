<?php
error_reporting(E_ALL | E_STRICT);
date_default_timezone_set('Asia/Tokyo');

defined('AIGIS_BOOT_TYPE') || define('AIGIS_BOOT_TYPE', 'public');
defined('AIGIS_START_TIME') || define('AIGIS_START_TIME', microtime(true));
defined('AIGIS_START_MEMORY') || define('AIGIS_START_MEMORY', memory_get_usage());

require realpath(__DIR__) . '/../../vendor/autoload.php';
