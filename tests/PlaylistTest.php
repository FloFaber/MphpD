<?php declare(strict_types=1);

require_once __DIR__ . "/../src/MphpD.php";
require_once __DIR__ . "/config/config.php";

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use FloFaber\MphpD\MphpD;
use FloFaber\MphpD\Filter;

#[CoversClass(\FloFaber\MphpD\Playlist::class)]
final class PlaylistTest extends TestCase
{

  private MphpD $mphpd;


  public function setUp(): void
  {
    parent::setUp();

    $this->mphpd = new MphpD(MPD_CONFIG);
    $this->mphpd->connect();
  }

  public function tearDown(): void
  {
    $this->mphpd->playlist("test2")->delete();
    $this->mphpd->playlist("test3")->delete();
    $this->mphpd->disconnect();
  }


  public function testExists(){
    $this->assertFalse($this->mphpd->playlist("non-existant")->exists());
    $this->assertTrue($this->mphpd->playlist("test")->exists());
  }


  public function testGetsongs(){

    $a = $this->mphpd->playlist("test")->get_songs();
    $b = $this->mphpd->playlist("test")->get_songs(true);

    $this->assertSame([
      [
        "file" => "test-song1.mp3"
      ],[
        "file" => "test-song2.mp3"
      ],[
        "file" => "test-song3.mp3"
      ]
    ], $this->mphpd->playlist("test")->get_songs());

    $this->assertSame([
      [
        "file" => "test-song1.mp3",
        "last-modified" => "2025-03-17T17:04:36Z",
        "added" => "2025-03-17T17:04:36Z",
        "format" => "8000:16:1",
        "artist" => "fictional artist",
        "albumartist" => "Anar Software LLC",
        "title" => "test song 1",
        "album" => "test songs",
        "time" => 1899,
        "duration" => 1898.611,
      ],[
        "file" => "test-song2.mp3",
        "last-modified" => "2025-03-17T17:04:36Z",
        "added" => "2025-03-17T17:04:36Z",
        "format" => "8000:16:1",
        "artist" => "fictional artist",
        "albumartist" => "Anar Software LLC",
        "title" => "test song 2",
        "album" => "test songs",
        "time" => 1899,
        "duration" => 1898.611,
      ],[
        "file" => "test-song3.mp3",
        "last-modified" => "2025-03-17T17:04:36Z",
        "added" => "2025-03-17T17:04:36Z",
        "format" => "8000:16:1",
        "artist" => "fictional artist",
        "albumartist" => "Anar Software LLC",
        "title" => "test song 3",
        "album" => "test songs",
        "time" => 1801,
        "duration" => 1800.664,
      ]
    ], $this->mphpd->playlist("test")->get_songs(true));
  }


  public function testGet(){
    $a = $this->mphpd->playlist("test")->get();
    $b = $this->mphpd->playlist("test")->get(true);
    $c = $this->mphpd->playlist("test")->get(range: [0,2]);
    $d = $this->mphpd->playlist("test")->get(true, [0,2]);

    $this->assertSame([
      "test-song1.mp3",
      "test-song2.mp3",
      "test-song3.mp3"
    ], $this->mphpd->playlist("test")->get());

    $this->assertSame([
      [
        "file" => "test-song1.mp3",
        "last-modified" => "2025-03-17T17:04:36Z",
        "added" => "2025-03-17T17:04:36Z",
        "format" => "8000:16:1",
        "artist" => "fictional artist",
        "albumartist" => "Anar Software LLC",
        "title" => "test song 1",
        "album" => "test songs",
        "time" => 1899,
        "duration" => 1898.611,
      ],[
        "file" => "test-song2.mp3",
        "last-modified" => "2025-03-17T17:04:36Z",
        "added" => "2025-03-17T17:04:36Z",
        "format" => "8000:16:1",
        "artist" => "fictional artist",
        "albumartist" => "Anar Software LLC",
        "title" => "test song 2",
        "album" => "test songs",
        "time" => 1899,
        "duration" => 1898.611,
      ],[
        "file" => "test-song3.mp3",
        "last-modified" => "2025-03-17T17:04:36Z",
        "added" => "2025-03-17T17:04:36Z",
        "format" => "8000:16:1",
        "artist" => "fictional artist",
        "albumartist" => "Anar Software LLC",
        "title" => "test song 3",
        "album" => "test songs",
        "time" => 1801,
        "duration" => 1800.664,
      ]
    ], $this->mphpd->playlist("test")->get(true));

    $this->assertSame(2, count($c));
    $this->assertSame(2, count($d));

  }


  public function testLoad(){

    $this->mphpd->queue()->clear();

    $this->assertSame(0, $this->mphpd->status(["playlistlength"]));
    $this->assertFalse($this->mphpd->playlist("non-existent")->load());
    $this->assertTrue($this->mphpd->playlist("test")->load());
    $this->assertSame(3, $this->mphpd->status(["playlistlength"]));

    $this->assertTrue($this->mphpd->playlist("test")->load([0,2], 3));
    $this->assertSame(5, $this->mphpd->status(["playlistlength"]));

  }


  public function testAdd(){

    $this->mphpd->queue()->clear();
    $this->mphpd->playlist("test")->load();
    $this->mphpd->playlist("test2")->save();

    $this->assertSame(3, $this->mphpd->playlist("test2")->length()["songs"]);

    $this->assertFalse($this->mphpd->playlist("test2")->add("test-song4.mp3"));
    $this->assertTrue($this->mphpd->playlist("test2")->add("test-song3.mp3"));
    $this->assertFalse($this->mphpd->playlist("test2")->add("test-song3.mp3", 15));
    $this->assertTrue($this->mphpd->playlist("test2")->add("test-song3.mp3", 3));

    $this->assertSame(5, $this->mphpd->playlist("test2")->length()["songs"]);

  }


