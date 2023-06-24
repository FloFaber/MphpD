title: Player::volume
tags: method,Player

---

<div class="method">
<h3 class="method-name">volume</h3>
<p>Sets volume to `$volume` or returns the current volume if `$volume` is omitted.<br></p>

```php
Player::volume(int $volume = -1) : int|bool
```

#### Parameters

*  int $volume If specified the current volume is set to $volume.

If omitted the current volume is returned.


#### Returns `int|bool`

Returns `true` on success, `false` on failure and `int` if $volume was omitted.


</div>