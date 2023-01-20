<?php
/*
 * MphpD
 * http://mphpd.org
 *
 * Copyright (c) 2023 Florian Faber
 * http://www.flofaber.com
 */

require_once __DIR__ . "/mphpdTest.php";

use FloFaber\MphpD;
use PHPUnit\Framework\TestCase;

class InactiveMphpdTest extends mphpdTest
{
  public function setUp(): void
  {
    $this->mpd = new MphpD([
      "errormode" => MPD_ERRORMODE_EXCEPTION
    ]);
    $this->mpd->connect();
    $this->mpd->queue()->clear();
    $this->mpd->playlist("test")->load();
  }
}