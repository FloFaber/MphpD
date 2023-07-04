title: Player::repeat
tags: method,Player

---

<div class="method">
<h3 class="method-name">repeat</h3>
<p>Specifies if MPD should start from the top again when reaching the end of the queue.<br></p>

```php
Player::repeat(int $state) : bool
```

#### Parameters

*  int $state Either `MPD_STATE_OFF` or `MPD_STATE_ON`


#### Returns `bool`

Returns true on success and false on failure.


</div>