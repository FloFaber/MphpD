title: Player::single
tags: method,Player

---

<div class="method">
<h3 class="method-name">single</h3>
<p>Enables/Disables the single-mode. If enabled MPD will play the same song over and over.<br></p>

```php
Player::single(int $state) : bool
```

#### Parameters

*  int $state One of the following:

* `MPD_STATE_ON` - Enables single mode

* `MPD_STATE_OFF` - Disables single mode

* `MPD_STATE_ONESHOT` - Enables single mode for only a single time.
  This is only supported on MPD version 0.21 and newer.


#### Returns `bool`




</div>