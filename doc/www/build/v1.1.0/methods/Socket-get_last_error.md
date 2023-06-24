title: Socket::get_last_error
tags: method,Socket

---

<div class="method">
<h3 class="method-name">get_last_error</h3>
<p>Return an array containing information about the last error</p>

```php
Socket::get_last_error() : array
```

#### Parameters

*none*


#### Returns `array`

associative array containing the following keys:
<pre>
[
  "code" => (int),
  "message" => (string),
  "command" => (string),
  "commandlistnum" => (int)
]
</pre>


</div>