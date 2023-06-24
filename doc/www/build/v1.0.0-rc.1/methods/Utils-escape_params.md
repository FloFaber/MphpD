title: Utils::escape_params
tags: method,Utils

---

<div class="method">
<h3 class="method-name">escape_params</h3>
<p>Function to parse an array of given parameters.<br></p>

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




</div>