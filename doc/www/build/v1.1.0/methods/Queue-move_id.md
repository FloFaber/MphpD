title: Queue::move_id
tags: method,Queue

---

<div class="method">
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




</div>