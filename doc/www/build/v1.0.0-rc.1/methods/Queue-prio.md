title: Queue::prio
tags: method,Queue

---

<div class="method">
<h3 class="method-name">prio</h3>
<p>Sets the priority of given songs to $priority.<br>This only has effect when the `random`-mode is enabled.
A higher priority means that it will be played first when `random` is enabled.</p>

```php
Queue::prio(int $priority,  $range = -1) : bool
```

#### Parameters

*  int $priority Priority. 0-255.
*  int|array $range Position of song or Range


#### Returns `bool`




</div>