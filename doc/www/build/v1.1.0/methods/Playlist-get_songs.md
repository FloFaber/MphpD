title: Playlist::get_songs
tags: method,Playlist

---

<div class="method">
<h3 class="method-name">get_songs</h3>
<p>Returns a list of all songs in the specified playlist.</p>

```php
Playlist::get_songs(bool $metadata = ) : array|false
```

#### Parameters

*  bool $metadata If set to true metadata like duration, last-modified,... will be included.


#### Returns `array|false`

On success returns an Array of associative Arrays containing song information. False on failure.


</div>