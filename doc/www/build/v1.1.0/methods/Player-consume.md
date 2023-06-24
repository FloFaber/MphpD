title: Player::consume
tags: method,Player

---

<div class="method">
<h3 class="method-name">consume</h3>
<p>Enables/Disables the consume mode</p>

```php
Player::consume(int $state) : bool
```

#### Parameters

*  int $state One of the following:

* `MPD_STATE_ON` - Enables consume mode

* `MPD_STATE_OFF` - Disables consume mode

* `MPD_STATE_ONESHOT` - Enables consume mode for a single song.
                      This is only supported on MPD version 0.24 and newer.


#### Returns `bool`

Returns true on success and false on failure


</div>