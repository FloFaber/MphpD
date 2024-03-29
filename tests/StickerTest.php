<?php

require_once __DIR__ . "/config/config.php";
require_once __DIR__ . "/../src/MphpD.php";

use PHPUnit\Framework\TestCase;
use FloFaber\MphpD\MphpD;

class StickerTest extends TestCase
{

  private MphpD $mphpd;

  public function __construct(?string $name = null, array $data = [], $dataName = '')
  {
    parent::__construct($name, $data, $dataName);
    $this->mphpd = new MphpD(MPD_CONFIG);
    $this->mphpd->connect();
  }

  public function setUp(): void
  {
    parent::setUp(); // TODO: Change the autogenerated stub
    $this->mphpd->sticker("song", "test-song1.mp3")->set("test1", "asdf1");
    $this->mphpd->sticker("song", "test-song1.mp3")->set("test2", "asdf2");
  }

  public function tearDown(): void
  {
    parent::tearDown(); // TODO: Change the autogenerated stub
    $this->mphpd->sticker("song", "test-song1.mp3")->delete("test1");
    $this->mphpd->sticker("song", "test-song1.mp3")->delete("test2");
  }

  public function testSet()
  {
    $this->assertTrue($this->mphpd->sticker("song", "test-song1.mp3")->set("test1", "asdf"));
  }

  public function testDelete()
  {
    $this->assertFalse($this->mphpd->sticker("song", "test-song1.mp3")->delete("non-existent"));
    $this->assertTrue($this->mphpd->sticker("song", "test-song1.mp3")->delete("test1"));
  }

  public function testFind()
  {
    $r = $this->mphpd->sticker("song", "")->find("test1");
    $this->assertIsArray($r);

  }

  public function testList()
  {
    $r = $this->mphpd->sticker("song", "test-song1.mp3")->list();
    $this->assertIsArray($r);
    $this->assertSame([
      "test1" => "asdf1",
      "test2" => "asdf2"
    ], $r);
  }

  public function testGet()
  {
    $this->assertSame("asdf1", $this->mphpd->sticker("song", "test-song1.mp3")->get("test1"));
    $this->assertSame("asdf2", $this->mphpd->sticker("song", "test-song1.mp3")->get("test2"));
  }
}
