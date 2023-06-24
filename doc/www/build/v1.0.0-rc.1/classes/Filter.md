title: FloFaber\MphpD\Filter
tags: class

---

<h1 class="method-name">FloFaber\MphpD\Filter</h1>
<p>Filters provide a way to search for specific songs. They take care of parsing and escaping.<br>They are used in various other methods like [DB::search](../classes/DB#search), [Playlist::add_search](../classes/Playlist#add_search) and more.
Refer to the [MPD documentation](https://mpd.readthedocs.io/en/latest/protocol.html#filters) for more information about filters.</p>

```php
new FloFaber\MphpD\Filter(string $tag, string $operator, string $value) : Filter
```

## Methods

<div class="method">
<h3 class="method-name">__construct</h3>
<p>Creates a new filter.<br></p>

```php
Filter::__construct(string $tag, string $operator, string $value)
```

#### Parameters

*  string $tag Tag to be searched for. Like artist, title,...
*  string $operator Comparison operator. Like ==, contains, ~=,...
*  string $value The value to search for. Unescaped.


#### Returns ``



</div><div class="method">
<h3 class="method-name">and</h3>
<p>Used to chain multiple filters together with a logical AND.<br></p>

```php
Filter::and(string $tag, string $operator, string $value)
```

#### Parameters

*  string $tag
*  string $operator
*  string $value


#### Returns ``

$this


</div><div class="method">
<h3 class="method-name">__toString</h3>
<p>Generate and return the Filter string.<br></p>

```php
Filter::__toString() : string
```

#### Parameters

*none*


#### Returns `string`




</div>