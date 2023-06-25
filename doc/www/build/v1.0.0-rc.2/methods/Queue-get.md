title: Queue::get
tags: method,Queue

---

<div class="method">
<h3 class="method-name">get</h3>
<p>If $p is omitted returns an array of associative arrays containing information about songs in the Queue.<br>If $p is specified returns an associative array containing the given songs information only.</p>

```php
Queue::get( $p = -1) : array|false
```

#### Parameters

*  $p int|array Optional. Song Position or Range.

If omitted all songs in the queue will be returned.


#### Returns `array|false`

Array on success. False on failure.


</div>