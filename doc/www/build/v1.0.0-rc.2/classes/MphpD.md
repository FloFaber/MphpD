title: FloFaber\MphpD\MphpD
tags: class

---

<h1 class="method-name">FloFaber\MphpD\MphpD</h1>
<p>The Main MphpD class.<br></p>

```php
new MphpD(array $config = []) : MphpD
```

## Methods

<div class="method">
<h3 class="method-name">__construct</h3>
<p><br></p>

```php
MphpD::__construct(array $options = Array)
```

#### Parameters

*  array $options Array of options. [Documentation](../guides/configuration)


#### Returns ``



</div><div class="method">
<h3 class="method-name">db</h3>
<p>Return the DB instance<br></p>

```php
MphpD::db()
```

#### Parameters

*none*


#### Returns ``

\DB


</div><div class="method">
<h3 class="method-name">player</h3>
<p>Return the Player instance<br></p>

```php
MphpD::player()
```

#### Parameters

*none*


#### Returns ``

\Player


</div><div class="method">
<h3 class="method-name">queue</h3>
<p>Return the Queue instance<br></p>

```php
MphpD::queue()
```

#### Parameters

*none*


#### Returns ``

\Queue


</div><div class="method">
<h3 class="method-name">playlist</h3>
<p>Returns a Playlist instance with the given name or null if the name is empty<br></p>

```php
MphpD::playlist(string $name)
```

#### Parameters

*  string $name The name of the playlist. Must not be empty.


#### Returns ``

\Playlist|null


</div><div class="method">
<h3 class="method-name">playlists</h3>
<p>If $metadata is set to `true` an Array of associative arrays containing information about the playlists will be returned.<br>If $metadata is omitted or set to `false` a list containing all playlists names is returned.</p>

```php
MphpD::playlists(bool $metadata = ) : array|false
```

#### Parameters

*  bool $metadata Include/Exclude additional information like "last-modified",...


#### Returns `array|false`




</div><div class="method">
<h3 class="method-name">output</h3>
<p>Return a new output instance<br></p>

```php
MphpD::output(int $id)
```

#### Parameters

*  int $id The ID of the output


#### Returns ``

\Output


</div><div class="method">
<h3 class="method-name">outputs</h3>
<p>Returns an Array of associative arrays of all available outputs<br></p>

```php
MphpD::outputs() : array|false
```

#### Parameters

*none*


#### Returns `array|false`




</div><div class="method">
<h3 class="method-name">neighbors</h3>
<p>Return neighbors on the network like available SMB servers<br></p>

```php
MphpD::neighbors() : array|false
```

#### Parameters

*none*


#### Returns `array|false`




</div><div class="method">
<h3 class="method-name">partition</h3>
<p>Return a new Partition instance<br></p>

```php
MphpD::partition(string $name)
```

#### Parameters

*  string $name The name of the partition


#### Returns ``

\Partition


</div><div class="method">
<h3 class="method-name">partitions</h3>
<p>Return a list of all available partitions<br></p>

```php
MphpD::partitions() : array|false
```

#### Parameters

*none*


#### Returns `array|false`




</div><div class="method">
<h3 class="method-name">channel</h3>
<p>Return a new Channel instance<br></p>

```php
MphpD::channel(string $name = '')
```

#### Parameters

*  string $name The name of the channel


#### Returns ``

\Channel


</div><div class="method">
<h3 class="method-name">channels</h3>
<p>Return a list of available channels<br></p>

```php
MphpD::channels()
```

#### Parameters

*none*


#### Returns ``



</div><div class="method">
<h3 class="method-name">clear_error</h3>
<p>Clears the current error<br></p>

```php
MphpD::clear_error() : bool
```

#### Parameters

*none*


#### Returns `bool`




</div><div class="method">
<h3 class="method-name">status</h3>
<p>Returns the value of the specified key(s) from MPD's status.<br></p>

```php
MphpD::status(array $items = Array) : array|false|int|float|null
```

#### Parameters

*  array $items Optional. Array containing the wanted key(s) like `status`, `songid`,...

If only one item is given only it's value will be returned instead of an associative array.

If the given item(s) do not exist `null` will be set as their value.

If omitted, an associative array containing all status information will be returned.


#### Returns `array|false|int|float|null`

Returns
`false` on error

`string`, `int`, `float` or `null` if `$items` contains only one item. If it does not exist `null` will be returned instead.

