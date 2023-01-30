<?php
/*
 * MphpD
 * http://mphpd.org
 *
 * Copyright (c) 2023 Florian Faber
 * http://www.flofaber.com
 */

const MPD_STATE_OFF     = 0x00;
const MPD_STATE_ON      = 0x01;
const MPD_STATE_ONESHOT = 0x02;

const MPD_MODE_CREATE  = 0x01;
const MPD_MODE_APPEND  = 0x02;
const MPD_MODE_REPLACE = 0x04;

const MPD_CMD_READ_RAW         = 0x01;
const MPD_CMD_READ_NORMAL      = 0x02;
const MPD_CMD_READ_LIST        = 0x04;
const MPD_CMD_READ_LIST_SINGLE = 0x08;
const MPD_CMD_READ_NONE        = 0x16;
const MPD_CMD_READ_BOOL        = 0x32;

const MPD_ESCAPE_DOUBLE_QUOTES = 0x02;
const MPD_ESCAPE_PREFIX_SPACE  = 0x04;
const MPD_ESCAPE_SUFFIX_SPACE  = 0x08;
