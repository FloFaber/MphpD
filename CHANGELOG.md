# Changelog

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


