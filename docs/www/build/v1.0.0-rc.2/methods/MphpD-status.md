title: MphpD::status
tags: method,MphpD

---

<div class="method">
<h3 class="method-name">status</h3>
<p>Returns the value of the specified key(s) from MPD's status.<br></p>

```php
MphpD::status(array $items = Array) : array|false|int|float|null
```

#### Parameters

*  array $items Optional. Array containing the wanted key(s) like `status`, `songid`,...

If only one item is given only it's value will be returned instead of an associative array.

If the given item(s) do not exist `null` will be set as their value.

If omitted, an associative array containing all status information will be returned.


#### Returns `array|false|int|float|null`

Returns
`false` on error

`string`, `int`, `float` or `null` if `$items` contains only one item. If it does not exist `null` will be returned instead.

Otherwise, an associative array containing all available (or specified) keys.


</div>