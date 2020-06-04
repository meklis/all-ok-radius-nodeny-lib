<?php
require __DIR__ . "/../vendor/autoload.php";
try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
} catch (\Exception $e) {}
