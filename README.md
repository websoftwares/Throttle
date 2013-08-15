# Throttle (v0.3.2)
Ban identifier after certain amount of requests in a given timeframe.

[![Build Status](https://api.travis-ci.org/websoftwares/Throttle.png)](https://travis-ci.org/websoftwares/Throttle)

## Installing via Composer (recommended)

Install composer in your project:
```
curl -s http://getcomposer.org/installer | php
```

Create a composer.json file in your project root:
```
{
    "require": {
        "websoftwares/throttle": "dev-master"
    }
}
```

Install via composer
```
php composer.phar install
```

## Usage
Basic usage of the `Throttle` class to ban an identifier.

```php
use Websoftwares\Throttle, Websoftwares\Storage\Memcached, Monolog\Logger;

// Ip
$identifier = '$_SERVER["REMOTE_ADDR"]';
// Instantiate class
$throttle = new Throttle(new Logger('throttle'), new Memcached());

if($throttle->validate($identifier)) {
	// Success proceed
} else {
	// Banned
}

```

## Logger
Any logger library that implements the [PSR-3](https://github.com/php-fig/log) _LoggerInterface_ should work,
just create your Logger object and inject it into the `Throttle` constructor.
For example the excellent logging library [Monolog](https://github.com/Seldaek/monolog).

## Storage
Included is a `Memcached` example however it is very easy to use some other storage system
just implement the _StorageInterface_ and inject that object into the `Throttle` constructor.

####_Caution_####
Whatever storage system u decide to use,
don not store the failed request data into your database,
this could lead to a DDOS attack and take your database down.

## Options
U can override the default options by instantiating a `Throttle` class and pass in an _array_ as the third argument.

```php
$options = array(
	'banned' => 10, // Ban identifier after 10 attempts. (default 5)
	'logged' => 20, // Log identifier after 20 attempts. (default 10)
	'timespan' => 60 // The timespan for the duration of the ban. (default 86400)
	);

// Instantiate class
$throttle = new Throttle(new Logger('throttle'), new Memcached(), $options);

```

## Memcached
This requires u have the PHP memcached extension installed.

on Debian/Ubuntu systems for example install like this (requires administrative password).

```
sudo apt-get install php5-memcached
```

## reset();
This will remove the identifier from the storage.
```php
$throttle->reset($identifier);
```

## remaining();
This will return an integer that is the remaining attempt(s) available before identifier gets banned.
```php
$throttle->remaining($identifier);
```

## Testing
In the tests folder u can find several tests.

## License
[DBAD](http://www.dbad-license.org/ "DBAD") Public License.

## Acknowledgement
Converted from python example and comments from [Forrst.com](https://forrst.com/posts/Limiting_number_of_requests_in_a_given_timeframe-0BW "Forrst") post.