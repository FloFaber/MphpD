# A fully-featured, dependency-free PHP library for MPD

MphpD is a library for the Music Player Daemon.
It lets you control MPD from within your PHP application in a simple and object-oriented
way while also taking care of escaping, parsing, error reporting and all the other
painful stuff.


The whole MPD [protocol](https://mpd.readthedocs.io/en/latest/protocol.html) is supported!



## Installation


You can either install this library by using composer:
```
composer require flofaber/mphpd
```
and then autoload it:
```PHP
require_once __DIR__ . "/vendor/autoload.php";
```

or by simply [downloading](https://github.com/FloFaber/MphpD/releases) it and including it in your code like so:
```PHP
require_once __DIR__ . "/MphpD/MphpD.php";
```

## Usage

Create a new MphpD instance:

```PHP
use FloFaber\MphpD\MphpD;
use FloFaber\MphpD\MPDException;

$mphpd = new MphpD([
  "host" => "127.0.0.1",
  "port" => 6600,
  "timeout" => 5
]);
```

and connect to MPD
```PHP
try{
  $mphpd->connect();
}catch (MPDException $e){
  echo $e->getMessage();
  return false;
}
```

## Example

Here are some examples of what you can do with it:

```PHP
// get MPD's status like current song, volume, state, etc...
$status = $mphpd->status();

// if you only want to retrieve only one (or more) values
// you can pass it a list of keys.
$state = $mphpd->status([ "state" ]);

// clear the queue
$mphpd->queue()->clear();

// load the first 10 songs of a playlist into the queue and exit on failure.
if(!$mphpd->playlist("some-playlist")->load([0,10])){
  echo $mphpd->get_last_error()["message"]; // prints "No such playlist"
  return false;
}

// shuffle the queue
$mphpd->queue()->shuffle();

// adjust volume to 40%
$mphpd->player()->volume(40);

// start playing
$mphpd->player()->play();
```

For further information have a look at the [Documentation](https://mphpd.org/latest/overview.html).


## Required PHP extensions
* Only `sockets` which is included by default on most PHP installations.

## Required PHP functions

A list of PHP functions required by MphpD for socket communication:

* `fgets`
* `fputs`
* `fread`
* `stream_get_meta_data`
* `stream_set_chunk_size`
* `stream_set_timeout`
* `stream_socket_client`
