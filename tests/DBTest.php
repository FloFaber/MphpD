<?php

require_once __DIR__ . "/config/config.php";
require_once __DIR__ . "/../src/mphpd.php";

use FloFaber\MphpD;
use PHPUnit\Framework\TestCase;

class DBTest extends TestCase
{

  private MphpD $mphpd;

  public function __construct(?string $name = null, array $data = [], $dataName = '')
  {
    parent::__construct($name, $data, $dataName);
    $this->mphpd = new MphpD(MPD_CONFIG);
    $this->mphpd->connect();
  }

  public function testSearch()
  {
    $r1 = $this->mphpd->db()->search(new \FloFaber\Filter("album", "==", "test songs"), "title");
    $this->assertIsArray($r1);
    $this->assertNotEmpty($r1);

    $r2 = $this->mphpd->db()->search(new \FloFaber\Filter("album", "==", "Test songs"), "title");
    $this->assertIsArray($r2);
    $this->assertSame($r1, $r2);
  }

  public function testFind()
  {
    $r = $this->mphpd->db()->find(new \FloFaber\Filter("album", "==", "test songs"), "title");
    $this->assertIsArray($r);
    $this->assertNotEmpty($r);

    $r = $this->mphpd->db()->find(new \FloFaber\Filter("album", "==", "Test songs"), "title");
    $this->assertIsArray($r);
    $this->assertEmpty($r);
  }

  public function testRead_comments()
  {
    $this->assertIsArray($this->mphpd->db()->read_comments("test-song1.mp3"));
  }

  public function testUpdate()
  {
    $u1 = $this->mphpd->db()->update("", true);
    $u2 = $this->mphpd->db()->update("", true);
    $u3 = $this->mphpd->db()->update("", true, true);

    $this->assertIsInt($u1);
    $this->assertIsInt($u2);
    $this->assertIsInt($u3);

    $this->assertSame($u1, $u2);
    $this->assertSame($u1+1, $u3);
  }

  public function testList()
  {
    $r = $this->mphpd->db()->list("artist");
    $this->assertIsArray($r);
  }

  public function testAlbumart()
  {
    $ret = $this->mphpd->db()->albumart("test-song1.mp3");
    $this->assertNotFalse($ret);
    $this->assertEquals($ret, file_get_contents(__DIR__ . "/mpd/music/cover.jpg"));
  }

  public function testCount()
  {
    $r = $this->mphpd->db()->count(new \FloFaber\Filter("artist", "==", "fictional artist"));
    $this->assertIsArray($r);
  }

  public function testRead_picture()
  {
    $this->assertNotEmpty($this->mphpd->db()->read_picture("test-song1.mp3"));
  }

  // ToDo
  /*public function testFingerprint()
  {

  }*/

  public function testLs()
  {
    $this->assertIsArray($this->mphpd->db()->ls("", false, false));
    $this->assertIsArray($this->mphpd->db()->ls("", false, true));
    $this->assertIsArray($this->mphpd->db()->ls("", true, false));
    $this->assertIsArray($this->mphpd->db()->ls("", true, true));
  }
}