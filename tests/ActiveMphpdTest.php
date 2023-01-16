<?php

require_once __DIR__ . "/mphpdTest.php";

use FloFaber\MphpD;

class ActiveMphpdTest extends mphpdTest
{
  public function setUp(): void
  {
    $this->mpd = new MphpD([
      "errormode" => MPD_ERRORMODE_EXCEPTION
    ]);
    $this->mpd->connect();
    $this->mpd->playlist("test")->load();
    $this->mpd->player()->play();
  }

  public function testCurrentSong()
  {
    $currentsong = $this->mpd->status()->currentsong();
    $this->assertArrayHasKey("file", $currentsong);
    $this->assertArrayHasKey("id", $currentsong);
  }

  public function testStatus()
  {
    $ret = $this->mpd->status()->get();
    $this->assertArrayHasKey("volume", $ret);
    $this->assertIsInt($this->mpd->status()->get([ "volume" ]));
  }

  public function testStats()
  {
    $stats = $this->mpd->status()->stats();
    $this->assertIsArray($stats);
    $this->assertIsInt($stats["artists"]);
    $this->assertIsInt($stats["albums"]);
    $this->assertIsInt($stats["songs"]);
    $this->assertIsInt($stats["uptime"]);
    $this->assertIsInt($stats["db_playtime"]);
    $this->assertIsInt($stats["db_update"]);
    $this->assertIsInt($stats["playtime"]);
  }

  public function testConsume()
  {
    $consume = $this->mpd->player()->consume(MPD_STATE_ON);
    $this->assertNotFalse($consume);

    $consume = $this->mpd->status()->get(["consume"]);
    $this->assertEquals(1, $consume);

    $consume = $this->mpd->player()->consume(MPD_STATE_OFF);
    $this->assertNotFalse($consume);

    $consume = $this->mpd->status()->get(["consume"]);
    $this->assertEquals(0, $consume);



    // oneshot is only available since MPD 0.24
    if(version_compare($this->mpd->get_version(), "0.24") === -1){
      $this->expectException(FloFaber\MPDException::class);
    }

    $consume = $this->mpd->player()->consume(MPD_STATE_ONESHOT);
    $this->assertNotFalse($consume);

    $status = $this->mpd->status()->get();
    $this->assertEquals("oneshot", $status["consume"]);


  }

  public function testCrossfade()
  {
    $crossfade = $this->mpd->player()->crossfade(4);
    $this->assertNotFalse($crossfade);

    $status = $this->mpd->status()->get(["xfade"]);
    $this->assertEquals(4, $status);

    $crossfade = $this->mpd->player()->crossfade(0);
    $this->assertNotFalse($crossfade);

    $status = $this->mpd->status()->get(["xfade"]);
    $this->assertNull($status);

    $this->expectException(FloFaber\MPDException::class);
    $this->mpd->player()->crossfade(-1);
  }



}