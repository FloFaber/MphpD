title: FloFaber\MphpD\Queue
tags: class

---

<h1 class="method-name">FloFaber\MphpD\Queue</h1>
<p></p>

```php
MphpD::queue() : Queue
```

## Methods

<div class="method">
<h3 class="method-name">__construct</h3>
<p>This class is not intended for direct usage.</p>

```php
Queue::__construct(FloFaber\MphpD\MphpD $mphpd)
```

#### Parameters

*  \MphpD $mphpd


#### Returns ``



</div><div class="method">
<h3 class="method-name">add</h3>
<p>Adds the file $uri to the queue (directories add recursively). $uri can also be a single file.</p>

```php
Queue::add(string $uri,  $pos = -1) : bool
```

#### Parameters

*  string $uri Can be a single file or folder.

If connected via Unix socket you may add arbitrary local files (absolute paths)
*  int|string $pos If set the song is inserted at the specified position.
If the parameter starts with + or -, then it is relative to the current song.
e.g. +0 inserts right after the current song and -0 inserts right before the current song (i.e. zero songs between the current song and the newly added song).


#### Returns `bool`

Returns `true` on success and `false` on failure.


</div><div class="method">
<h3 class="method-name">add_id</h3>
<p>Adds a song to the playlist (non-recursive) and returns the song id.</p>

```php
Queue::add_id(string $uri,  $pos = -1) : int|false
```

#### Parameters

*  string $uri Is always a single file or URL
*  int|string $pos If set the song is inserted at the specified position.
If the parameter starts with + or -, then it is relative to the current song.
e.g. +0 inserts right after the current song and -0 inserts right before the current song (i.e. zero songs between the current song and the newly added song).


#### Returns `int|false`

Returns the song ID on success or false on failure.


</div><div class="method">
<h3 class="method-name">add_search</h3>
<p>Same as `search()` but adds the songs into the Queue at position $pos.</p>

```php
Queue::add_search(FloFaber\MphpD\Filter $filter, string $sort = '', array $window = Array, int $position = -1) : bool
```

#### Parameters

*  \Filter $filter
*  string $sort
*  array $window
*  int $position


#### Returns `bool`




</div><div class="method">
<h3 class="method-name">add_find</h3>
<p>Same as `find()` but this adds the matching song to the Queue.</p>

```php
Queue::add_find(FloFaber\MphpD\Filter $filter, string $sort = '', array $window = Array, int $pos = -1) : bool
```

#### Parameters

*  \Filter $filter
*  string $sort
*  array $window
*  int $pos Optional. If specified the matched songs will be added to this position in the Queue.


#### Returns `bool`




</div><div class="method">
<h3 class="method-name">clear</h3>
<p>Clears the queue</p>

```php
Queue::clear() : bool
```

#### Parameters

*none*


#### Returns `bool`

Returns true on success and false on failure.


</div><div class="method">
<h3 class="method-name">delete</h3>
<p>Deletes a song or a range of songs from the queue</p>

```php
Queue::delete( $p) : bool
```

#### Parameters

*  int|array $p The song position or Range


#### Returns `bool`




</div><div class="method">
<h3 class="method-name">delete_id</h3>
<p>Deletes the song with ID $songid from the Queue</p>

```php
Queue::delete_id(int $songid) : bool
```

#### Parameters

*  int $songid


#### Returns `bool`




</div><div class="method">
<h3 class="method-name">move</h3>
<p>Moves the song at $from to $to in the queue</p>

```php
Queue::move( $from, string $to) : bool
```

#### Parameters

*  int|array $from Song position or Range
*  string $to If starting with + or -, then it is relative to the current song
e.g. +0 moves to right after the current song and -0 moves to right before the current song
(i.e. zero songs between the current song and the moved range).


#### Returns `bool`




</div><div class="method">
<h3 class="method-name">move_id</h3>
<p>Moves the song with $from (songid) to $to (playlist index) in the queue</p>

```php
Queue::move_id(int $from, string $to) : bool
```

#### Parameters

*  int $from
*  string $to If starting with + or -, then it is relative to the current song
e.g. +0 moves to right after the current song and -0 moves to right before the current song
(i.e. zero songs between the current song and the moved song).


#### Returns `bool`




</div><div class="method">
<h3 class="method-name">find</h3>
<p>Same as Queue::search but case-sensitive</p>

