# Changelog

## v1.2.0

### Improvements
* Added new `DB::get_picture` function.


## v1.1.1

### Fixes

* Fixed return type of `Queue::get`. Issue [#18](https://github.com/FloFaber/MphpD/issues/18).


## v1.1.0

### Fixes

* Fixed `MPD_CMD_READ_LIST_SINGLE` for real this time.
* `DB::read_picture` does not throw a warning anymore if the requested file did not contain a picture. In that case an empty string is returned (as before).
* `DB::count` and `DB::list` now return correct values when `$group` is set. This is probably a breaking change but it was broken before.


### Improvements
* Added `$case_sensitive`-parameter to `DB::count`. MPD command `searchcount` is therefore supported as well.
* Added new parse-mode `MPD_CMD_READ_GROUP` for grouped responses.


## v1.1.0-rc.3

### Fixes

* Fixed `MPD_CMD_READ_LIST_SINGLE` for real this time.


## v1.1.0-rc.2

### Fixes
* `DB::read_picture` does not throw a warning anymore if the requested file did not contain a picture. In that case an empty string is returned (as before).


## v1.1.0-rc.1

### Improvements
* Added `$case_sensitive`-parameter to `DB::count`. MPD command `searchcount` is therefore supported as well.
* Added new parse-mode `MPD_CMD_READ_GROUP` for grouped responses.

### Fixes
* `DB::count` and `DB::list` now return correct values when `$group` is set. This is probably a breaking change but it was broken before.


## v1.0.2

* [FIX] command parameters are not double-escaped anymore. This caused a lot of problems with special chars.


## v1.0.1

* [FIX] Fix parsing behaviour for mode `MPD_CMD_READ_LIST_SINGLE`. We do not [blame the user](https://github.com/FloFaber/MphpD/commit/e0db40675e56d96fddcde8a889c2cde72a907cc8#diff-0b6cfa6f8773aba0062f7e069cd25c530c73b6d545dfa3bbbb04baee77cb19eeL505) anymore.


## v1.0.0

You may now consider the majority of this library stable.

* [FIX] Improved documentation.
* [FIX] Fixed return type of Playlist::get_songs()


## v1.0.0-rc.2

* [FIX] Fixed critical unnoticed typo in composer.json


## v1.0.0-rc.1

* [BREAKING CHANGE] Reorganized Namespaces of nearly all classes.
* [BREAKING CHANGE] Moved all classes to src/ directory.
* [FIX] Updated namespace in composer.json for psr-4 autoloading.
* [FIX] Cleaned up docblocks and added several docblocks for classes.
* [FIX] The whole documentation is now in this repo including a rather ugly buildscript.
* [MISC] Moved utils.php to classes/Utils.php therefore moved the functions inside into a new Utils class.


## v0.1.3

* [BREAKING CHANGE] Renamed the main class file `mphpd.php` to `MphpD.php`.
* [FIX] `Floats` returned from MPD are not casted into `ints` anymore (#13). `MphpD::status` may now return `float` if `$items` contain only one item.
* The library is now available as a composer package. Hurray.


## v0.1.2

* [BREAKING CHANGE] Removed Mount.php and moved `mount()` and `unmount()` to MphpD class
* [BREAKING CHANGE] (re)moved `DB::list_all` and `DB::list_files` into a new `DB::ls` method.
* [BREAKING CHANGE] `Player::play()` doesn't unpause anymore if no `$pos` is given. `$pos` is now required.
* [FIX] Fixed bug in parses with `$mode = MPD_CMD_READ_LIST`.
* [FIX] Return type of `Queue::add_id` is now `int|false` instead of `array`
* [FIX] Return type of `Queue::add_find` is now `bool` instead of `array`
* [FIX] `$sort` is not required anymore in `Queue::add_search`
* [FIX] `Queue::find` does now work
* [FIX] `Queue::get` now returns a list when `$p` is omitted instead of a single song
* [FIX] `Queue::range_id` does not produce an error anymore if `$range` is omitted
* [FIX] `plchanges` and `plchangesposid` in `Queue::changes` is not reversed anymore.
* [FIX] `Player::volume` is not causing an `Undefined array key`-error anymore
* [FIX] `MphpD::get_last_error` now returns an empty array if there was no error yet
* [FIX] Return type of `Playlist::rename` is now `bool` instead of `array`
* [FIX] Fixed bug when saving a playlist on MPD versions before 0.24
* [FIX] Fixed bug in `Sticker::list` when parsing stickers
* [FIX] Fixed bug in `Sticker::find` when $uri was empty
* [FIX] Fixed bug when using UNIX socket (#10)
* `Player::consume` and `Player::single` now throw an MPDException if the given `$mode` is not supported
* Made `MphpD::readls` private


## v0.1.1

This _release_ breaks but also fixes a lot of stuff. I'm lucky this library is **A**: private and **B**:
still in development so we can break stuff without anyone noticing.

* [BREAKING CHANGE] Renamed a lot of methods to be more consistent (snake_case)
* [BREAKING CHANGE] Split up Status.php
* [BREAKING CHANGE] Moved searchadd,findadd and searchaddpl away from DB.php
* [BREAKING CHANGE] Renamed getError to get_last_error and made it return `array` instead of `MPDException`
* [FIX] Fixed _critical_ bug in response parser
* [FIX] Fixed Filters
* [FIX] Fixed bug in Channel.php where unread messages where thrown away
* [NEW] Introduced `MPD_CMD_READ_BOOL` and replaced `!== false` in a lot of methods
* Added copyright notice to source files
* Wrote some more tests (a lot more need to be written)
* Added required PHP functions to README



## v0.1.0

This is the first "real" release for MphpD.

* current MPD version (0.23.11) is fully supported
* MphpD::player::volume() now also work on MPD version <0.23.
* MphpD::binarylimit is now ignored for MPD version <0.22.4.

Some cleanup still needs to be done before the 1.0.0 release.


