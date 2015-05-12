<?php

if ($autoload = stream_resolve_include_path('vendor/autoload.php')) {
    require($autoload);
}

define('APP_TESTDIR', __DIR__);