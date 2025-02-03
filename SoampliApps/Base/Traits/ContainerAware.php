<?php
namespace SoampliApps\Base\Traits;

trait ContainerAware
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }
}
