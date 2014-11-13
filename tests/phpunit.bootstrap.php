<?php
error_reporting(E_ALL | E_STRICT);
date_default_timezone_set('Asia/Tokyo');

defined('PHPEEL_BOOT_TYPE') || define('PHPEEL_BOOT_TYPE', 'public');
defined('PHPEEL_START_TIME') || define('PHPEEL_START_TIME', microtime(true));
defined('PHPEEL_START_MEMORY') || define('PHPEEL_START_MEMORY', memory_get_usage());

require realpath(__DIR__) . '/../vendor/autoload.php';
