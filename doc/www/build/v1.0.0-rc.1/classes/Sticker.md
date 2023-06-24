title: FloFaber\MphpD\Sticker
tags: class

---

<h1 class="method-name">FloFaber\MphpD\Sticker</h1>
<p></p>

```php
MphpD::sticker(string $type, string $uri) : Sticker
```

## Methods

<div class="method">
<h3 class="method-name">__construct</h3>
<p>This class is not intended for direct usage.<br>Use MphpD::sticker() instead to retrieve an instance of this class.</p>

```php
Sticker::__construct(FloFaber\MphpD\MphpD $mphpd, string $type, string $uri)
```

#### Parameters

*  \MphpD $mphpd
*  string $type
*  string $uri


#### Returns ``



</div><div class="method">
<h3 class="method-name">get</h3>
<p>Returns the value of the specified sticker<br></p>

```php
Sticker::get(string $name) : false|string
```

#### Parameters

*  string $name


#### Returns `false|string`

Returns string on success and false on failure


</div><div class="method">
<h3 class="method-name">set</h3>
<p>Add a value to the specified sticker.<br></p>

```php
Sticker::set(string $name, string $value) : bool
```

#### Parameters

*  string $name
*  string $value


#### Returns `bool`




</div><div class="method">
<h3 class="method-name">delete</h3>
<p>Deletes the value from the specified sticker.<br></p>

```php
Sticker::delete(string $name = '') : bool
```

#### Parameters

*  string $name If omitted all sticker values will be deleted.


#### Returns `bool`




</div><div class="method">
<h3 class="method-name">list</h3>
<p>Returns an associative array containing sticker names and values of the specified object.<br></p>

```php
Sticker::list() : array|false
```

#### Parameters

*none*


#### Returns `array|false`




</div><div class="method">
<h3 class="method-name">find</h3>
<p>Search the sticker database for sticker with the specified name and/or value in the specified $uri<br></p>

```php
Sticker::find(string $name, string $operator = '', string $value = '') : array|false
```

#### Parameters

*  string $name The sticker name
*  string $operator Optional. Can be one of `=`, `<` or `>`. Only in combination with $value.
*  string $value Optional. The value to search for. Only in combination with $operator.


#### Returns `array|false`




</div>