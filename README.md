# MphpD
A fully-featured, dependency-free PHP library for MPD

# What is it?
MphpD is a library for the Music Player Daemon. It lets you control MPD from within your PHP application in a simple and object-oriented way while also taking care of escaping, parsing, error reporting and all the other painful stuff.

Did I mention the whole MPD [protocol](https://mpd.readthedocs.io/en/latest/protocol.html) is fully supported?

## Installation
You can either install this library by using composer:
```
composer require flofaber/mphpd
```
or by simply [downloading](https://github.com/FloFaber/mphpd/releases) it and including it in your code like so:
```PHP
require_once __DIR__ . "/mphpd/mphpd.php";
```

## How to use

This library is simple to use.

```PHP
$options = [
  "host" => "127.0.0.1",
  "port" => 6600,
  "errormode" => MPD_ERRORMODE_EXCEPTION,
];

// create new mphpd instance
$mpd = new MphpD($options);

// try connecting to MPD.
try{
  $mpd->connect();
  echo "connected!\n";
}catch(MPDException $e){
  echo $e."\n";
  return false;
}

// get MPD's status like current song, volume, state, etc...
$status = $mpd->status();

// if you only want to retrieve only one (or more) values
// you can pass it a list of keys.
$state = $mpd->status([ "state", "playlist" ]);

// clear the queue
$mpd->queue()->clear();

// load the first 10 songs of a playlist into the queue
$mpd->playlist("some-playlist")->load([0,10]);

// shuffle the queue
$mpd->queue()->shuffle();

// adjust volume to 40%
$mpd->player()->volume(40);

// start playing
$mpd->player()->play();
```

See the full documentation on [mphpd.org/doc](https://mphpd.org/doc) for more.


## Required PHP extensions
* Only `sockets` which is included by default on most PHP installations.

## Required PHP functions

A list of PHP functions required by MphpD for socket communication:
 
* fgets
* fputs
* fread
* stream_get_meta_data
* stream_set_chunk_size
* stream_set_timeout
* stream_socket_client