  public function testAddsearch(){

    $this->mphpd->queue()->clear();
    $this->mphpd->playlist("test")->load();
    $this->mphpd->playlist("test2")->save();
    $this->assertSame(3, $this->mphpd->playlist("test2")->length()["songs"]);

    $this->assertTrue($this->mphpd->playlist("test2")->add_search(new Filter("album", "==", "test songs")));
    $this->assertSame(6, $this->mphpd->playlist("test2")->length()["songs"]);

    $a = $this->mphpd->playlist("test2")->add_search(
      new Filter("album", "==", "test songs"),
      "ArtistSort",
      [0,2],
      0
    );
    $this->assertTrue($a);

    $this->assertSame(8, $this->mphpd->playlist("test2")->length()["songs"]);

  }


  public function testClear(){

    $this->mphpd->queue()->clear();
    $this->mphpd->playlist("test")->load();
    $this->mphpd->playlist("test2")->save();

    $this->assertSame(3, $this->mphpd->playlist("test2")->length()["songs"]);

    $this->assertFalse($this->mphpd->playlist("test3")->clear());
    $this->assertTrue($this->mphpd->playlist("test2")->clear());

    $this->assertSame(0, $this->mphpd->playlist("test2")->length()["songs"]);
  }


  public function testRemoveSong(){

    $this->mphpd->queue()->clear();
    $this->mphpd->playlist("test")->load();
    $this->mphpd->playlist("test2")->save();

    $this->assertSame(3, $this->mphpd->playlist("test2")->length()["songs"]);

    $this->assertFalse($this->mphpd->playlist("test2")->remove_song(14));
    $this->assertTrue($this->mphpd->playlist("test2")->remove_song(2));

    $this->assertSame([
      [
        "file" => "test-song1.mp3"
      ],[
        "file" => "test-song2.mp3"
      ]
    ], $this->mphpd->playlist("test2")->get_songs());
  }


  public function testMoveSong(){

    $this->mphpd->queue()->clear();
    $this->mphpd->playlist("test")->load();
    $this->mphpd->playlist("test2")->save();

    $this->assertEquals([
      0 => [
        "file" => "test-song1.mp3"
      ],
      1 => [
        "file" => "test-song2.mp3"
      ],
      2 => [
        "file" => "test-song3.mp3"
      ]
    ], $this->mphpd->playlist("test2")->get_songs());


    $this->mphpd->playlist("test2")->move_song(0, 2);
    $this->assertEquals([
      0 => [
        "file" => "test-song2.mp3"
      ],
      1 => [
        "file" => "test-song3.mp3"
      ],
      2 => [
        "file" => "test-song1.mp3"
      ]
    ], $this->mphpd->playlist("test2")->get_songs());

  }


  public function testRename(){
    $this->mphpd->queue()->clear();
    $this->mphpd->playlist("test")->load();
    $this->mphpd->playlist("test2")->save();

    $this->assertFalse($this->mphpd->playlist("test2")->rename("test"));
    $this->assertTrue($this->mphpd->playlist("test2")->rename("test3"));

    $playlists = $this->mphpd->playlists();
    sort($playlists);

    $this->assertSame([
      "test",
      "test3"
    ], $playlists);

  }


  public function testDelete(){

    $this->mphpd->queue()->clear();
    $this->mphpd->playlist("test")->load();
    $this->mphpd->playlist("test3")->save();


    $playlists = $this->mphpd->playlists();
    sort($playlists);

    $this->assertSame([
      "test",
      "test3"
    ], $playlists);

    $this->assertFalse($this->mphpd->playlist("non-existent")->delete());
    $this->assertTrue($this->mphpd->playlist("test3")->delete());

    $playlists = $this->mphpd->playlists();
    $this->assertSame([
      "test"
    ], $playlists);

  }


  public function testSave(){
    $this->mphpd->queue()->clear();
    $this->mphpd->playlist("test")->load();

    $this->assertFalse($this->mphpd->playlist("test")->save(MPD_MODE_CREATE));
    $this->assertTrue($this->mphpd->playlist("test2")->save(MPD_MODE_CREATE));
    $this->assertTrue($this->mphpd->playlist("test2")->save(MPD_MODE_APPEND));

    $this->assertSame(6, $this->mphpd->playlist("test2")->length()["songs"]);

    $this->assertTrue($this->mphpd->playlist("test2")->save(MPD_MODE_REPLACE));
    $this->assertSame(3, $this->mphpd->playlist("test2")->length()["songs"]);
  }


  public function testSearch(){
    // @ToDo, dont be so lazy
    $r = $this->mphpd->playlist("test")->search(new Filter("album", "==", "test songs"));
    $this->assertSame(3, count($r));
    $this->assertSame(11, count($r[0]));

    $r = $this->mphpd->playlist("test")->search(new Filter("album", "==", "test songs"), [0,2]);
    $this->assertSame(2, count($r));
  }


  public function testPlaylistlength(){
    $x = $this->mphpd->playlist("test")->length();
    $this->assertSame([
      "songs" => 3,
      "playtime" => 5598
    ], $x);
  }
}