```php
Queue::find(FloFaber\MphpD\Filter $filter, string $sort = '', array $window = Array) : array|false
```

#### Parameters

*  \Filter $filter
*  string $sort
*  array $window


#### Returns `array|false`




</div><div class="method">
<h3 class="method-name">get_id</h3>
<p>Returns an associative arrays containing information about the song with ID $songid.</p>

```php
Queue::get_id(int $songid) : array|false
```

#### Parameters

*  int $songid


#### Returns `array|false`

Associative array containing song information or false on failure.


</div><div class="method">
<h3 class="method-name">get</h3>
<p>If $p is omitted returns an array of associative arrays containing information about songs in the Queue.</p>

```php
Queue::get( $p = -1) : array|false
```

#### Parameters

*  $p int|array Optional. Song Position or Range.

If omitted all songs in the queue will be returned.


#### Returns `array|false`

Array on success. False on failure.


</div><div class="method">
<h3 class="method-name">search</h3>
<p>Search the queue for matching songs.</p>

```php
Queue::search(FloFaber\MphpD\Filter $filter, string $sort = '', array $window = Array) : array|false
```

#### Parameters

*  \Filter $filter The Filter.
*  string $sort If specified the results are sorted by the specified tag.
*  array $window If specified returns only the given portion.


#### Returns `array|false`

Returns array on success and false on failure.


</div><div class="method">
<h3 class="method-name">changes</h3>
<p>Returns an array of changed songs currently in the playlist since $version.</p>

```php
Queue::changes(int $version,  $range = -1, bool $metadata = ) : array|false
```

#### Parameters

*  int $version The current version can be retrieved with MphpD::status([ "playlist" ]).
*  int|array $range Position of song or Range
*  bool $metadata If set to true the metadata will be included.

If set to false only the position and ID of the changed songs will be returned.


#### Returns `array|false`

Returns array on success and false on failure.


</div><div class="method">
<h3 class="method-name">prio</h3>
<p>Sets the priority of given songs to $priority.</p>

```php
Queue::prio(int $priority,  $range = -1) : bool
```

#### Parameters

*  int $priority Priority. 0-255.
*  int|array $range Position of song or Range


#### Returns `bool`




</div><div class="method">
<h3 class="method-name">prio_id</h3>
<p>Sets the priority of Song ID $id to $priority.</p>

```php
Queue::prio_id(int $priority, int $id) : bool
```

#### Parameters

*  int $priority
*  int $id


#### Returns `bool`




</div><div class="method">
<h3 class="method-name">range_id</h3>
<p>Set's the portion of the song that should be played.</p>

```php
Queue::range_id(int $songid, array $range = Array) : bool
```

#### Parameters

*  int $songid
*  array $range Range. Start and End are offsets in seconds. If omitted the "play-range" will be removed from the song.


#### Returns `bool`




</div><div class="method">
<h3 class="method-name">shuffle</h3>
<p>Shuffle the Queue.</p>

```php
Queue::shuffle(array $range = Array) : bool
```

#### Parameters

*  array $range If specified only this portion will be shuffled.


#### Returns `bool`




</div><div class="method">
<h3 class="method-name">swap</h3>
<p>Swap two songs in Queue. By Position.</p>

```php
Queue::swap(int $songpos_1, int $songpos_2) : bool
```

#### Parameters

*  int $songpos_1
*  int $songpos_2


#### Returns `bool`




</div><div class="method">
<h3 class="method-name">swap_id</h3>
<p>Swap two songs in Queue. By ID.</p>

```php
Queue::swap_id(int $songid_1, int $songid_2) : bool
```

#### Parameters

*  int $songid_1
*  int $songid_2


#### Returns `bool`




</div><div class="method">
<h3 class="method-name">add_tag_id</h3>
<p>Adds a tag to the specified song</p>

```php
Queue::add_tag_id(int $songid, string $tag, string $value) : bool
```

#### Parameters

*  int $songid
*  string $tag Tag name
*  string $value Tag value


#### Returns `bool`




</div><div class="method">
<h3 class="method-name">clear_tag_id</h3>
<p>Removes a tag from the specified song</p>

```php
Queue::clear_tag_id(int $songid, string $tag) : bool
```

#### Parameters

*  int $songid
*  string $tag Tag name


#### Returns `bool`




</div>