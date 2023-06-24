title: Playlist::add
tags: method,Playlist

---

<div class="method">
<h3 class="method-name">add</h3>
<p>Adds $uri to the specified playlist at position $pos.</p>

```php
Playlist::add(string $uri,  $pos = '') : bool
```

#### Parameters

*  string $uri Relative file path or other supported URIs.
*  int|string $pos Specifies where the songs will be inserted into the playlist.
Can be relative if prefixed with + or -


#### Returns `bool`

Returns true on success and false on failure.


</div>