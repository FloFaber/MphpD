# Ranges

Some methods, like [Queue::delete](../classes/Queue#delete) allow specifying a `Range` of songs instead of a single position.

Here they are implemented by simple arrays containing two `ints`.
The first one is the `start` and the seconds one is the `end`.

The `end` is excluded like in Python Ranges.
If you specify `[10,20]` the first song will be `10` but the last song will be `19`.
Keep that in mind.

If `end` is omitted (an array containing only a single item), it's interpreted as an open end.
So all songs from `start` are affected.

## Usage

For example, if you wanted to remove songs 5-20 from the Queue you want to do the following:
```php
use FloFaber\MphpD\MphpD;
$mphpd = new MphpD(...);
$mphpd->queue()->delete([5,21]);
```

Or if you want load all songs from a playlist starting at 30 until the end:
```php
$mphpd->playlist("keygen")->load([30,]);
```
