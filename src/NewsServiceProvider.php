<?php 

namespace Gwaps4nlp\NewsManager;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

class NewsServiceProvider extends ServiceProvider {


    /**
     * Register the service provider.
     *
     * @return void
     */
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        $viewPath = __DIR__.'/../resources/views';
        $this->loadViewsFrom($viewPath, 'news-manager');
        $this->publishes([
            $viewPath => base_path('resources/views/vendor/news-manager'),
        ], 'views');

        $migrationPath = __DIR__.'/../database/migrations';
        $this->loadMigrationsFrom($migrationPath);        
        $this->publishes([
            $migrationPath => base_path('database/migrations'),
        ], 'migrations');

        $config = $this->app['config']->get('news-manager.back-route', []);
        $config['namespace'] = 'Gwaps4nlp\NewsManager';

        $router->group($config, function($router)
        {
            $router->get('/', 'NewsController@getIndex');
            $router->get('/show/{news}', 'NewsController@getShow');
            $router->get('/create', 'NewsController@getCreate');
            $router->post('/create', 'NewsController@postCreate');
            $router->get('/edit/{news}', 'NewsController@getEdit');
            $router->post('/edit/{news}', 'NewsController@postEdit');
            $router->post('/delete', 'NewsController@postDelete');
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $configPath = __DIR__ . '/../config/news-manager.php';
        $this->mergeConfigFrom($configPath, 'news-manager');
        $this->publishes([$configPath => config_path('news-manager.php')], 'config');

        $this->app->singleton('command.news-manager.plan-email', function ($app) {
            return new Console\PlanEmailNews();
        });
        $this->commands('command.news-manager.plan-email');

        $this->app->singleton('command.news-manager.send', function ($app) {
            return new Console\SendNews();
        });
        $this->commands('command.news-manager.send');
    }


}
