# Changelog

## v0.1.1

A new private _release_. I'm lucky this library is **A**: private and **B**:
still in development so we can break stuff without anyone noticing.

* [BREAKING CHANGE] Renamed a lot of methods to be more consistent
* [BREAKING CHANGE] Split up Status.php
* [BREAKING CHANGE] Moved searchadd,findadd and searchaddpl away from DB.php
* Fixed _critical_ bug in response parser
* Fixed Filters
* Introduced `MPD_CMD_READ_BOOL` and replaced `!== false` in a lot of methods
* Added copyright notice to source files
* Wrote some more tests



## v0.1.0

This is the first "real" release for MphpD.

* current MPD version (0.23.11) is fully supported
* MphpD::player::volume() now also work on MPD version <0.23.
* MphpD::binarylimit is now ignored for MPD version <0.22.4.

Some cleanup still needs to be done before the 1.0.0 release.


