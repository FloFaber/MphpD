<?php

require_once __DIR__ . "/config/config.php";
require_once __DIR__ . "/../src/MphpD.php";

use FloFaber\MphpD;
use PHPUnit\Framework\TestCase;


class PartitionTest extends TestCase
{

  private MphpD $mphpd;

  public function __construct(?string $name = null, array $data = [], $dataName = '')
  {
    $this->mphpd = new MphpD(MPD_CONFIG);
    $this->mphpd->connect();
    parent::__construct($name, $data, $dataName);
  }

  public function testCreate()
  {
    if(!in_array("test1", $this->mphpd->partitions())){
      $this->assertTrue($this->mphpd->partition("test1")->create());
    }else{
      $this->assertFalse($this->mphpd->partition("test1")->create());
    }
  }

  /**
   * @depends testCreate
   * @return void
   */
  public function testMove_output()
  {
    $r = $this->mphpd->partition("test1")->move_output(MPD_OUTPUT_NAME);
    $this->assertTrue($r);
  }

  /**
   * @depends testCreate
   * @return void
   */
  public function testSwitch()
  {
    $this->assertTrue($this->mphpd->partition("test1")->switch());
    $this->assertTrue($this->mphpd->partition("default")->switch());
  }

  /**
   * @depends testCreate
   * @return void
   */
  public function testDelete()
  {
    $this->mphpd->disconnect();
    $this->mphpd->connect();
    $this->assertTrue($this->mphpd->partition("test1")->delete());
  }
}
