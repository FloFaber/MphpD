title: FloFaber\MphpD\Utils
tags: class

---

<h1 class="method-name">FloFaber\MphpD\Utils</h1>
<p></p>

```php

```

## Methods

<div class="method">
<h3 class="method-name">escape_params</h3>
<p>Function to parse an array of given parameters.</p>

```php
Utils::escape_params(array $params, int $flags = 4) : string
```

#### Parameters

*  array $params Array of strings.
*  int $flags One or multiple OR-ed together of the following:
* MPD_ESCAPE_NORMAL        - "beetle's juice" becomes "\"beetle\'s juice\""
* MPD_ESCAPE_DOUBLE_QUOTES - "beetle's juice" becomes "\\\"beetle\'s juice\\\""
* MPD_ESCAPE_PREFIX_SPACE  - Adds a space at the params beginning
* MPD_ESCAPE_SUFFIX_SPACE  - Adds a space at the params ending


#### Returns `string`




</div><div class="method">
<h3 class="method-name">pos_or_range</h3>
<p>Function to "convert" int or array to a pos or range argument</p>

```php
Utils::pos_or_range( $p) : int|string
```

#### Parameters

*  int|array $p


#### Returns `int|string`




</div><div class="method">
<h3 class="method-name">parse_error</h3>
<p>Function to parse an MPD error string to an array</p>

```php
Utils::parse_error(string $error)
```

#### Parameters

*  string $error The error string. For example "ACK [error@command_listNum] {current_command} message_text"


#### Returns ``

\MPDException


</div>