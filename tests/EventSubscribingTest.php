<?php

declare(strict_types=1);

namespace Snicco\Component\EventDispatcher\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Snicco\Component\EventDispatcher\BaseEventDispatcher;
use Snicco\Component\EventDispatcher\EventDispatcher;
use Snicco\Component\EventDispatcher\EventSubscriber;
use Snicco\Component\EventDispatcher\ListenerFactory\NewableListenerFactory;
use Snicco\Component\EventDispatcher\Tests\fixtures\AssertListenerResponse;
use Snicco\Component\EventDispatcher\Tests\fixtures\Event\EventStub;

/**
 * @internal
 */
final class EventSubscribingTest extends TestCase
{
    use AssertListenerResponse;

    private EventDispatcher $dispatcher;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resetListenersResponses();
        $this->dispatcher = $this->getDispatcher();
    }

    protected function tearDown(): void
    {
        $this->resetListenersResponses();
        parent::tearDown();
    }

    /**
     * @test
     */
    public function all_subscribed_events_are_added(): void
    {
        $this->dispatcher->subscribe(TestSubscriber::class);

        $event = new EventStub('foo', 'bar');

        $this->dispatcher->dispatch($event);

        $this->assertSame('SUBSCRIBED', $event->val1);
    }

    /**
     * @test
     */
    public function test_exception_for_wrong_interface(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('does not implement');
        $this->dispatcher->subscribe(BadSubscriber::class);
    }

    /**
     * @test
     */
    public function test_exception_for_wrong_method(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('does not have a [bogus] method');

        $this->dispatcher->subscribe(BadMethodSubscriber::class);
    }

    private function getDispatcher(): BaseEventDispatcher
    {
        return new BaseEventDispatcher(new NewableListenerFactory());
    }
}

final class BadSubscriber
{
    public static function subscribedEvents(): array
    {
        return [
            EventStub::class => 'handleEvent',
        ];
    }
}

final class BadMethodSubscriber implements EventSubscriber
{
    public static function subscribedEvents(): array
    {
        return [
            EventStub::class => 'bogus',
        ];
    }
}

final class TestSubscriber implements EventSubscriber
{
    public static function subscribedEvents(): array
    {
        return [
            EventStub::class => 'handleEvent',
        ];
    }

    public function handleEvent(EventStub $event_stub): void
    {
        $event_stub->val1 = 'SUBSCRIBED';
    }
}
