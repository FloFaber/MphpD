title: FloFaber\MphpD\Output
tags: class

---

<h1 class="method-name">FloFaber\MphpD\Output</h1>
<p>Have a look at the [MPD doc](https://mpd.readthedocs.io/en/latest/protocol.html#audio-output-devices) for more.</p>

```php
MphpD::output(int $id) : Output
```

## Methods

<div class="method">
<h3 class="method-name">__construct</h3>
<p>This class is not intended for direct usage.<br>Use MphpD::output() instead to retrieve an instance of this class.</p>

```php
Output::__construct(FloFaber\MphpD\MphpD $mphpd, int $id)
```

#### Parameters

*  \MphpD $mphpd
*  int $id


#### Returns ``



</div><div class="method">
<h3 class="method-name">disable</h3>
<p>Disable the given output<br></p>

```php
Output::disable() : bool
```

#### Parameters

*none*


#### Returns `bool`




</div><div class="method">
<h3 class="method-name">enable</h3>
<p>Enable the given output<br></p>

```php
Output::enable() : bool
```

#### Parameters

*none*


#### Returns `bool`




</div><div class="method">
<h3 class="method-name">toggle</h3>
<p>Enable/Disable the given output depending on the current state.<br></p>

```php
Output::toggle() : bool
```

#### Parameters

*none*


#### Returns `bool`




</div><div class="method">
<h3 class="method-name">set</h3>
<p>Set a runtime attribute. Supported values can be retrieved from the `MphpD::outputs()` method.<br></p>

```php
Output::set(string $name, string $value) : bool
```

#### Parameters

*  string $name
*  string $value


#### Returns `bool`




</div>