title: Playlist::load
tags: method,Playlist

---

<div class="method">
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




</div>