Otherwise, an associative array containing all available (or specified) keys.


</div><div class="method">
<h3 class="method-name">stats</h3>
<p>Returns the value of the specified key from MPD's stats.<br></p>

```php
MphpD::stats(array $items = Array) : array|false|int|null
```

#### Parameters

*  array $items Optional. Array containing the wanted stat(s). Example: `[ "artists", "uptime", "playtime" ]`

 If only one item is given only it's value will be returned instead of an associative array.

If the given item(s) do not exist `null` will be set as their value.

If omitted, an associative array containing all stats will be returned.


#### Returns `array|false|int|null`

Returns
`false` on error

`string`, `int` or `null` if $items contains only one item. If it does not exist `null` will be returned instead.

Otherwise, an associative array containing all available (or specified) stats.


</div><div class="method">
<h3 class="method-name">sticker</h3>
<p>Returns a Sticker instance<br></p>

```php
MphpD::sticker(string $type, string $uri)
```

#### Parameters

*  string $type
*  string $uri


#### Returns ``

\Sticker


</div><div class="method">
<h3 class="method-name">mounts</h3>
<p>Return all mounts.<br></p>

```php
MphpD::mounts() : array|false
```

#### Parameters

*none*


#### Returns `array|false`




</div><div class="method">
<h3 class="method-name">mount</h3>
<p>Mount $uri to path<br></p>

```php
MphpD::mount(string $path, string $uri) : bool
```

#### Parameters

*  string $path
*  string $uri The URI to mount


#### Returns `bool`




</div><div class="method">
<h3 class="method-name">unmount</h3>
<p>Unmount the path<br></p>

```php
MphpD::unmount(string $path) : bool
```

#### Parameters

*  string $path


#### Returns `bool`




</div><div class="method">
<h3 class="method-name">bulk_start</h3>
<p>Function to start a command-list.<br></p>

```php
MphpD::bulk_start() : void
```

#### Parameters

*none*


#### Returns `void`




</div><div class="method">
<h3 class="method-name">bulk_end</h3>
<p>Function to end a command-list and execute its commands
The command list is stopped in case an error occurs.<br></p>

```php
MphpD::bulk_end() : array|false
```

#### Parameters

*none*


#### Returns `array|false`

Returns an array containing the commands responses.


</div><div class="method">
<h3 class="method-name">bulk_abort</h3>
<p>Function to abort the current command list.<br>We can do that because we only start the list at protocol level when bulk_end() is called.</p>

```php
MphpD::bulk_abort() : void
```

#### Parameters

*none*


#### Returns `void`




</div><div class="method">
<h3 class="method-name">bulk_add</h3>
<p>Function to add a command to the bulk_list.<br></p>

```php
MphpD::bulk_add(string $cmd, array $params = Array, int $mode = 32) : bool
```

#### Parameters

*  string $cmd
*  array $params
*  int $mode


#### Returns `bool`




</div><div class="method">
<h3 class="method-name">tagtypes</h3>
<p>Return a list of all available tag types.<br></p>

```php
MphpD::tagtypes() : array|false
```

#### Parameters

*none*


#### Returns `array|false`




</div><div class="method">
<h3 class="method-name">tagtypes_disable</h3>
<p>Disable specified tag types.<br></p>

```php
MphpD::tagtypes_disable(array $tagtypes) : bool
```

#### Parameters

*  array $tagtypes A list of tag types to disable.


#### Returns `bool`




</div><div class="method">
<h3 class="method-name">tagtypes_enable</h3>
<p>Enable specified tag types.<br></p>

```php
MphpD::tagtypes_enable(array $tagtypes) : bool
```

#### Parameters

*  array $tagtypes A list of tag types to enable.


#### Returns `bool`




</div><div class="method">
<h3 class="method-name">tagtypes_clear</h3>
<p>Remove all tag types from responses.<br></p>

```php
MphpD::tagtypes_clear() : bool
```

#### Parameters

*none*


#### Returns `bool`




</div><div class="method">
<h3 class="method-name">tagtypes_all</h3>
<p>Enable all available tag types.<br></p>

```php
MphpD::tagtypes_all() : bool
```

#### Parameters

*none*


#### Returns `bool`




</div><div class="method">
<h3 class="method-name">ping</h3>
<p>Ping.<br></p>

