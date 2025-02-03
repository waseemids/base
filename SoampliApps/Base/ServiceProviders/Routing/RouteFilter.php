<?php
namespace SoampliApps\Base\ServiceProviders\Routing;

interface RouteFilter extends Filter
{
    public function filterRoute(array $route);
}
