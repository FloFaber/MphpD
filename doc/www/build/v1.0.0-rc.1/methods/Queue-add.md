title: Queue::add
tags: method,Queue

---

<div class="method">
<h3 class="method-name">add</h3>
<p>Adds the file $uri to the queue (directories add recursively). $uri can also be a single file.<br></p>

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


</div>