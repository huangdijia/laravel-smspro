# SMS gateway for smspro

[![Latest Stable Version](https://poser.pugx.org/huangdijia/laravel-smspro/version.png)](https://packagist.org/packages/huangdijia/laravel-smspro)
[![Total Downloads](https://poser.pugx.org/huangdijia/laravel-smspro/d/total.png)](https://packagist.org/packages/huangdijia/laravel-smspro)

## Requirements

* PHP >= 7.0
* Laravel >= 5.5

## Installation

```bash
composer require huangdijia/laravel-smspro
```

## Publish

```bash
php artisan vendor:publish --provider="Huangdijia\Smspro\SmsproServiceProvider"
```

## Configure

```env
SMSPRO_USERNAME=account
SMSPRO_PASSWORD=password
SMSPRO_SENDER=sender
```

## Usage

### Command

```bash
php artisan smspro:send [mobile] [message]
```

### Facade

```php
Huangdijia\Smspro\Facades\Smspro::send($mobile, $message);
```

### Container

```php
app('sms.smspro')->send($mobile, $message);
```

## Error

```php
$smspro = app('sms.smspro');

if (!$smspro->send($mobile, $message)) {
    dd($smspro->getError());
}
```