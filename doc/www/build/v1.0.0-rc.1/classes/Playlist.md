title: FloFaber\MphpD\Playlist
tags: class

---

<h1 class="method-name">FloFaber\MphpD\Playlist</h1>
<p></p>

```php
MphpD::playlist(string $name) : Playlist
```

## Methods

<div class="method">
<h3 class="method-name">__construct</h3>
<p>This class is not intended for direct usage.<br>Use MphpD::playlist() instead to retrieve an instance of this class.</p>

```php
Playlist::__construct(FloFaber\MphpD\MphpD $mphpd, string $name)
```

#### Parameters

*  \MphpD $mphpd
*  string $name


#### Returns ``



</div><div class="method">
<h3 class="method-name">exists</h3>
<p>Function to determine if the specified playlist exists.<br></p>

```php
Playlist::exists() : bool
```

#### Parameters

*none*


#### Returns `bool`

True if it exists. False if it doesn't.


</div><div class="method">
<h3 class="method-name">get_songs</h3>
<p>Returns a list of all songs in the specified playlist.<br></p>

```php
Playlist::get_songs(bool $metadata = ) : array|false
```

#### Parameters

*  bool $metadata If set to true metadata like duration, last-modified,... will be included.


#### Returns `array|false`

On success returns an Array of associative Arrays containing song information. False on failure.


</div><div class="method">
<h3 class="method-name">load</h3>
<p>Loads the specified playlist into the Queue.<br></p>

```php
Playlist::load(array $range = Array,  $pos = '') : bool
```

#### Parameters

*  array $range Range. If specified only the requested portion of the playlist is loaded. Starts at 0.
*  int|string $pos The $pos parameter specifies where the songs will be inserted into the queue.

Can be relative if prefixed with + or -


#### Returns `bool`




</div><div class="method">
<h3 class="method-name">add</h3>
<p>Adds $uri to the specified playlist at position $pos.<br></p>

```php
Playlist::add(string $uri,  $pos = '') : bool
```

#### Parameters

*  string $uri Relative file path or other supported URIs.
*  int|string $pos Specifies where the songs will be inserted into the playlist.
Can be relative if prefixed with + or -


#### Returns `bool`

Returns true on success and false on failure.


</div><div class="method">
<h3 class="method-name">add_search</h3>
<p>Search for songs using Filter and add them into the Playlist at position $pos.<br></p>

```php
Playlist::add_search(FloFaber\MphpD\Filter $filter, string $sort = '', array $window = Array, int $position = -1) : bool
```

#### Parameters

*  \Filter $filter
*  string $sort
*  array $window
*  int $position


#### Returns `bool`




</div><div class="method">
<h3 class="method-name">clear</h3>
<p>Removes all songs from the specified playlist.<br></p>

```php
Playlist::clear() : bool
```

#### Parameters

*none*


#### Returns `bool`

Returns true on success and false on failure.


</div><div class="method">
<h3 class="method-name">remove_song</h3>
<p>Deletes $songpos from the specified playlist.<br></p>

```php
Playlist::remove_song( $songpos = -1) : bool
```

#### Parameters

*  int|array $songpos Position of the song or Range


#### Returns `bool`




</div><div class="method">
<h3 class="method-name">move_song</h3>
<p>Moves the song at position $from in the specified playlist to the position $to.<br></p>

```php
Playlist::move_song(int $from, int $to) : bool
```

#### Parameters

*  int $from
*  int $to


#### Returns `bool`




</div><div class="method">
<h3 class="method-name">rename</h3>
<p>Renames the specified playlist to $new_name<br></p>

```php
Playlist::rename(string $new_name) : bool
```

#### Parameters

*  string $new_name New playlist name


#### Returns `bool`

Returns true on success and false on failure


</div><div class="method">
<h3 class="method-name">delete</h3>
<p>Removes the specified playlist from the playlist directory.<br></p>

```php
Playlist::delete() : bool
```

#### Parameters

*none*


#### Returns `bool`




</div><div class="method">
<h3 class="method-name">save</h3>
<p>Saves the queue to the specified playlist in the playlist directory<br></p>

```php
Playlist::save(int $mode = 1) : bool
```

#### Parameters

*  int $mode Optional. One of the following:

* MPD_MODE_CREATE: The default. Create a new playlist. Fails if a playlist with name $name already exists.

* MPD_MODE_APPEND: Append an existing playlist. Fails if a playlist with name $name doesn't already exist.
                   Only supported on MPD v0.24 and newer.

* MPD_MODE_REPLACE: Replace an existing playlist. Fails if a playlist with name $name doesn't already exist.
                    Only supported on MPD v0.24 and newer.


#### Returns `bool`

True on success. False on failure.


</div>