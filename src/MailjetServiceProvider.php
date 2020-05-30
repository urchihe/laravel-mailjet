<?php

namespace Urchihe\LaravelMailjet;

use Illuminate\Support\ServiceProvider;
use Urchihe\LaravelMailjet\Services\MailjetService;

class MailjetServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Mailjet', function ($app) {
            $configs = $app->make('config');
            $config = array();
            $options = array();
            $config = $configs->get('services.mailjet', array());
            $call = $configs->get('services.mailjet.transactionnal.call', true);
            $options = $configs->get('services.mailjet.transactionnal.options', array());
            return new MailjetService($config['key'], $config['secret'], $call, $options);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    public function provides()
    {
        return ['mailjet'];
    }
}
