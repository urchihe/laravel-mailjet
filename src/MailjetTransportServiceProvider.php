<?php


namespace Urchihe\LaravelMailjet;


use Urchihe\LaravelMailjet\Transport\MailjetTransport;
use Illuminate\Mail\MailManager;
use Illuminate\Support\ServiceProvider;
use Swift_Events_SimpleEventDispatcher;

class MailjetTransportServiceProvider extends ServiceProvider
{
    public function register(){
        $this->app->afterResolving(MailManager::class, function (MailManager $mailManager) {
            $mailManager->extend("mailjet", function () {
                $configs = $this->app->make('config');
                $config = $configs->get('services.mailjet', array());
                $call = $configs->get('services.mailjet.transactionnal.call', true);
                $options = $configs->get('services.mailjet.transactionnal.options', array());
                return new MailjetTransport(
                    new Swift_Events_SimpleEventDispatcher(),
                    $config['key'],
                    $config['secret'],
                    $call,
                    $options
                );
            });
        });
    }

}
