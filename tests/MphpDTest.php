<?php

require_once __DIR__ . "/config/config.php";
require_once __DIR__ . "/../src/MphpD.php";

use FloFaber\MphpD\MphpD;
use FloFaber\MphpD\Channel;
use FloFaber\MphpD\DB;
use FloFaber\MphpD\Output;
use FloFaber\MphpD\Partition;
use FloFaber\MphpD\Player;
use FloFaber\MphpD\Playlist;
use FloFaber\MphpD\Queue;
use FloFaber\MphpD\Sticker;

use PHPUnit\Framework\TestCase;

class MphpDTest extends TestCase
{

  private MphpD $mphpd;

  public function __construct(?string $name = null, array $data = [], $dataName = '')
  {
    parent::__construct($name, $data, $dataName);
    $this->mphpd = new MphpD(MPD_CONFIG);
    $this->mphpd->connect();
  }

  public function testInstance()
  {
    $this->assertInstanceof(MphpD::class, $this->mphpd);
  }

  public function testInvalidCommand()
  {
    $this->assertFalse($this->mphpd->cmd("unknowncommand"));
  }


  public function testChannel()
  {
    $this->assertInstanceOf(Channel::class, $this->mphpd->channel("c1"));
  }

  public function testChannels()
  {
    $this->assertIsArray($this->mphpd->channels());
  }


  public function testDb()
  {
    $this->assertInstanceOf(DB::class, $this->mphpd->db());
  }


  public function testOutput()
  {
    $this->assertInstanceOf(Output::class, $this->mphpd->output(0));
  }

  public function testOutputs()
  {
    $this->assertIsArray($this->mphpd->outputs());
  }


  public function testPartition()
  {
    $this->assertInstanceOf(Partition::class, $this->mphpd->partition("default"));
  }

  public function testPartitions()
  {
    $this->assertIsArray($this->mphpd->partitions());
  }


  public function testPlayer()
  {
    $this->assertInstanceOf(Player::class, $this->mphpd->player());
  }


  public function testPlaylist()
  {
    $this->assertInstanceOf(Playlist::class, $this->mphpd->playlist("test"));
    $this->assertNull($this->mphpd->playlist(""));
  }

  public function testPlaylists()
  {
    $playlists = $this->mphpd->playlists(true);
    $this->assertIsArray($playlists);
    $this->assertIsArray($playlists[0]);

    $playlists = $this->mphpd->playlists();
    $this->assertIsArray($playlists);
  }


  public function testQueue()
  {
    $this->assertInstanceOf(Queue::class, $this->mphpd->queue());
  }


  public function testSticker()
  {
    $this->assertInstanceOf(Sticker::class, $this->mphpd->sticker("file", "test-song1.mp3"));
  }



  public function testBulk()
  {
    $this->mphpd->bulk_start();

    $this->mphpd->bulk_add("add", [ "test-song1.mp3"]);
    $this->mphpd->bulk_add("status", [], MPD_CMD_READ_NORMAL);

    $ret = $this->mphpd->bulk_end();

    $this->assertIsArray($ret);
    $this->assertSame(count($ret), 2);

    $this->assertTrue($ret[0]);

    $this->assertNotFalse($ret[1]);
    $this->assertArrayHasKey("state", $ret[1]);
  }

  public function testClear_error()
  {
    $this->assertTrue($this->mphpd->clear_error());
  }

  public function testDecoders()
  {
    $this->assertIsArray($this->mphpd->decoders());
  }

  public function testCommands()
  {
    $this->assertIsArray($this->mphpd->commands());
  }

  public function testNotcommands()
  {
    $this->assertIsArray($this->mphpd->notcommands());
  }

  public function testUrlhandlers()
  {
    $this->assertIsArray($this->mphpd->urlhandlers());
  }

  public function testStatus()
  {
    $this->assertArrayHasKey("single", $this->mphpd->status());

    $this->assertIsInt($this->mphpd->status([ "playlist" ]));
    $this->assertNull($this->mphpd->status([ "nonexistant" ]));

    $this->assertArrayHasKey("random", $this->mphpd->status([ "random", "single" ]));
    $this->assertIsInt($this->mphpd->status([ "playlist", "single" ])["playlist"]);
  }

  public function testConfig()
  {
    if(strpos(MPD_CONFIG["host"], "unix:") === 0){
      $this->assertIsArray($this->mphpd->config());
    }else{
      $this->assertFalse($this->mphpd->config());
    }
  }

  public function testStats()
  {
    $this->assertArrayHasKey("artists", $this->mphpd->stats());

    $this->assertIsInt($this->mphpd->stats([ "songs" ]));
    $this->assertNull($this->mphpd->stats([ "nonexistant" ]));

    $this->assertArrayHasKey("uptime", $this->mphpd->stats([ "uptime", "songs" ]));
    $this->assertIsInt($this->mphpd->stats([ "uptime", "single" ])["uptime"]);
  }

  public function testPing()
  {
    $this->assertTrue($this->mphpd->ping());
  }

  public function testTagtypes()
  {
    $this->assertIsArray($this->mphpd->tagtypes());
  }

  public function testTagtypes_enable()
  {
    $this->assertTrue($this->mphpd->tagtypes_enable([ "artist", "name" ]));
  }

  public function testTagtypes_all()
  {
    $this->assertTrue($this->mphpd->tagtypes_all());
  }

  public function testTagtypes_disable()
  {
    $this->assertTrue($this->mphpd->tagtypes_disable([ "artist", "name" ]));
  }

  public function testTagtypes_clear()
  {
    $this->assertTrue($this->mphpd->tagtypes_clear());
  }


  /*
   * @ToDo
  public function testNeighbors()
  {
    $this->assertIsArray($this->mphpd->neighbors());
  }

  public function testMounts()
  {
    $this->assertIsArray($this->mphpd->mounts());
  }

  public function testMount()
  {

  }

  public function testUnmount()
  {

  }
  */

}
