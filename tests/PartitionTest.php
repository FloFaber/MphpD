<?php

require_once __DIR__ . "/../src/mphpd.php";

use FloFaber\MphpD;
use PHPUnit\Framework\TestCase;


class PartitionTest extends TestCase
{

  private MphpD $mphpd;

  public function __construct(?string $name = null, array $data = [], $dataName = '')
  {
    $this->mphpd = new MphpD();
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
    $this->assertTrue($this->mphpd->partition("test1")->move_output("pipewire-output"));
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
