title: FloFaber\MphpD\DB
tags: class

---

<h1 class="method-name">FloFaber\MphpD\DB</h1>
<p>This subclass is used to interact with and retrieve information from MPD's database.<br></p>

```php
MphpD::db() : DB
```

## Methods

<div class="method">
<h3 class="method-name">__construct</h3>
<p>This class is not intended for direct usage.<br>Use MphpD::db() instead to retrieve an instance of this class.</p>

```php
DB::__construct(FloFaber\MphpD\MphpD $mphpd)
```

#### Parameters

*  \MphpD $mphpd


#### Returns ``



</div><div class="method">
<h3 class="method-name">albumart</h3>
<p>Returns the albumart (binary!) for given song.<br></p>

```php
DB::albumart(string $songuri) : false|string
```

#### Parameters

*  string $songuri


#### Returns `false|string`

Returns binary data on success or false on failure.


</div><div class="method">
<h3 class="method-name">count</h3>
<p>Counts the number of songs and their playtime matching the specified Filter.<br>If $group is omitted returns an associative array containing a "songs" and "playtime" key.
If $group is specified an array of associative array will be returned.</p>

```php
DB::count(FloFaber\MphpD\Filter $filter, string $group = '') : array|false
```

#### Parameters

*  \Filter $filter
*  string $group A tag name like `artist`. If specified the results will be grouped by this tag.


#### Returns `array|false`




</div><div class="method">
<h3 class="method-name">fingerprint</h3>
<p>Calculate the song's fingerprint<br></p>

```php
DB::fingerprint(string $uri) : string|false
```

#### Parameters

*  string $uri


#### Returns `string|false`

Returns the fingerprint on success or false on failure.


</div><div class="method">
<h3 class="method-name">find</h3>
<p>Search for songs matching Filter and return an array of associative array of found songs.<br>Case-sensitive!</p>

```php
DB::find(FloFaber\MphpD\Filter $filter, string $sort = '', array $window = Array) : array|false
```

#### Parameters

*  \Filter $filter
*  string $sort Tag name to sort by. Like artist. If prefixed with `-` it will be sorted descending.

If omitted the order is undefined.
*  array $window Retrieve only a given portion


#### Returns `array|false`

Returns array on success and false on failure.


</div><div class="method">
<h3 class="method-name">list</h3>
<p>Lists unique tags values of the specified type. `$type` can be any tag supported by MPD.<br>If group is omitted returns an array of unique tag values of the specified type.
If group is specified returns an array of associative arrays containing the grouped result.</p>

```php
DB::list(string $type, ?FloFaber\MphpD\Filter $filter = , string $group = '') : array|false
```

#### Parameters

*  string $type Any tag supported by MPD. Like artist or album.
*  \Filter|null $filter
*  string $group Tag name to group the result by. Like artist or album.


#### Returns `array|false`




</div><div class="method">
<h3 class="method-name">ls</h3>
<p>List files,directories and playlists in $uri<br></p>

```php
DB::ls(string $uri, bool $metadata = , bool $recursive = ) : array|false
```

#### Parameters

*  string $uri
*  bool $metadata Specifies if additional information should be included.
*  bool $recursive Specified if files and directories should be listed recursively.


#### Returns `array|false`

Returns an array containing the keys `files`, `directories` and `playlists` on success and `false` on failure.


</div><div class="method">
<h3 class="method-name">read_comments</h3>
<p>Read "comments" from the specified file.<br>The meaning of these "comments" depend on the codec. For an OGG file this lists the vorbis commands.</p>

```php
DB::read_comments(string $uri) : array|false
```

#### Parameters

*  string $uri


#### Returns `array|false`




</div><div class="method">
<h3 class="method-name">read_picture</h3>
<p>Returns a picture of $uri by reading embedded pictures from binary tags.<br></p>

```php
DB::read_picture(string $uri) : false|string
```

#### Parameters

*  string $uri


#### Returns `false|string`

Binary data on success and false on failure.


</div><div class="method">
<h3 class="method-name">search</h3>
<p>Searches for matching songs and returns an array of associative arrays containing song information.<br>NOT case-sensitive!</p>

```php
DB::search(FloFaber\MphpD\Filter $filter, string $sort = '', array $window = Array) : array|false
```

#### Parameters

*  \Filter $filter
*  string $sort
*  array $window


#### Returns `array|false`




</div><div class="method">
<h3 class="method-name">update</h3>
<p>Update the Database and return the Job-ID.<br></p>

```php
DB::update(string $uri = '', bool $rescan = , bool $force = ) : int|false
```

#### Parameters

*  string $uri Optional. Only update the given path. Omit or specify an empty string to update everything.
*  bool $rescan If set to `true` also rescan unmodified files.
*  bool $force If set to `false` and an update Job is already running, just return its ID.

If true and an update Job is already running it starts another one and returns the ID of the new Job.


#### Returns `int|false`

Returns the Job-ID on success or false on failure.


</div>