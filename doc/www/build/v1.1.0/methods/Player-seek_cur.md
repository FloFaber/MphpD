title: Player::seek_cur
tags: method,Player

---

<div class="method">
<h3 class="method-name">seek_cur</h3>
<p>Seeks to `$seconds` of the current song.</p>

```php
Player::seek_cur( $time) : bool
```

#### Parameters

*  string|int|float $time If prefixed with `+` or `-` the time is relative to the current playing position.


#### Returns `bool`

Returns true on success and false on failure


</div>