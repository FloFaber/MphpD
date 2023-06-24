title: FloFaber\MphpD\Channel
tags: class

---

<h1 class="method-name">FloFaber\MphpD\Channel</h1>
<p></p>

```php
MphpD::channel(string $name) : Channel
```

## Methods

<div class="method">
<h3 class="method-name">__construct</h3>
<p>This class is not intended for direct usage.</p>

```php
Channel::__construct(FloFaber\MphpD\MphpD $mphpd, string $name)
```

#### Parameters

*  \MphpD $mphpd
*  string $name


#### Returns ``



</div><div class="method">
<h3 class="method-name">subscribe</h3>
<p>Subscribe to the channel.</p>

```php
Channel::subscribe() : bool
```

#### Parameters

*none*


#### Returns `bool`




</div><div class="method">
<h3 class="method-name">unsubscribe</h3>
<p>Unsubscribe the channel.</p>

```php
Channel::unsubscribe() : bool
```

#### Parameters

*none*


#### Returns `bool`




</div><div class="method">
<h3 class="method-name">read</h3>
<p>Returns a list of the channel's messages.</p>

```php
Channel::read() : array|false
```

#### Parameters

*none*


#### Returns `array|false`

`Array` containing the messages on success. `False` otherwise.


</div><div class="method">
<h3 class="method-name">send</h3>
<p>Send a message to the channel.</p>

```php
Channel::send(string $message) : bool
```

#### Parameters

*  string $message


#### Returns `bool`




</div>