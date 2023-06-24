title: FloFaber\MphpD\Partition
tags: class

---

<h1 class="method-name">FloFaber\MphpD\Partition</h1>
<p></p>

```php
MphpD::partition(string $name) : Partition
```

## Methods

<div class="method">
<h3 class="method-name">__construct</h3>
<p>This class is not intended for direct usage.</p>

```php
Partition::__construct(FloFaber\MphpD\MphpD $mphpd, string $name)
```

#### Parameters

*  \MphpD $mphpd
*  string $name


#### Returns ``



</div><div class="method">
<h3 class="method-name">switch</h3>
<p>Switch the client to the given partition</p>

```php
Partition::switch() : bool
```

#### Parameters

*none*


#### Returns `bool`




</div><div class="method">
<h3 class="method-name">create</h3>
<p>Create a new partition</p>

```php
Partition::create() : bool
```

#### Parameters

*none*


#### Returns `bool`




</div><div class="method">
<h3 class="method-name">delete</h3>
<p>Delete a given partition</p>

```php
Partition::delete() : bool
```

#### Parameters

*none*


#### Returns `bool`




</div><div class="method">
<h3 class="method-name">move_output</h3>
<p>Move a specific output to the current partition</p>

```php
Partition::move_output(string $name) : bool
```

#### Parameters

*  string $name Name of the output


#### Returns `bool`




</div>