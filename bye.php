<?php
require_once __DIR__.'/vendor/autoload.php';

$response = new Response('Goodbye');
$response->send();