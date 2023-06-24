title: DB::count
tags: method,DB

---

<div class="method">
<h3 class="method-name">count</h3>
<p>Counts the number of songs and their playtime matching the specified Filter.</p>

```php
DB::count(FloFaber\MphpD\Filter $filter, string $group = '') : array|false
```

#### Parameters

*  \Filter $filter
*  string $group A tag name like `artist`. If specified the results will be grouped by this tag.


#### Returns `array|false`




</div>