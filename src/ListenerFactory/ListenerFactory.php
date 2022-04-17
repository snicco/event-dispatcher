<?php

declare(strict_types=1);

namespace Snicco\Component\EventDispatcher\ListenerFactory;

use Snicco\Component\EventDispatcher\Exception\CantCreateListener;

interface ListenerFactory
{
    /**
     * @param class-string $listener_class
     *
     * @throws CantCreateListener
     */
    public function create(string $listener_class, string $event_name): object;
}
