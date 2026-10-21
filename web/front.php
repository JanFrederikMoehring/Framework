<?php

require_once __DIR__ . '/../vendor/autoload.php';

$routes = include __DIR__ . '/../src/app.php';
$container = include __DIR__ . '/../src/container.php';

use Symfony\Component\HttpFoundation\Request;
use Simplex\StringResponseListener;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\EventListener;

$container->setParameter('debug', true);
$container->setParameter('charset', 'UTF-8');
$container->set('routes', $routes);

$container->register('listener.string_response', StringResponseListener::class);
$container->getDefinition('dispatcher')
    ->addMethodCall('addSubscriber', [new Reference('listener.string_response')]);

$container->register('listener.response', EventListener\ResponseListener::class)
    ->setArguments(['%charset%']);

$request = Request::createFromGlobals();

$response = $container->get('framework')->handle($request);

$response->send();