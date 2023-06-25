title: DB::find
tags: method,DB

---

<div class="method">
<h3 class="method-name">find</h3>
<p>Search for songs matching Filter and return an array of associative array of found songs.<br>Case-sensitive!</p>

```php
DB::find(FloFaber\MphpD\Filter $filter, string $sort = '', array $window = Array) : array|false
```

#### Parameters

*  \Filter $filter
*  string $sort Tag name to sort by. Like artist. If prefixed with `-` it will be sorted descending.

If omitted the order is undefined.
*  array $window Retrieve only a given portion


#### Returns `array|false`

Returns array on success and false on failure.


</div>