<?php

require_once __DIR__ . "/../src/MphpD.php";
require_once __DIR__ . "/config/config.php";

use PHPUnit\Framework\TestCase;
use FloFaber\MphpD\MphpD;
use FloFaber\MphpD\Filter;

class PlaylistTest extends TestCase
{

  private MphpD $mphpd;

  /*final public function __construct(?string $name = null, array $data = [], $dataName = '')
  {
    parent::__construct($name, $data, $dataName);
    $this->mphpd = new MphpD(MPD_CONFIG);
    $this->mphpd->connect();
  }*/

  public function setUp(): void
  {
    $this->mphpd = new MphpD(MPD_CONFIG);
    $this->mphpd->connect();

    $this->mphpd->queue()->clear();
    $this->mphpd->playlist("test")->load();
    $this->mphpd->playlist("test2")->save();
  }

  public function tearDown(): void
  {
    $this->mphpd->playlist("test2")->delete();
    $this->mphpd->disconnect();
  }

  public function testMoveSong(){

    var_dump($this->mphpd->playlist("test2")->move_song([1,3], 0));
    $this->assertEquals($this->mphpd->playlist("test2")->get_songs(), [
      0 => [
        "file" => "test-song2.mp3"
      ],
      1 => [
        "file" => "test-song3.mp3"
      ],
      2 => [
        "file" => "test-song1.mp3"
      ]
    ]);

    $this->mphpd->playlist("test2")->move_song(0, 2);
    $this->assertEquals($this->mphpd->playlist("test2")->get_songs(), [
      0 => [
        "file" => "test-song3.mp3"
      ],
      1 => [
        "file" => "test-song1.mp3"
      ],
      2 => [
        "file" => "test-song2.mp3"
      ]
    ]);


  }
}
