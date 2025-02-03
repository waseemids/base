<?php
namespace SoampliApps\Base;

class Application
{
    protected $container;
    protected $invokableFunctions = [];
    protected $serviceProviders = [];
    protected $bootSequence = null;
    protected $applicationRootFolder = null;
    protected $configurationKey = 'settings';

    public function __construct(\SoampliApps\Base\Containers\Container $container = null, $application_root_folder = null)
    {
        $this->container = (is_null($container)) ? new \SoampliApps\Base\Containers\Container() : $container;
        $this->bootSequence = new \splPriorityQueue();
        $this->applicationRootFolder = is_null($application_root_folder) ? __DIR__.'/' : $application_root_folder;
    }

    public function getApplicationRootFolder()
    {
        // TODO: refactor this away
        return $this->applicationRootFolder;
    }

    public function setConfigurationKey($key)
    {
        $this->configurationKey = $key;
    }

    public function loadConfiguration($configuration_file)
    {
        // TODO: migrate settings/configuration out of the application
        $file_contents = simplexml_load_file($configuration_file);
        if (false == $file_contents) {
            throw new \Exception('Configuration file ' . $configuration_file . ' not found');
        }
        $configuration = $this->convertSimpleXmlElementToArray($file_contents);

        $this->container[$this->configurationKey] = $configuration;
    }

    protected function convertSimpleXmlElementToArray(\SimpleXMLElement $configuration)
    {
        // TODO: migrate settings/configuration out of the application
        $configuration = (array) $configuration;
        $resulting_array = $configuration;
        foreach ($configuration as $key => $value) {
            if ($value instanceOf \SimpleXMLElement) {
                $resulting_array[$key] = $this->convertSimpleXmlElementToArray($value);
            }
        }

        return $resulting_array;
    }

    public function getExecutionContext()
    {
        return (php_sapi_name() == 'cli') ? 'cli' : 'web';
    }

    public function boot()
    {
        $this->bootSequence->rewind();
        while ($this->bootSequence->valid()) {
            $boot_step = $this->bootSequence->current();
            $boot_step->boot();
            $this->bootSequence->next();
        }
    }

    public function registerInvokableFunction($key, callable $function)
    {
        $this->invokableFunctions[$key] = $function;
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function registerServiceProviders(array $providers)
    {
        foreach ($providers as $provider) {
            $this->registerServiceProvider($provider);
        }
    }

    // Service providers are very inspired from Silex, however I've added
    // boot sequence support and explicit method invokation, this means that
    // providers boot() methods can be called in a developer defined order
    // and providers can have key methods registered with the application
    // key methods should be used sparingly, my recommendation would be for
    // when you _could_ use a boot sequence, but the call will alter the flow
    // of the application, e.g. route()
    public function registerServiceProvider(ServiceProviders\ServiceProviderInterface $provider)
    {
        $provider->register($this);
        $this->bootSequence->insert($provider, $provider->getBootPriority());
        $this->serviceProviders[] = $provider;
    }

    // This magic method allows support for service providers key method invokation
    public function __call($method, $args)
    {
        if (array_key_exists($method, $this->invokableFunctions)) {
            return call_user_func_array($this->invokableFunctions[$method], $args);
        } else {
            throw new \RuntimeException("Invokable method " . $method . " has not been registered with the application");
        }
    }
}
