<?php
namespace SoampliApps\Base\Traits;

trait Dispatcher
{
    public function dispatch($event_name, $payload = null)
    {
        $standard_event = $this->container['standard_event']($payload);
        $standard_event->setName('login.required');
        $this->container['dispatcher']->dispatch($standard_event,$event_name);
    }
}
