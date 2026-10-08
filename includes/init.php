<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/data.php';

$ff_viewer = ff_current_user();
$ff_viewer_name = $ff_viewer
    ? trim($ff_viewer['first_name'] . ' ' . $ff_viewer['last_name'])
    : null;
