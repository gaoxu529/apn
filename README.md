## Laravel APN (Apple Push) Notification Channel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/laravel-notification-channels/apn.svg?style=flat-square)](https://packagist.org/packages/laravel-notification-channels/apn)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/laravel-notification-channels/apn/master.svg?style=flat-square)](https://travis-ci.org/laravel-notification-channels/apn)
[![StyleCI](https://styleci.io/repos/66449499/shield)](https://styleci.io/repos/:style_ci_id)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/c0cd30d0-1013-4ced-a4b5-65e0dc87832e.svg?style=flat-square)](https://insight.sensiolabs.com/projects/c0cd30d0-1013-4ced-a4b5-65e0dc87832e)
[![Quality Score](https://img.shields.io/scrutinizer/g/laravel-notification-channels/apn.svg?style=flat-square)](https://scrutinizer-ci.com/g/laravel-notification-channels/apn)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/laravel-notification-channels/apn/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/laravel-notification-channels/apn/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/laravel-notification-channels/apn.svg?style=flat-square)](https://packagist.org/packages/laravel-notification-channels/apn)

This package makes it easy to send notifications using Apple Push (APN) with Laravel.

## Contents

- [Installation](#installation)
	- [Setting up the APN service](#setting-up-the-apn-service)
- [Usage](#usage)
	- [Available Message methods](#available-message-methods)
- [Changelog](#changelog)
- [Testing](#testing)
- [Security](#security)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)


## Installation

Install this package with Composer:

    composer require laravel-notification-channels/apn

If you're using Laravel 5.x you'll also need to specify a version constraint:

    composer require laravel-notification-channels/apn -v 0.4.0

### Setting up the APN service

Before using the APN Service, [enable Push Notifications in your app](https://help.apple.com/xcode/mac/current/#/devdfd3d04a1). Then, [generate a p12 certificate](https://help.apple.com/developer-account/#/dev82a71386a) and convert it into a .pem file so that we can use it with this library:

* Run this command to convert your p12 file to a pem file:

  `$ openssl pkcs12 -nodes -in Push.p12 -out Push.pem` 
* Make sure your server or development machine can send outgoing traffic on port 2195 and on port 2196 (for the feedback service, see below)
* Run this command to make sure your certificates are valid:

  `$ openssl s_client -cert Push.pem -connect gateway.push.apple.com:2195`

  (use gateway.sandbox.push.apple.com if you have a dev push certificate)
* If the certificates are valid, the server should not hang up

You need to successfully complete the above before you can use this channel. Once you have done so, you can configure the path to the Push.pem certificate in `config/broadcasting.php`

```php
    'connections' => [

      'apn' => [
          'environment' => ApnChannel::PRODUCTION, // Or ApnChannel::SANDBOX
          'certificate' => '/path/to/certificate.pem', 
          'pass_phrase' => null, // Optional passPhrase
      ],

    ]
```

## Usage

You can now send messages to APN by creating a ApnMessage:

```php
use NotificationChannels\Apn\ApnChannel;
use NotificationChannels\Apn\ApnMessage;
use Illuminate\Notifications\Notification;

class AccountApproved extends Notification
{
    public function via($notifiable)
    {
        return [ApnChannel::class]; // Make sure you use 'ApnChannel::class', not just 'apn'
    }

    public function toApn($notifiable)
    {
        return ApnMessage::create()
            ->badge(1)
            ->title('Account approved')
            ->body("Your {$notifiable->service} account was approved!");
    }
}
```

In your `notifiable` model, make sure to include a `routeNotificationForApn()` method, which return one or an array of tokens.

```php
public function routeNotificationForApn()
{
    return $this->apn_token;
}
```

### Available methods

 - `title($str)`
 - `body($str)`
 - `badge($integer)`
 - `custom($customData)`

### Feedback Service

Apple implements a Feedback Service. See the [Zend APN documentation](https://framework.zend.com/manual/2.2/en/modules/zendservice.apple.apns.html#feedback-service)

> APNS has a feedback service that you must listen to. Apple states that they monitor providers to ensure that they are listening to this service.
> 
> The feedback service simply returns an array of Feedback responses. All tokens provided in the feedback should not be sent to again; unless the device re-registers for push notification. You can use the time in the Feedback response to ensure that the device has not re-registered for push notifications since the last send.

One way to use the Feedback Service is by [using Laravel's task scheduling functionality](https://laravel.com/docs/scheduling). All tokens returned from the feedback service should be removed from the system and should not be sent again. 

```php
use App\User;
use NotificationChannels\Apn\FeedbackService;
use NotificationChannels\Apn\ApnFeedback;

$feedbackService = app(FeedbackService::class);

/** @var ApnFeedback $feedback */
foreach ($feedbackService->get() as $feedback) {
    User::where('apn_token', $feedback->token)
        ->update(['apn_token' => null]);
}
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Security

If you discover any security related issues, please email info@fruitcake.nl instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Fruitcake](https://github.com/fruitcake)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
