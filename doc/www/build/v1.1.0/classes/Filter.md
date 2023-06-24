title: FloFaber\MphpD\Filter
tags: class

---

<h1 class="method-name">FloFaber\MphpD\Filter</h1>
<p>They are used in various other methods like [DB::search](../methods/DB-search), [Playlist::add_search](../methods/Playlist-add_search) and more.
Refer to the [MPD documentation](https://mpd.readthedocs.io/en/latest/protocol.html#filters) for more information about filters.</p>

```php
new Filter(string $tag, string $operator, string $value) : Filter
```

## Methods

<div class="method">
<h3 class="method-name">__construct</h3>
<p>Creates a new filter.</p>

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
<p>Used to chain multiple filters together with a logical AND.</p>

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
<p>Generate and return the Filter string.</p>

```php
Filter::__toString() : string
```

#### Parameters

*none*


#### Returns `string`




</div>