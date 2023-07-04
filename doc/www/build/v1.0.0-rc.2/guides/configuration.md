title: Configuration
date: 2022-01-01

---

# Configuration


You can pass the following settings to MphpD when initializing it:
```php
use FloFaber\MphpD\MphpD;
$mphpd = new MphpD([
  "host" => "127.0.0.1",
  "port" => 6600,
  "timeout" => 5,
  "password" => "beetlejuice",
  "binarylimit" => 8192
]);
```

All of those are optional. If one is not specified the default value is used. See below.


## Host

`String`. Specifies the IP to which MphpD should connect.

If prefixed with `unix:` the path to MPD's Unix socket. Example: `unix:/var/run/mpd.sock`

Default: `127.0.0.1`


## Port

`Int`. The port on which MPD is listening.

Default: `6600`


## Timeout

`Int`. Defines the connect-, read- and write-timeout in seconds.

Default: `1`


## Password

`String`. The plaintext password used for authentication on MPD.

Default: *empty string*

## Binarylimit

`Int`. The maximum number of bytes a binary chunk returned by MPD can have.

A bigger value means less overhead. Don't change it if you don't experience any problems with the default value.

Only supported on MPD v0.22.4 and newer. It's simply ignored and set to 8192 on older versions.

Default: `8192`
