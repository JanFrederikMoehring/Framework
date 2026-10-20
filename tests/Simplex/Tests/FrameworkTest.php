<?php
namespace Simplex\Tests;

use PHPUnit\Framework\TestCase;
use Simplex\Framework;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\Routing;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class FrameworkTest extends TestCase
{
    public function testNotFoundHandling(): void
        {
            $framework = $this->getFrameworkForException(new ResourceNotFoundException());

            $response = $framework->handle(new Request());

            $this->assertEquals(404, $response->getStatusCode());
        }


    private function getFrameworkForException($exception): Framework
        {
            $matcher = $this->createMock(Routing\Matcher\UrlMatcherInterface::class);

            $matcher
                ->expects($this->once())
                ->method('match')
                ->willThrowException($exception)
                ;
            $matcher
                ->expects($this->once())
                ->method('getContext')
                ->willReturn($this->createMock(Routing\RequestContext::class))
                ;
            $controllerResolver = $this->createMock(ControllerResolverInterface::class);
            $argumentResolver = $this->createMock(ArgumentResolverInterface::class);

            return new Framework($matcher, $controllerResolver, $argumentResolver);
        }
}