title: MphpD::idle
tags: method,MphpD

---

<div class="method">
<h3 class="method-name">idle</h3>
<p>Waits until there is a noteworthy change in one or more of MPD’s subsystems.</p>

```php
MphpD::idle(string $subsystem = '', int $timeout = 60) : array|false
```

#### Parameters

*  string $subsystem
*  int $timeout Specifies how long to wait for MPD to return an answer.


#### Returns `array|false`

Returns an array of changed subsystems or false on timeout.


</div>