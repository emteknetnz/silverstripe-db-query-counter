<?php

namespace emteknetnz\DBQueryCounter;

use SilverStripe\PolyExecution\PolyOutput;

$version = class_exists(PolyOutput::class) ? '6' : '5';
$src = __DIR__ . '/src';
$inc = __DIR__ . '/inc';
$filename = "$src/DBQueryCounterTask.php";

if (!file_exists($filename)) {
    $contents = file_get_contents("$inc/DBQueryCounterTaskCMS$version.php.inc");
    file_put_contents($filename, $contents);
}
