<?php
namespace SoampliApps\Base\ServiceProviders;

interface ServiceProviderInterface
{
	public function __construct($boot_priority = 10, $key = null);

	public function register(\SoampliApps\Base\Application $application);

	public function boot();

	public function getBootPriority();
}
