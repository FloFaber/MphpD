title: FloFaber\MphpD\Socket
tags: class

---

<h1 class="method-name">FloFaber\MphpD\Socket</h1>
<p></p>

```php

```

## Methods

<div class="method">
<h3 class="method-name">__construct</h3>
<p></p>

```php
Socket::__construct(array $options = Array)
```

#### Parameters

*none*


#### Returns ``



</div><div class="method">
<h3 class="method-name">cmd</h3>
<p>Send $command with $params to the MPD server.</p>

```php
Socket::cmd(string $command, array $params = Array, int $mode = 2, array $list_start = Array) : array|bool
```

#### Parameters

*  string $command The command
*  array $params Parameters, automatically escaped
*  int $mode One of the following constants:

* MPD_CMD_READ_NONE        - Do not read anything from the answer. Returns an empty array.

* MPD_CMD_READ_NORMAL      - Parses the answer as a one-dimensional "key=>value" array.
                             If a key already existed its value gets overwritten.
                             Used for commands like "status" where only unique keys are given.

* MPD_CMD_READ_LIST        - Parses the answer as a list of "key=>value" arrays.
                             Used for commands like "listplaylists" where keys are not unique.

* MPD_CMD_READ_LIST_SINGLE - Parses the answer into a simple "indexed" array.
                             Used for commands like "idle" where there is
                             only a single possible "key".

* MPD_CMD_READ_BOOL        - Parses the answer into `true` on OK and list_OK and `false` on `ACK`.
                             Used for commands which do not return anything but OK or ACK.
*  array $list_start In combination with `$mode = MPD_CMD_READ_LIST` indicates on which `key` a new list starts.


#### Returns `array|bool`

False on failure.
Array on success.
True on success if $mode is MPD_CMD_READ_BOOL


</div><div class="method">
<h3 class="method-name">get_socket</h3>
<p></p>

```php
Socket::get_socket()
```

#### Parameters

*none*


#### Returns ``



</div><div class="method">
<h3 class="method-name">get_version</h3>
<p>Returns MPDs version as string</p>

```php
Socket::get_version() : string
```

#### Parameters

*none*


#### Returns `string`




</div><div class="method">
<h3 class="method-name">version_bte</h3>
<p>Function to compare a given version string with the current version of MPD</p>

```php
Socket::version_bte(string $version) : bool
```

#### Parameters

*  string $version


#### Returns `bool`

Returns true if MPDs version is equal to or newer than the given version. False otherwise.


</div><div class="method">
<h3 class="method-name">idle</h3>
<p>Waits until there is a noteworthy change in one or more of MPD’s subsystems.</p>

```php
Socket::idle(string $subsystem = '', int $timeout = 60) : array|false
```

#### Parameters

*  string $subsystem
*  int $timeout Specifies how long to wait for MPD to return an answer.


#### Returns `array|false`

Returns an array of changed subsystems or false on timeout.


</div><div class="method">
<h3 class="method-name">close</h3>
<p>Close the connection to the MPD socket</p>

```php
Socket::close() : void
```

#### Parameters

*none*


#### Returns `void`




</div><div class="method">
<h3 class="method-name">kill</h3>
<p>Kill MPD.</p>

```php
Socket::kill() : void
```

#### Parameters

*none*


#### Returns `void`




</div><div class="method">
<h3 class="method-name">get_binarylimit</h3>
<p>Returns the current binarylimit</p>

```php
Socket::get_binarylimit() : int
```

#### Parameters

*none*


#### Returns `int`




</div><div class="method">
<h3 class="method-name">set_error</h3>
<p>Function to set the last occurred error.</p>

```php
Socket::set_error( $err) : false
```

#### Parameters

*  \MPDException|string $err


#### Returns `false`




</div><div class="method">
<h3 class="method-name">get_last_error</h3>
<p>Return an array containing information about the last error</p>

```php
Socket::get_last_error() : array
```

#### Parameters

*none*


#### Returns `array`

associative array containing the following keys:
<pre>
[
  "code" => (int),
  "message" => (string),
  "command" => (string),
  "commandlistnum" => (int)
]
</pre>


</div><div class="method">
<h3 class="method-name">connect</h3>
<p>Initiate connection to MPD with the parameters given at instantiation.</p>

```php
Socket::connect() : bool
```

#### Parameters

*none*


#### Returns `bool`




</div><div class="method">
<h3 class="method-name">disconnect</h3>
<p>Disconnect from MPD</p>

```php
Socket::disconnect() : void
```

#### Parameters

*none*


#### Returns `void`




</div>