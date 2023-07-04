title: MPDException

---

# MPDException
MPDException is a slightly modified version of a standard Exception.
You may call `MPDException::getCode` and `MPDException::getMessage` to retrieve information about the error.

In case an error occurs at the protocol level the called methods simply return false.

To retrieve the last occurred error call [MphpD::get_last_error](../classes/MphpD#get_last_error).


```php
MphpD::get_last_error() : array
```
