title: DB::list
tags: method,DB

---

<div class="method">
<h3 class="method-name">list</h3>
<p>Lists unique tags values of the specified type. `$type` can be any tag supported by MPD.<br>If group is omitted returns an array of unique tag values of the specified type.
If group is specified returns an array of associative arrays containing the grouped result.</p>

```php
DB::list(string $type, ?FloFaber\MphpD\Filter $filter = , string $group = '') : array|false
```

#### Parameters

*  string $type Any tag supported by MPD. Like artist or album.
*  \Filter|null $filter
*  string $group Tag name to group the result by. Like artist or album.


#### Returns `array|false`




</div>