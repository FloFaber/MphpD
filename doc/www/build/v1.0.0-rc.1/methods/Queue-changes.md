title: Queue::changes
tags: method,Queue

---

<div class="method">
<h3 class="method-name">changes</h3>
<p>Returns an array of changed songs currently in the playlist since $version.<br></p>

```php
Queue::changes(int $version,  $range = -1, bool $metadata = ) : array|false
```

#### Parameters

*  int $version The current version can be retrieved with MphpD::status([ "playlist" ]).
*  int|array $range Position of song or Range
*  bool $metadata If set to true the metadata will be included.

If set to false only the position and ID of the changed songs will be returned.


#### Returns `array|false`

Returns array on success and false on failure.


</div>