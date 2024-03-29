<?php

require_once __DIR__ . "/config/config.php";
require_once __DIR__ . "/../src/MphpD.php";

use FloFaber\MphpD\MphpD;
use FloFaber\MphpD\Filter;
use PHPUnit\Framework\TestCase;

class QueueTest extends TestCase
{

  protected MphpD $mphpd;
  private int $version;
  private array $random_song;

  public function __construct(?string $name = null, array $data = [], $dataName = '')
  {
    parent::__construct($name, $data, $dataName);
    $this->mphpd = new MphpD(MPD_CONFIG);
    $this->mphpd->connect();

    $this->version = $this->mphpd->status([ "playlist" ]);

  }

  public function setUp(): void
  {
    parent::setUp(); // TODO: Change the autogenerated stub
    $this->mphpd->queue()->clear();

    // load a playlist first
    $this->mphpd->playlist("test")->load();

    $songs = $this->mphpd->queue()->get();
    $rand = rand(0, count($songs)-1);
    $this->random_song = $songs[$rand];
  }

  public function testChanges()
  {
    $this->assertTrue($this->mphpd->status([ "playlist" ]) > $this->version);
  }

  public function testMove_id()
  {
    $this->assertTrue($this->mphpd->queue()->move_id(0, 1));
  }

  public function testClear()
  {
    $this->mphpd->queue()->clear();
    $this->assertEquals([], $this->mphpd->queue()->get());
  }

  public function testAdd_find()
  {
    $this->assertTrue($this->mphpd->queue()->add_find(new Filter("Artist", "contains", "Aequitas")));
  }

  public function testSwap_id()
  {
    $songs = $this->mphpd->queue()->get();
    $rand1 = $songs[rand(0, count($songs)-1)];
    $rand2 = $songs[rand(0, count($songs)-1)];

    $this->assertTrue($this->mphpd->queue()->swap_id($rand1["id"], $rand2["id"]));
  }

  public function testShuffle()
  {
    $this->assertTrue($this->mphpd->queue()->shuffle());
  }

  public function testFind()
  {
    $ret = $this->mphpd->queue()->find(new Filter("file", "contains", "test"));
    $this->assertNotEmpty($ret);
  }

  public function testMove()
  {
    $this->assertTrue($this->mphpd->queue()->move(0, 2));
  }

  public function testGet_id()
  {
    $this->assertIsInt($this->mphpd->queue()->get_id($this->random_song["id"])["id"]); // lol
  }

  public function testPrio_id()
  {
    $this->assertTrue($this->mphpd->queue()->prio_id(10, $this->random_song["id"]));
  }

  public function testGet()
  {
    $this->assertIsArray($this->mphpd->queue()->get());
    $this->assertIsArray($this->mphpd->queue()->get(1));
    $this->assertIsArray($this->mphpd->queue()->get([0,10]));
  }

  public function testDelete_id()
  {
    $this->assertTrue($this->mphpd->queue()->delete_id($this->random_song["id"]));
  }

  public function testClear_tag_id()
  {
    $id = $this->mphpd->queue()->add_id("https://www.youtube.com/watch?v=dQw4w9WgXcQ");
    $this->assertTrue($this->mphpd->queue()->clear_tag_id($id, "Artist"));
  }

  public function testPrio()
  {
    $this->assertTrue($this->mphpd->queue()->prio(10, 0));
  }

  public function testAdd()
  {
    $this->assertTrue($this->mphpd->queue()->add("https://www.youtube.com/watch?v=dQw4w9WgXcQ"));
  }

  public function testRange_id()
  {
    $this->assertTrue($this->mphpd->queue()->range_id($this->random_song["id"], [5, 30]));
    $this->assertTrue($this->mphpd->queue()->range_id($this->random_song["id"]));
  }

  public function testSwap()
  {
    $this->assertTrue($this->mphpd->queue()->swap(0,1));
  }

  public function testSearch()
  {
    $ret = $this->mphpd->queue()->search(new Filter("Artist", "contains", "fictional"));
    $this->assertNotEmpty($ret);
  }

  public function testDelete()
  {
    $this->assertTrue($this->mphpd->queue()->delete(1));
    $this->assertTrue($this->mphpd->queue()->delete([0,10]));
  }

  public function testAdd_id()
  {
    $this->assertIsInt($this->mphpd->queue()->add_id("https://www.youtube.com/watch?v=dQw4w9WgXcQ"));
  }

  public function testAdd_tag_id()
  {
    $id = $this->mphpd->queue()->add_id("https://www.youtube.com/watch?v=dQw4w9WgXcQ");
    $this->assertTrue($this->mphpd->queue()->add_tag_id($id, "Artist", "Coldplay"));
  }

  public function testAdd_search()
  {
    $this->assertTrue($this->mphpd->queue()->add_search(new Filter("Artist", "contains", "Aequitas")));
  }
}
