<?php declare(strict_types=1);

require_once __DIR__ . "/config/config.php";
require_once __DIR__ . "/../vendor/autoload.php";

use FloFaber\MphpD\MphpD;
use FloFaber\MphpD\Filter;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(\FloFaber\MphpD\Queue::class)]
class QueueTest extends TestCase
{

  protected MphpD $mphpd;
  private int $version;
  private array $random_song;

  public function setUp(): void
  {
    parent::setUp();

    $this->mphpd = new MphpD(MPD_CONFIG);
    $this->mphpd->connect();

    $this->version = $this->mphpd->status([ "playlist" ]);

    $this->mphpd->queue()->clear();

    // load a playlist first
    $this->mphpd->playlist("test")->load();
  }

  public function tearDown(): void
  {
    parent::tearDown();

    $this->mphpd->queue()->clear();
    $this->mphpd->disconnect();
  }


  private function pick_random_queue_id($queue)
  {
    return $queue[array_rand($queue)]["id"];
  }


  public function testAdd()
  {
    $this->assertTrue($this->mphpd->queue()->add("test-song1.mp3"));
    $this->assertTrue($this->mphpd->queue()->add("test-song1.mp3",2));
    $this->assertFalse($this->mphpd->queue()->add("test-song1.mp3",15));
    $this->assertSame(5, count($this->mphpd->queue()->get()));
  }


  public function testAddID()
  {
    $this->assertIsInt($this->mphpd->queue()->add_id("test-song1.mp3"));
    $this->assertIsInt($this->mphpd->queue()->add_id("test-song1.mp3",2));
    $this->assertFalse($this->mphpd->queue()->add_id("test-song1.mp3",15));

    $this->assertSame(5, count($this->mphpd->queue()->get()));
  }


  public function testAddSearch()
  {
    $r = $this->mphpd->queue()->add_search(new Filter("album", "==", "Test songs"), "ArtistSort", [0,2], 1);
    $this->assertTrue($r);
    $this->assertSame(5, count($this->mphpd->queue()->get()));

    $r = $this->mphpd->queue()->add_search(new Filter("album", "==", "Test songs"), case_sensitive: true);
    $this->assertTrue($r);
    $this->assertSame(5, count($this->mphpd->queue()->get()));
  }


  public function testAddFind()
  {
    $r = $this->mphpd->queue()->add_find(new Filter("album", "==", "Test songs"), "ArtistSort", [0,2], 1);
    $this->assertTrue($r);
    $this->assertSame(3, count($this->mphpd->queue()->get()));

    $r = $this->mphpd->queue()->add_find(new Filter("album", "==", "test songs"));
    $this->assertTrue($r);
    $this->assertSame(6, count($this->mphpd->queue()->get()));
  }


  public function testClear()
  {
    $this->assertTrue($this->mphpd->queue()->clear());
    $this->assertSame(0, count($this->mphpd->queue()->get()));
  }


  public function testDelete()
  {
    $this->assertTrue($this->mphpd->queue()->delete([1,3]));
    $this->assertSame(1, count($this->mphpd->queue()->get()));

    $this->assertFalse($this->mphpd->queue()->delete(15));

    $this->assertTrue($this->mphpd->queue()->delete(0));
    $this->assertSame(0, count($this->mphpd->queue()->get()));
  }


  public function testDeleteId()
  {
    $queue = $this->mphpd->queue()->get();
    $song_id = $queue[array_rand($queue)]["id"];

    $song_id = $this->pick_random_queue_id($queue);

    $this->assertTrue($this->mphpd->queue()->delete_id((int)$song_id));

    $queue = $this->mphpd->queue()->get();
    $this->assertSame(2, count($queue));
    foreach ($queue as $item) {
      $this->assertNotSame($song_id, $item["id"]);
    }
  }


  public function testMove()
  {
    $this->assertTrue($this->mphpd->queue()->move(0, 1));
    $this->assertTrue($this->mphpd->queue()->move([1,3], 0));

    $queue = $this->mphpd->queue()->get(metadata: false);

    $this->assertSame([
      [
        "file" => "test-song1.mp3",
      ],[
        "file" => "test-song3.mp3",
      ],[
        "file" => "test-song2.mp3",
      ],
    ], $queue);
  }


  public function testMoveId()
  {
    $queue = $this->mphpd->queue()->get();
    $this->assertTrue($this->mphpd->queue()->move_id($this->pick_random_queue_id($queue), 1));
    $this->assertTrue($this->mphpd->queue()->move_id($this->pick_random_queue_id($queue), 0));
    $this->assertFalse($this->mphpd->queue()->move_id($this->pick_random_queue_id($queue), 15));
  }


