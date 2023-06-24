title: Sticker::find
tags: method,Sticker

---

<div class="method">
<h3 class="method-name">find</h3>
<p>Search the sticker database for sticker with the specified name and/or value in the specified $uri</p>

```php
Sticker::find(string $name, string $operator = '', string $value = '') : array|false
```

#### Parameters

*  string $name The sticker name
*  string $operator Optional. Can be one of `=`, `<` or `>`. Only in combination with $value.
*  string $value Optional. The value to search for. Only in combination with $operator.


#### Returns `array|false`




</div>