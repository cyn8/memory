<?php namespace Orchestra\Memory;

use Orchestra\Support\Providers\ServiceProvider;

class MemoryServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('orchestra.memory', function ($app) {
            $manager = new MemoryManager($app);

            $manager->setConfig($app['config']['orchestra/memory::']);

            return $manager;
        });
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $path = realpath(__DIR__.'/../resources');

        $this->addConfigComponent('orchestra/memory', 'orchestra/memory', $path.'/config');

        $this->bootMemoryEvent();
    }

    /**
     * Register memory events during booting.
     *
     * @return void
     */
    protected function bootMemoryEvent()
    {
        $app = $this->app;

        $app->terminating(function () use ($app) {
            $app['orchestra.memory']->finish();
        });
    }
}
