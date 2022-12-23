<?php


require_once __DIR__ . "/../src/mphpd.php";

use FloFaber\MphpD;
use PHPUnit\Framework\TestCase;

abstract class mphpdTest extends TestCase
{

  protected MphpD $mpd;

  public function __construct(?string $name = null, array $data = [], $dataName = '')
  {
    parent::__construct($name, $data, $dataName);

  }

  public function tearDown(): void
  {
    $this->mpd->disconnect();
  }

  public function testInstance()
  {
    $this->assertInstanceof(MphpD::class, $this->mpd);
  }

  public function testInvalidCommand()
  {
    $this->expectException(FloFaber\MPDException::class);
    $this->mpd->cmd("unknowncommand");
  }

  public function testClearError()
  {
    $this->assertNotFalse($this->mpd->status()->clearerror());
  }

  public function testCurrentSong()
  {
    $this->assertNotFalse($this->mpd->status()->currentsong());
  }

  public function testStatus()
  {
    $this->assertArrayHasKey("single", $this->mpd->status()->get());

    $this->assertIsInt($this->mpd->status()->get([ "playlist" ]));
    $this->assertNull($this->mpd->status()->get([ "nonexistant" ]));

    $this->assertArrayHasKey("random", $this->mpd->status()->get([ "random", "single" ]));
    $this->assertIsInt($this->mpd->status()->get([ "playlist", "single" ])["playlist"]);

  }

  public function testStats()
  {
    $this->assertArrayHasKey("artists", $this->mpd->status()->stats());

    $this->assertIsInt($this->mpd->status()->stats([ "songs" ]));
    $this->assertNull($this->mpd->status()->stats([ "nonexistant" ]));

    $this->assertArrayHasKey("uptime", $this->mpd->status()->stats([ "uptime", "songs" ]));
    $this->assertIsInt($this->mpd->status()->stats([ "uptime", "single" ])["uptime"]);
  }


  public function testUpdate()
  {
    $job = $this->mpd->db()->update("", true, true);
    $this->assertIsInt($job);
    $this->assertIsInt($this->mpd->status()->get([ "updating_db" ]));

    $this->assertSame($job, $this->mpd->db()->update());
  }


  public function testBulk()
  {

    $this->mpd->bulk_start();

    $this->mpd->status()->get();
    $this->mpd->playlists();
    $this->mpd->status()->stats();
    $this->mpd->cmd("invalidcommand");
    $this->mpd->queue()->get();

    $ret = $this->mpd->bulk_end(true);

    $this->assertSame(count($ret), 4);

    $this->assertArrayHasKey("state", $ret[0]);
    $this->assertNotNull($ret[0]);

    $this->assertArrayHasKey("playlist", $ret[1][0]);
    $this->assertNotNull($ret[1][0]["playlist"]);

    $this->assertArrayHasKey("artists", $ret[2]);
    $this->assertNotNull($ret[2]["artists"]);

    $this->assertInstanceOf(\FloFaber\MPDException::class, $ret[3]);

  }



}

