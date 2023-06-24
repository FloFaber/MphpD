title: FloFaber\MphpD\Player
tags: class

---

<h1 class="method-name">FloFaber\MphpD\Player</h1>
<p>You may also want to have a look at the [MPD documentation](https://mpd.readthedocs.io/en/latest/protocol.html#playback-options).</p>

```php
MphpD::player() : Player
```

## Methods

<div class="method">
<h3 class="method-name">__construct</h3>
<p>This class is not intended for direct usage.</p>

```php
Player::__construct(FloFaber\MphpD\MphpD $mphpd)
```

#### Parameters

*  \MphpD $mphpd


#### Returns ``



</div><div class="method">
<h3 class="method-name">consume</h3>
<p>Enables/Disables the consume mode</p>

```php
Player::consume(int $state) : bool
```

#### Parameters

*  int $state One of the following:

* `MPD_STATE_ON` - Enables consume mode

* `MPD_STATE_OFF` - Disables consume mode

* `MPD_STATE_ONESHOT` - Enables consume mode for a single song.
                      This is only supported on MPD version 0.24 and newer.


#### Returns `bool`

Returns true on success and false on failure


</div><div class="method">
<h3 class="method-name">crossfade</h3>
<p>Sets crossfade to `$seconds` seconds.</p>

```php
Player::crossfade(int $seconds) : bool
```

#### Parameters

*  int $seconds


#### Returns `bool`

Returns true on success and false on failure


</div><div class="method">
<h3 class="method-name">mixramp_db</h3>
<p>Sets the threshold at which songs will be overlapped.</p>

```php
Player::mixramp_db(int $dB) : bool
```

#### Parameters

*  int $dB


#### Returns `bool`

Returns true on success and false on failure.


</div><div class="method">
<h3 class="method-name">mixramp_delay</h3>
<p></p>

```php
Player::mixramp_delay(float $seconds) : bool
```

#### Parameters

*  float $seconds


#### Returns `bool`

Returns true on success and false on failure


</div><div class="method">
<h3 class="method-name">random</h3>
<p>Specified if MPD should play the queue in random order</p>

```php
Player::random(int $state) : bool
```

#### Parameters

*  int $state Either `MPD_STATE_OFF` or `MPD_STATE_ON`


#### Returns `bool`

Returns true on success and false on failure.


</div><div class="method">
<h3 class="method-name">repeat</h3>
<p>Specifies if MPD should start from the top again when reaching the end of the queue.</p>

```php
Player::repeat(int $state) : bool
```

#### Parameters

*  int $state Either `MPD_STATE_OFF` or `MPD_STATE_ON`


#### Returns `bool`

Returns true on success and false on failure.


</div><div class="method">
<h3 class="method-name">volume</h3>
<p>Sets volume to `$volume` or returns the current volume if `$volume` is omitted.</p>

```php
Player::volume(int $volume = -1) : int|bool
```

#### Parameters

*  int $volume If specified the current volume is set to $volume.

If omitted the current volume is returned.


#### Returns `int|bool`

Returns `true` on success, `false` on failure and `int` if $volume was omitted.


</div><div class="method">
<h3 class="method-name">single</h3>
<p>Enables/Disables the single-mode. If enabled MPD will play the same song over and over.</p>

```php
Player::single(int $state) : bool
```

#### Parameters

*  int $state One of the following:

* `MPD_STATE_ON` - Enables single mode

* `MPD_STATE_OFF` - Disables single mode

* `MPD_STATE_ONESHOT` - Enables single mode for only a single time.
  This is only supported on MPD version 0.21 and newer.


#### Returns `bool`




</div><div class="method">
<h3 class="method-name">replay_gain_mode</h3>
<p>Specifies whether MPD shall adjust the volume of songs played using ReplayGain tags.</p>

```php
Player::replay_gain_mode(string $mode) : bool
```

#### Parameters

*  string $mode One of `off`, `track`, `album`, `auto`


#### Returns `bool`

Returns true on success and false on failure.


</div><div class="method">
<h3 class="method-name">replay_gain_status</h3>
<p>Get the current replay gain</p>

```php
Player::replay_gain_status() : array|false
```

#### Parameters

*none*


#### Returns `array|false`




</div><div class="method">
<h3 class="method-name">current_song</h3>
<p>Returns an associative array containing information about the currently playing song.</p>

```php
Player::current_song() : array|false
```

#### Parameters

*none*


#### Returns `array|false`




</div><div class="method">
<h3 class="method-name">next</h3>
<p>Plays the next song in the Queue</p>

```php
Player::next() : bool
```

#### Parameters

*none*


#### Returns `bool`

Returns true on success and false on failure


</div><div class="method">
<h3 class="method-name">pause</h3>
<p>Pause or resume playback.</p>

```php
Player::pause(?int $state = ) : bool
```

#### Parameters

*  int|null $state Optional. One of the following:

* `MPD_STATE_ON` - Pause

* `MPD_STATE_OFF` - Resume

If omitted or `null` the pause state is toggled.


#### Returns `bool`

Returns true on success and false on failure


</div><div class="method">
<h3 class="method-name">play</h3>
<p>Plays the song position `$pos` in the Queue</p>

```php
Player::play(int $pos) : bool
```

#### Parameters

*  int $pos Song position. Starting at 0


#### Returns `bool`

Returns true on success and false on failure


</div><div class="method">
<h3 class="method-name">play_id</h3>
<p>Begins playing the playlist at song `$id`</p>

```php
Player::play_id(int $id) : bool
```

#### Parameters

*  int $id


#### Returns `bool`

Returns true on success and false on failure


</div><div class="method">
<h3 class="method-name">previous</h3>
<p>Plays the previous song in the Queue</p>

```php
Player::previous() : bool
```

#### Parameters

*none*


#### Returns `bool`



</div><div class="method">
<h3 class="method-name">seek</h3>
<p>Seeks to `$seconds` of song `$songpos` in the Queue.</p>

```php
Player::seek(int $songpos, float $time) : bool
```

#### Parameters

*  int $songpos
*  float $time


#### Returns `bool`

Returns true on success and false on failure


</div><div class="method">
<h3 class="method-name">seek_id</h3>
<p>Seeks to `$seconds` of song `$songid`</p>

```php
Player::seek_id(int $songid, float $time) : bool
```

#### Parameters

*  int $songid
*  float $time


#### Returns `bool`

Returns true on success and false on failure


</div><div class="method">
<h3 class="method-name">seek_cur</h3>
<p>Seeks to `$seconds` of the current song.</p>

```php
Player::seek_cur( $time) : bool
```

#### Parameters

*  string|int|float $time If prefixed with `+` or `-` the time is relative to the current playing position.


#### Returns `bool`

Returns true on success and false on failure


</div><div class="method">
<h3 class="method-name">stop</h3>
<p>Stops playing.</p>

```php
Player::stop() : bool
```

#### Parameters

*none*


#### Returns `bool`

Returns true on success and false on failure


</div>