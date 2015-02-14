<?php namespace Zofe\Rapyd;

use Illuminate\Html\FormBuilder;
use Illuminate\Html\HtmlBuilder;
use Illuminate\Support\ServiceProvider;

class RapydServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {

        // $this->package('zofe/rapyd', 'rapyd');
        $this->loadViewsFrom(__DIR__.'/../views', 'rapyd');


        $this->publishes([
            __DIR__.'/../public/assets' => public_path('packages/zofe/rapyd/assets')
        ], 'assets');
        
        $this->publishes([
            __DIR__.'/../config/rapyd.php' => config_path('rapyd.php'),
        ], 'config');


        $this->mergeConfigFrom(
            __DIR__.'/../config/rapyd.php', 'rapyd'
        );

        include __DIR__ . '/routes.php';
        include __DIR__ . '/macro.php';
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app = static::make($this->app);
        
        $this->app->booting(function () {
            $loader  =  \Illuminate\Foundation\AliasLoader::getInstance();
            
            $loader->alias('Rapyd'     , 'Zofe\Rapyd\Facades\Rapyd'     );
            
            //deprecated .. and more facade are really needed ?
            $loader->alias('DataSet'   , 'Zofe\Rapyd\Facades\DataSet'   );
            $loader->alias('DataGrid'  , 'Zofe\Rapyd\Facades\DataGrid'  );
            $loader->alias('DataForm'  , 'Zofe\Rapyd\Facades\DataForm'  );
            $loader->alias('DataEdit'  , 'Zofe\Rapyd\Facades\DataEdit'  );
            $loader->alias('DataFilter', 'Zofe\Rapyd\Facades\DataFilter');
            $loader->alias('Documenter', 'Zofe\Rapyd\Facades\Documenter');
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        //return array('dataset', 'datagrid');
        return [];
    }

    /**
     * Create a Rapyd container and bind all needed services
     *
     * @param  Container $app
     * @return Container
     */
    public static function make($app = null)
    {
        if (!$app) {
            $app = new Container();
        }
        
        //bind 'html' and 'form' from  Illuminate/html if not already binded 
        $app->bindIf('html', function($app)
        {
            return new HtmlBuilder($app['url']);
        });

        $app->bindIf('form', function($app)
        {
            $form = new FormBuilder($app['html'], $app['url'], $app['session.store']->getToken());
            return $form->setSessionStore($app['session.store']);
        });
        
        Rapyd::setContainer($app);
        
        return $app;
    }
}