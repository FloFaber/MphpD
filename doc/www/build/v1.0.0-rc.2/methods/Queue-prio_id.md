title: Queue::prio_id
tags: method,Queue

---

<div class="method">
<h3 class="method-name">prio_id</h3>
<p>Sets the priority of Song ID $id to $priority.<br>This only has effect when the `random`-mode is enabled.
A higher priority means that it will be played first when `random` is enabled.</p>

```php
Queue::prio_id(int $priority, int $id) : bool
```

#### Parameters

*  int $priority
*  int $id


#### Returns `bool`




</div>