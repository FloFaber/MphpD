<?php
/*
 * MphpD
 * http://mphpd.org
 *
 * Copyright (c) 2023 Florian Faber
 * http://www.flofaber.com
 */

require_once __DIR__ . "/config/config.php";
require_once __DIR__ . "/../src/MphpD.php";

use FloFaber\MphpD\MPDException;
use FloFaber\MphpD\MphpD;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(\FloFaber\MphpD\Player::class)]

class ProtocolTest extends TestCase
{

  protected MphpD $mphpd;

  protected function setUp(): void
  {
    parent::setUp();
    $this->mphpd = new MphpD(MPD_CONFIG);
    $this->mphpd->connect();
    $this->mphpd->protocol()->enable_all();
  }


  protected function tearDown(): void
  {
    parent::tearDown();
    $this->mphpd->disconnect();
    $this->mphpd->protocol()->disable_all();
  }


  public function testAvailable()
  {
    $ret = $this->mphpd->protocol()->available();
    $this->assertNotEmpty($ret);
  }


  public function testGet()
  {
    $ret = $this->mphpd->protocol()->get();
    $this->assertIsArray($ret);
  }

  public function testDisable()
  {
    $this->assertTrue($this->mphpd->protocol()->disable(["hide_playlists_in_root"]));
    $this->assertEmpty($this->mphpd->protocol()->get());
  }

  public function testEnable()
  {
    $this->assertTrue($this->mphpd->protocol()->enable(["hide_playlists_in_root"]));
    $this->assertNotEmpty($this->mphpd->protocol()->get());
  }


  public function testDisableAll()
  {
    $this->assertTrue($this->mphpd->protocol()->disable_all());
    $this->assertEmpty($this->mphpd->protocol()->get());
  }

  public function testEnableAll()
  {
    $this->assertTrue($this->mphpd->protocol()->enable_all());
    $this->assertNotEmpty($this->mphpd->protocol()->get());
  }


}
