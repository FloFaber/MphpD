title: Queue::search
tags: method,Queue

---

<div class="method">
<h3 class="method-name">search</h3>
<p>Search the queue for matching songs.</p>

```php
Queue::search(FloFaber\MphpD\Filter $filter, string $sort = '', array $window = Array) : array|false
```

#### Parameters

*  \Filter $filter The Filter.
*  string $sort If specified the results are sorted by the specified tag.
*  array $window If specified returns only the given portion.


#### Returns `array|false`

Returns array on success and false on failure.


</div>