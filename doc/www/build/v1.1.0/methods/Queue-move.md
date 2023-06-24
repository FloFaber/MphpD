title: Queue::move
tags: method,Queue

---

<div class="method">
<h3 class="method-name">move</h3>
<p>Moves the song at $from to $to in the queue</p>

```php
Queue::move( $from, string $to) : bool
```

#### Parameters

*  int|array $from Song position or Range
*  string $to If starting with + or -, then it is relative to the current song
e.g. +0 moves to right after the current song and -0 moves to right before the current song
(i.e. zero songs between the current song and the moved range).


#### Returns `bool`




</div>