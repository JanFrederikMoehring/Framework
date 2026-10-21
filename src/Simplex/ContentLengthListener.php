<?php

namespace Simplex;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class ContentLengthListener implements EventSubscriberInterface
{
    public function onResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();
        $headers = $response->headers;

        if(!$headers->has('Content-Lenght') &&!$headers->has('Transfer-Encoding')) {
            $headers->set('Content-Lenght', strlen($response->getContent()));
        }
    }

    public static function getSubscribedEvents(): array
    {
        return ['response' => ['onResponse', -255]];
    }
}