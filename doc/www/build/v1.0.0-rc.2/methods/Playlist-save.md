title: Playlist::save
tags: method,Playlist

---

<div class="method">
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