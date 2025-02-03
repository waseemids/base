<?php
require_once(__DIR__.'/../../vendor/autoload.php');
require_once(__DIR__.'/splClassLoader.php');
$classLoader = new SplClassLoader('SoampliApps', __DIR__.'/../');
$classLoader->register();
