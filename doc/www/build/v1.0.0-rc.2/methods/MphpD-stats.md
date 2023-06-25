title: MphpD::stats
tags: method,MphpD

---

<div class="method">
<h3 class="method-name">stats</h3>
<p>Returns the value of the specified key from MPD's stats.<br></p>

```php
MphpD::stats(array $items = Array) : array|false|int|null
```

#### Parameters

*  array $items Optional. Array containing the wanted stat(s). Example: `[ "artists", "uptime", "playtime" ]`

 If only one item is given only it's value will be returned instead of an associative array.

If the given item(s) do not exist `null` will be set as their value.

If omitted, an associative array containing all stats will be returned.


#### Returns `array|false|int|null`

Returns
`false` on error

`string`, `int` or `null` if $items contains only one item. If it does not exist `null` will be returned instead.

Otherwise, an associative array containing all available (or specified) stats.


</div>