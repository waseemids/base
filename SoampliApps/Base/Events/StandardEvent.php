<?php
namespace SoampliApps\Base\Events;

class StandardEvent extends \Symfony\Contracts\EventDispatcher\Event
{
    protected $payload = null;

    protected $name;

    public function __construct($payload = null)
    {
        if (!is_null($payload)) {
            $this->payload = $payload;
        }
    }
    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setPayload($payload)
    {
        $this->payload = $payload;
    }

    public function getPayload()
    {
        return $this->payload;
    }
}