```php
MphpD::ping() : bool
```

#### Parameters

*none*


#### Returns `bool`




</div><div class="method">
<h3 class="method-name">config</h3>
<p>Returns an associative array of configuration values.<br>This function is only available for client connected via Unix Socket!</p>

```php
MphpD::config() : array|false
```

#### Parameters

*none*


#### Returns `array|false`




</div><div class="method">
<h3 class="method-name">commands</h3>
<p>Returns a list of all available commands.<br></p>

```php
MphpD::commands() : array|false
```

#### Parameters

*none*


#### Returns `array|false`




</div><div class="method">
<h3 class="method-name">notcommands</h3>
<p>Returns a list of all not-available commands.<br></p>

```php
MphpD::notcommands() : array|false
```

#### Parameters

*none*


#### Returns `array|false`




</div><div class="method">
<h3 class="method-name">urlhandlers</h3>
<p>Returns a list of all available urlhandlers. Like smb://, sftp://, http://.<br>..</p>

```php
MphpD::urlhandlers() : array|false
```

#### Parameters

*none*


#### Returns `array|false`




</div><div class="method">
<h3 class="method-name">decoders</h3>
<p>Returns a list of available decoder plugins and their supported suffixes and mimetypes.<br></p>

```php
MphpD::decoders() : array|false
```

#### Parameters

*none*


#### Returns `array|false`




</div><div class="method">
<h3 class="method-name">cmd</h3>
<p>Send $command with $params to the MPD server.<br>You, the library's user, are not intended to ever
need this method. If you ever need it because the library does not support
a specific command please file a [bug report](https://github.com/FloFaber/MphpD/issues).
This method also parses MPDs response depending on the chosen mode.</p>

```php
MphpD::cmd(string $command, array $params = Array, int $mode = 2, array $list_start = Array) : array|bool
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
MphpD::get_socket()
```

#### Parameters

*none*


#### Returns ``



</div><div class="method">
<h3 class="method-name">get_version</h3>
<p>Returns MPDs version as string<br></p>

```php
MphpD::get_version() : string
```

#### Parameters

*none*


#### Returns `string`




</div><div class="method">
<h3 class="method-name">version_bte</h3>
<p>Function to compare a given version string with the current version of MPD<br></p>

```php
MphpD::version_bte(string $version) : bool
```

#### Parameters

*  string $version


#### Returns `bool`

Returns true if MPDs version is equal to or newer than the given version. False otherwise.


</div><div class="method">
<h3 class="method-name">idle</h3>
<p>Waits until there is a noteworthy change in one or more of MPD’s subsystems.<br></p>

```php
MphpD::idle(string $subsystem = '', int $timeout = 60) : array|false
```

#### Parameters

*  string $subsystem
*  int $timeout Specifies how long to wait for MPD to return an answer.


#### Returns `array|false`

Returns an array of changed subsystems or false on timeout.


</div><div class="method">
<h3 class="method-name">close</h3>
<p>Close the connection to the MPD socket<br></p>

```php
MphpD::close() : void
```

#### Parameters

*none*


#### Returns `void`




</div><div class="method">
<h3 class="method-name">kill</h3>
<p>Kill MPD.<br></p>

```php
MphpD::kill() : void
```

#### Parameters

*none*


#### Returns `void`




</div><div class="method">
<h3 class="method-name">get_binarylimit</h3>
<p>Returns the current binarylimit<br></p>

```php
MphpD::get_binarylimit() : int
```

#### Parameters

*none*


#### Returns `int`




</div><div class="method">
<h3 class="method-name">set_error</h3>
<p>Function to set the last occurred error.<br>Should only be used inside the library!</p>

```php
MphpD::set_error( $err) : false
```

#### Parameters

*  \MPDException|string $err


#### Returns `false`




</div><div class="method">
<h3 class="method-name">get_last_error</h3>
<p>Return an array containing information about the last error<br></p>

```php
MphpD::get_last_error() : array
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
<p>Initiate connection to MPD with the parameters given at instantiation.<br></p>

```php
MphpD::connect() : bool
```

#### Parameters

*none*


#### Returns `bool`




</div><div class="method">
<h3 class="method-name">disconnect</h3>
<p>Disconnect from MPD<br></p>

```php
MphpD::disconnect() : void
```

#### Parameters

*none*


#### Returns `void`




</div>