  public function testFind()
  {
    $found = $this->mphpd->queue()->find(new Filter("album", "==", "test songs"), "ArtistSort", [0,2]);

    $this->assertNotFalse($found);
    $this->assertSame(2, count($found));
  }


  public function testGetId()
  {
    $queue = $this->mphpd->queue()->get();
    $song_id = $this->pick_random_queue_id($queue);
    $song = $this->mphpd->queue()->get_id($song_id);

    $this->assertNotFalse($song);
    $this->assertNotEmpty($song);
  }


  public function testGet()
  {
    $queue = $this->mphpd->queue()->get();
    $this->assertNotFalse($queue);
    $this->assertSame(3, count($queue));

    $queue = $this->mphpd->queue()->get([0,2], false);
    $this->assertSame(2, count($queue));
    $this->assertArrayNotHasKey("id", $queue[0]);
    $this->assertArrayHasKey("file", $queue[0]);

    $queue = $this->mphpd->queue()->get(metadata: true);
    $this->assertArrayHasKey("id", $queue[0]);
  }


  public function testSearch()
  {
    $found = $this->mphpd->queue()->search(new Filter("album", "==", "teSt soNgs"), "ArtistSort", [0,2]);
    $this->assertSame(2, count($found));

    $found = $this->mphpd->queue()->search(new Filter("album", "==", "teSt soNgs"), case_sensitive: true);
    $this->assertSame(0, count($found));
  }


  public function testChanges()
  {
    $changes = $this->mphpd->queue()->changes($this->version);
    $this->assertSame(3, count($changes));
  }


  public function testPrio()
  {
    $this->assertTrue($this->mphpd->queue()->prio(200, [0,2]));
    $x = $this->mphpd->queue()->get(0);
    $this->assertSame(200, $x["prio"]);
  }


  public function testPrioId()
  {
    $song_id = $this->pick_random_queue_id($this->mphpd->queue()->get());
    $this->assertTrue($this->mphpd->queue()->prio_id(199, $song_id));
    $this->assertSame(199, $this->mphpd->queue()->get_id($song_id)["prio"]);
  }


  public function testRangeId()
  {
    $song_id = $this->pick_random_queue_id($this->mphpd->queue()->get());
    $this->assertTrue($this->mphpd->queue()->range_id($song_id, [1,4]));

    $song = $this->mphpd->queue()->get_id($song_id);
    $this->assertSame("1.000-4.000", $song["range"]);

    $this->assertTrue($this->mphpd->queue()->range_id($song_id));
  }


  public function testShuffle()
  {
    $queue_old = $this->mphpd->queue()->get();

    // make sure the queue is different before asserting difference. It's likely to be shuffled in the same way as before when using only 3 songs
    do{
      $this->mphpd->queue()->shuffle();
      $queue_new = $this->mphpd->queue()->get();
    }while(json_encode($queue_old) === json_encode($queue_new));

    $this->assertNotSame($queue_old, $queue_new);
  }


  public function testSwap()
  {
    $this->assertTrue($this->mphpd->queue()->swap(0,2));

    $this->assertSame([
      [
        "file" => "test-song3.mp3",
      ],[
        "file" => "test-song2.mp3",
      ],[
        "file" => "test-song1.mp3",
      ],
    ], $this->mphpd->queue()->get(metadata: false));

  }


  public function testSwapId()
  {
    $queue = $this->mphpd->queue()->get();
    $songid_1 = $this->pick_random_queue_id($queue);
    do{
      $songid_2 = $this->pick_random_queue_id($queue);
    }while($songid_1 === $songid_2);

    $this->assertTrue($this->mphpd->queue()->swap_id($songid_1, $songid_2));

    $this->assertNotSame($queue, $this->mphpd->queue()->get());
  }


  public function testAddTagId()
  {
    $song_id = $this->pick_random_queue_id($this->mphpd->queue()->get());

    $tagtypes = $this->mphpd->tagtypes();
    $tagtype = $tagtypes[array_rand($tagtypes)];

    $x = $this->mphpd->queue()->add_tag_id($song_id, $tagtype, "fixoida");

    // cant edit tags of local files
    $this->assertFalse($x);
  }


  public function testClearTagId()
  {
    $song_id = $this->pick_random_queue_id($this->mphpd->queue()->get());

    $tagtypes = $this->mphpd->tagtypes();
    $tagtype = $tagtypes[array_rand($tagtypes)];

    $x = $this->mphpd->queue()->clear_tag_id($song_id, $tagtype);

    // cant edit tags of local files
    $this->assertFalse($x);
  }


}
