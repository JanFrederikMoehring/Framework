<?php
namespace Simplex\Tests;

use Calendar\Controller\LeapYearController;
use PHPUnit\Framework\TestCase;
use Simplex\Framework;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\Routing;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\EventDispatcher\EventDispatcher;

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

            $dispatcher = new EventDispatcher();

            return new Framework($dispatcher, $matcher, $controllerResolver, $argumentResolver);
        }

    public function testErrorHandling(): void
    {
        $framework = $this->getFrameworkForException((new \RuntimeException));

        $response = $framework->handle(new Request());

        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testControllerResponse(): void
    {
        $matcher = $this->createMock(Routing\Matcher\UrlMatcherInterface::class);

        $matcher
            ->expects($this->once())
            ->method('match')
            ->willReturn([
                '_route' => 'is_leap_year/{year}',
                'year' => '2008',
                '_controller' => [new LeapYearController(), 'index']
            ]);
        $matcher
            ->expects($this->once())
            ->method('getContext')
            ->willReturn($this->createMock(Routing\RequestContext::class));

        $controllerResolver = new ControllerResolver();
        $argumentResolver = new ArgumentResolver();

        $dispatcher = new EventDispatcher();

        $framework = new Framework($dispatcher, $matcher, $controllerResolver, $argumentResolver);

        $response = $framework->handle(new Request());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('Yep, this is a leap year!', $response->getContent());
    }
}