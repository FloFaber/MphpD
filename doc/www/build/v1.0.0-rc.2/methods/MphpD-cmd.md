title: MphpD::cmd
tags: method,MphpD

---

<div class="method">
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


</div>