title: Player::pause
tags: method,Player

---

<div class="method">
<h3 class="method-name">pause</h3>
<p>Pause or resume playback.<br></p>

```php
Player::pause(?int $state = ) : bool
```

#### Parameters

*  int|null $state Optional. One of the following:

* `MPD_STATE_ON` - Pause

* `MPD_STATE_OFF` - Resume

If omitted or `null` the pause state is toggled.


#### Returns `bool`

Returns true on success and false on failure


</div>