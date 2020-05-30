# Laravel Mailjet

Laravel package for handling Mailjet API v3 using this wrapper: <https://github.com/mailjet/mailjet-apiv3-php>

It also provides a mailjetTransport for [Laravel mail feature](https://laravel.com/docs/master/mail)

## Installation

First, include the package in your dependencies:

    composer require urchihe/laravel-mailjet

Then, you need to add some informations in your configuration files. You can find your Mailjet API key/secret [here](https://app.mailjet.com/account/api_keys)



* In the services.php file:

```php
'mailjet' => [
    'key' => env('MAILJET_APIKEY'),
    'secret' => env('MAILJET_APISECRET'),
    'transactional' => [
        'call' => true,
        'options' => [
            'url' => 'api.mailjet.com',
            'version' => 'v3.1',
            'call' => true,
            'secured' => true
        ]
    ],
    'common' => [
        'call' => true,
        'options' => [
            'url' => 'api.mailjet.com',
            'version' => 'v3',
            'call' => true,
            'secured' => true
        ]
    ]
]
```

* In your .env file:

```php
MAILJET_APIKEY=YOUR_APIKEY
MAILJET_APISECRET=YOUR_APISECRET
```

## Full configuration

```php
'mailjet' => [
    'key' => env('MAILJET_APIKEY'),
    'secret' => env('MAILJET_APISECRET'),
    'transactional' => [
        'call' => true,
        'options' => [
            'url' => 'api.mailjet.com',
            'version' => 'v3.1',
            'call' => true,
            'secured' => true
        ]
    ],
    'common' => [
        'call' => true,
        'options' => [
            'url' => 'api.mailjet.com',
            'version' => 'v3',
            'call' => true,
            'secured' => true
        ]
    ]
]
```
You can pass settings to [MailjetClient](https://github.com/mailjet/mailjet-apiv3-php#new--version-120-of-the-php-wrapper-).

* `transactional`: settings to sendAPI client
* `common`: setting to MailjetClient accessible throught the Facade Mailjet

## Mail driver configuration

In order to use Mailjet as your Mail driver, you need to update the mail driver in your `config/mail.php` or your `.env` file to `MAIL_MAILER=mailjet`, `MAIL_HOST=in-v3.mailjet.com`, `MAIL_PORT=2525` and to the `mailers` array in `config/mail` add 
```php  
'mailjet' => [
            'transport' => 'mailjet',
            'host' => env('MAIL_HOST'),
            'port' => env('MAIL_PORT'),
            'encryption' => env('MAIL_ENCRYPTION', 'tls'),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => null,
        ],  
``` 
.

For usage, please check the [Laravel mail documentation](https://laravel.com/docs/master/mail)

## Usage

In order to usage this package, you first need to import Mailjet Facade in your code:

    use Urchihe\LaravelMailjet\Facades\Mailjet;


Then, in your code you can use one of the methods available in the MailjetServices.

Low level API methods:

* `Mailjet::get($resource, $args, $options)`
* `Mailjet::post($resource, $args, $options)`
* `Mailjet::put($resource, $args, $options)`
* `Mailjet::delete($resource, $args, $options)`

High level API methods:

* `Mailjet::getAllLists($filters)`
* `Mailjet::createList($body)`
* `Mailjet::getListRecipients($filters)`
* `Mailjet::getSingleContact($id)`
* `Mailjet::createContact($body)`
* `Mailjet::createListRecipient($body)`
* `Mailjet::editListrecipient($id, $body)`

For more informations about the filters you can use in each methods, refer to the [Mailjet API documentation](https://dev.mailjet.com/email-api/v3/apikey/)

All method return `Mailjet\Response` or throw a `MailjetException` in case of API error.

You can also get the Mailjet API client with the method `getClient()` and make your own custom request to Mailjet API.

## ToDo

* Add additional unit tests to increase code coverage.
