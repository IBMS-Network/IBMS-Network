<?php

$loader = require_once __DIR__ . '/../vendor/autoload.php';

require_once (__DIR__ . "/config/config.php");
$modules_names = array('general');
require_once (ENGINE_PATH . "sys_index.php");

use classes\clsI;

// Thumbnails generator.
$i = new clsI();
$i->thumbnailGenerator();
