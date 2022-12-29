<?php


require_once __DIR__ . "/../src/mphpd.php";

use FloFaber\MPDException;
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

    $offset = 0;
    if($this->mpd->status()->get([ "updating_db" ]) !== NULL){
      $offset = 1;
    }

    $job = $this->mpd->db()->update("", true, true);
    $this->assertIsInt($job);
    $this->assertIsInt($this->mpd->status()->get([ "updating_db" ]));

    $this->assertSame($job, $this->mpd->db()->update() + $offset);
  }


  public function testBulkFail()
  {

    $this->mpd->bulk_start();

    $this->mpd->bulk_add("add", [ "Eisregen/02 - Kaltwassergrab.mp3"]);
    $this->mpd->bulk_add("status", [], MPD_CMD_READ_NORMAL);
    $this->mpd->bulk_add("add", [ "nonexistant/asdf.mp3"]);

    $this->expectException(MPDException::class);
    $this->mpd->bulk_end();
  }


  public function testBulk()
  {
    $this->mpd->bulk_start();

    $this->mpd->bulk_add("add", [ "Eisregen/02 - Kaltwassergrab.mp3"]);
    $this->mpd->bulk_add("status", [], MPD_CMD_READ_NORMAL);

    $ret = $this->mpd->bulk_end();

    $this->assertIsArray($ret);
    $this->assertSame(count($ret), 2);


    $this->assertTrue($ret[0]);

    $this->assertNotFalse($ret[1]);
    $this->assertArrayHasKey("state", $ret[1]);
  }



}

