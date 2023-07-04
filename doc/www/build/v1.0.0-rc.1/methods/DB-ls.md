title: DB::ls
tags: method,DB

---

<div class="method">
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


</div>