title: Player::replay_gain_mode
tags: method,Player

---

<div class="method">
<h3 class="method-name">replay_gain_mode</h3>
<p>Specifies whether MPD shall adjust the volume of songs played using ReplayGain tags.</p>

```php
Player::replay_gain_mode(string $mode) : bool
```

#### Parameters

*  string $mode One of `off`, `track`, `album`, `auto`


#### Returns `bool`

Returns true on success and false on failure.


</div>