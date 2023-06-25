title: DB::update
tags: method,DB

---

<div class="method">
<h3 class="method-name">update</h3>
<p>Update the Database and return the Job-ID.<br></p>

```php
DB::update(string $uri = '', bool $rescan = , bool $force = ) : int|false
```

#### Parameters

*  string $uri Optional. Only update the given path. Omit or specify an empty string to update everything.
*  bool $rescan If set to `true` also rescan unmodified files.
*  bool $force If set to `false` and an update Job is already running, just return its ID.

If true and an update Job is already running it starts another one and returns the ID of the new Job.


#### Returns `int|false`

Returns the Job-ID on success or false on failure.


</div>