# Changelog

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


