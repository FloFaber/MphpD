title: DB::count
tags: method,DB

---

<div class="method">
<h3 class="method-name">count</h3>
<p>Counts the number of songs and their playtime matching the specified Filter.<br>If $group is omitted returns an associative array containing a "songs" and "playtime" key.
If $group is specified an array of associative array will be returned.</p>

```php
DB::count(FloFaber\MphpD\Filter $filter, string $group = '') : array|false
```

#### Parameters

*  \Filter $filter
*  string $group A tag name like `artist`. If specified the results will be grouped by this tag.


#### Returns `array|false`




</div>