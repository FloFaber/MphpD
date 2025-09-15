<?php declare(strict_types=1);

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/config/config.php";

//require_once __DIR__ . "/../src/MphpD.php";

use FloFaber\MphpD\MPDException;
use FloFaber\MphpD\MphpD;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(\FloFaber\MphpD\Player::class)]
class PlayerTest extends TestCase
{

  private MphpD $mphpd;


  protected function setUp(): void
  {
    parent::setUp();

    $this->mphpd = new MphpD(MPD_CONFIG);
    $this->mphpd->connect();

    $this->mphpd->playlist("test")->load();
  }


  protected function tearDown(): void
  {
    parent::tearDown();
    $this->mphpd->player()->stop();
    $this->mphpd->queue()->clear();

    $this->mphpd->disconnect();
  }


  public function testSeek()
  {
    $this->assertTrue($this->mphpd->player()->seek(2, 5));
  }


  public function testSeek_cur()
  {
    $this->mphpd->playlist("test1")->load();
    $this->mphpd->player()->play(0);
    $ret = $this->mphpd->player()->seek_cur(5);
    $this->assertTrue($ret);
  }


  public function testVolume()
  {
    $this->mphpd->player()->pause(MPD_STATE_OFF);
    $this->assertIsInt($this->mphpd->player()->volume());
    $this->assertTrue($this->mphpd->player()->volume(10));
    $this->assertSame(10, $this->mphpd->player()->volume());
  }


  public function testPrevious()
  {
    $this->mphpd->player()->play(0);
    $this->assertTrue($this->mphpd->player()->previous());
  }


  public function testPlay()
  {
    $this->assertTrue($this->mphpd->player()->play(0));
    $this->assertFalse($this->mphpd->player()->play(999999));
  }


  public function testReplay_gain_mode()
  {
    $this->assertTrue($this->mphpd->player()->replay_gain_mode("auto"));
  }


  public function testReplay_gain_status()
  {
    $this->mphpd->player()->replay_gain_mode("off");
    $this->assertSame("off", $this->mphpd->player()->replay_gain_status()["replay_gain_mode"]);
  }


  public function testCurrent_song()
  {
    $this->mphpd->player()->play(1);
    $ret = $this->mphpd->player()->current_song();
    $this->assertIsArray($ret);
    $this->assertArrayHasKey("file", $ret);
  }


  public function testCrossfade()
  {
    $this->assertTrue($this->mphpd->player()->crossfade(10));
    $this->assertSame(10, $this->mphpd->status([ "xfade" ]));
  }


  public function testStop()
  {
    $this->assertTrue($this->mphpd->player()->stop());
    $this->assertSame("stop", $this->mphpd->status([ "state" ]));
  }


  public function testConsume()
  {
    $this->assertTrue($this->mphpd->player()->consume(MPD_STATE_ON));
    $this->assertTrue($this->mphpd->player()->consume(MPD_STATE_OFF));
    if($this->mphpd->version_bte("0.24")){
      $this->assertTrue($this->mphpd->player()->consume(MPD_STATE_ONESHOT));
    }else{
      $this->expectException(MPDException::class);
      $this->mphpd->player()->consume(MPD_STATE_ONESHOT);
    }
  }


  public function testNext()
  {
    $this->mphpd->player()->play(0);
    $this->assertTrue($this->mphpd->player()->next());
  }


  public function testMixramp_delay()
  {
    $this->assertTrue($this->mphpd->player()->mixramp_delay(5));
  }


  public function testMixramp_db()
  {
    $this->assertTrue($this->mphpd->player()->mixramp_db(1));
  }


  public function testRandom()
  {
    $this->assertTrue($this->mphpd->player()->random(MPD_STATE_ON));
    $this->assertTrue($this->mphpd->player()->random(MPD_STATE_OFF));
  }


  public function testPlay_id()
  {
    $this->assertTrue($this->mphpd->player()->play_id(0));
  }


  public function testSingle()
  {
    $this->assertTrue($this->mphpd->player()->single(MPD_STATE_ON));
    $this->assertTrue($this->mphpd->player()->single(MPD_STATE_OFF));
    if($this->mphpd->version_bte("0.21")){
      $this->assertTrue($this->mphpd->player()->single(MPD_STATE_ONESHOT));
    }else{
      $this->assertFalse($this->mphpd->player()->single(MPD_STATE_ONESHOT));
    }
  }


  public function testPause()
  {
    $this->assertTrue($this->mphpd->player()->play(0));
    $this->assertTrue($this->mphpd->player()->pause());
    $this->assertTrue($this->mphpd->player()->pause(MPD_STATE_OFF));
    $this->assertTrue($this->mphpd->player()->pause(MPD_STATE_ON));
  }


  public function testRepeat()
  {
    $this->assertTrue($this->mphpd->player()->repeat(MPD_STATE_ON));
    $this->assertTrue($this->mphpd->player()->repeat(MPD_STATE_OFF));
  }


  public function testSeek_id()
  {
    $ret = $this->mphpd->player()->seek_id(0, 5);
    $this->assertTrue($ret);
  }
}
