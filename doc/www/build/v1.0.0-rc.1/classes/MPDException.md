title: FloFaber\MphpD\MPDException
tags: class

---

<h1 class="method-name">FloFaber\MphpD\MPDException</h1>
<p>You may call `MPDException::getCode` and `MPDException::getMessage` to retrieve information about the error.

In case an error occurs at the protocol level the called methods simply return false.

To retrieve the last occurred error call [MphpD::get_last_error](../methods/MphpD-get_last_error).</p>

```php
MphpD::get_last_error() : array
```

## Methods

<div class="method">
<h3 class="method-name">__construct</h3>
<p></p>

```php
MPDException::__construct( $message = '',  $code = 0, ?Throwable $previous = , string $command = '', int $commandlist_num = 0)
```

#### Parameters

*none*


#### Returns ``



</div><div class="method">
<h3 class="method-name">__toString</h3>
<p>Returns all information as string<br></p>

```php
MPDException::__toString() : string
```

#### Parameters

*none*


#### Returns `string`




</div><div class="method">
<h3 class="method-name">getCommand</h3>
<p>Returns the command which caused the error.<br></p>

```php
MPDException::getCommand() : string
```

#### Parameters

*none*


#### Returns `string`




</div><div class="method">
<h3 class="method-name">getCommandlistNum</h3>
<p>Returns the command's list-number in case a [commandlist](../guides/commandlist) was used.<br></p>

```php
MPDException::getCommandlistNum() : int
```

#### Parameters

*none*


#### Returns `int`




</div><div class="method">
<h3 class="method-name">__wakeup</h3>
<p></p>

```php
MPDException::__wakeup()
```

#### Parameters

*none*


#### Returns ``



</div><div class="method">
<h3 class="method-name">getMessage</h3>
<p></p>

```php
MPDException::getMessage() : string
```

#### Parameters

*none*


#### Returns `string`



</div><div class="method">
<h3 class="method-name">getCode</h3>
<p></p>

```php
MPDException::getCode()
```

#### Parameters

*none*


#### Returns ``



</div><div class="method">
<h3 class="method-name">getFile</h3>
<p></p>

```php
MPDException::getFile() : string
```

#### Parameters

*none*


#### Returns `string`



</div><div class="method">
<h3 class="method-name">getLine</h3>
<p></p>

```php
MPDException::getLine() : int
```

#### Parameters

*none*


#### Returns `int`



</div><div class="method">
<h3 class="method-name">getTrace</h3>
<p></p>

```php
MPDException::getTrace() : array
```

#### Parameters

*none*


#### Returns `array`



</div><div class="method">
<h3 class="method-name">getPrevious</h3>
<p></p>

```php
MPDException::getPrevious() : ?Throwable
```

#### Parameters

*none*


#### Returns `?Throwable`



</div><div class="method">
<h3 class="method-name">getTraceAsString</h3>
<p></p>

```php
MPDException::getTraceAsString() : string
```

#### Parameters

*none*


#### Returns `string`



</div>