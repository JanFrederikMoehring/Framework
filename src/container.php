<?php

use Simplex\Framework;
use Symfony\Component\DependencyInjection;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher;
use Symfony\Component\HttpFoundation;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Routing;
use Symfony\Component\HttpKernel\EventListener;
use Symfony\Component\HttpKernel\Controller;

$container = new DependencyInjection\ContainerBuilder();
$container->register('context', Routing\RequestContext::class);

$container->register('request_stack', HttpFoundation\RequestStack::class);
$container->register('controller_resolver', Controller\ControllerResolver::class);
$container->register('argument_resolver', Controller\ArgumentResolver::class);

$container->register('listener.router', EventListener\RouterListener::class)
    ->setArguments([new Reference('matcher'), new Reference('request_stack')]);

$container->register('listener.exception', EventListener\ErrorListener::class)
    ->setArguments(['Calendar\Controller\ErrorController::exception']);

$container->register('matcher', Routing\Matcher\UrlMatcher::class)
    ->setArguments([new Reference('routes'), new Reference('context')]);

$container->register('dispatcher', EventDispatcher\EventDispatcher::class)
    ->addMethodCall('addSubscriber', [new Reference('listener.router')])
    ->addMethodCall('addSubscriber', [new Reference('listener.response')])
    ->addMethodCall('addSubscriber', [new Reference('listener.exception')]);

$container->register('framework', Framework::class)
    ->setArguments([
        new Reference('dispatcher'),
        new Reference('controller_resolver'),
        new Reference('request_stack'),
        new Reference('argument_resolver'),
    ]);

return $container;

