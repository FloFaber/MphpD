title: Queue::add_id
tags: method,Queue

---

<div class="method">
<h3 class="method-name">add_id</h3>
<p>Adds a song to the playlist (non-recursive) and returns the song id.<br></p>

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


</div>