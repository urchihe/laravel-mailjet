<?php

namespace Admin\Mailjet;


use Illuminate\Mail\MailServiceProvider;


class MailjetMailServiceProvider extends MailServiceProvider
{
    /**
     * Extended register the Swift Transport instance.
     *
     * @return void
     */
    public function register()
    {
        parent::register();
        $this->app->register(MailjetTransportServiceProvider::class);
    }
}
