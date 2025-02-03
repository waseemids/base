<?php
namespace SoampliApps\Base\ServiceProviders;
use Twig\Loader\FilesystemLoader as TwigFilesystemLoader;
use Twig\Environment as TwigEnvironment;
use Twig\Extension\DebugExtension as TwigDebugExtension;

class TwigServiceProvider implements ServiceProviderInterface
{
    protected $bootPriority = 0;
    protected $key;

    public function __construct($boot_priority = 10, $key = null)
    {
        $this->bootPriority = $boot_priority;
        $this->key = (is_null($key)) ? 'twig' : $key;
    }

    public function register(\SoampliApps\Base\Application $application)
    {
        $container = $application->getContainer();

        $container[$this->key] = function ($c) {
            $settings = $c->getSettingFromNestedKey($nested_key = [$this->key]);
            $cache_settings = $settings['cache'];
            $loader = new TwigFilesystemLoader($settings['path']);
            $twig_config = [
                'cache' => (isset($cache_settings['enabled']) && true == $cache_settings['enabled']) ? ((isset($cache_settings['path'])) ? $cache_settings['path'] : false) : false,
            ];
            if (1 == $settings['debug']) {
                $twig_config['debug'] = true;
            }
            $twig = new TwigEnvironment($loader, $twig_config);
            $twig->addExtension(new TwigDebugExtension());

            return $twig;
        };

        $container['template_variables'] = function ($c){
            return new \SoampliApps\Base\Views\TemplateVariables();
        };

        $application->registerInvokableFunction('render', function ($template, $tags) use ($application) {
            return $application->getContainer()[$this->key]->render($template, $tags);
        });

        $application->registerInvokableFunction('getTemplateEngineAdapter', function() use ($application) {
            return new \SoampliApps\Base\ServiceProviders\Twig\TwigTemplateEngineAdapter($application->getContainer()[$this->key]);
        });

        $container['template_engine_adapter'] = function ($c) use ($application) {
            return $application->getTemplateEngineAdapter();
        };

        $application->registerInvokableFunction('getView', function($view_name = null, $template_name = null, $variables = null) use ($application) {
            if (is_null($view_name)) {
                $view_class = "\SoampliApps\Base\Views\BasicView";
            } else {
                $view_class = "\SoampliApps\Base\Views\\" . $view_name;
            }
            $view = new $view_class();
        });
    }

    public function boot() {}

    public function getBootPriority()
    {
        return $this->bootPriority;
    }
}
