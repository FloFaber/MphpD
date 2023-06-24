<?php

require_once __DIR__ . "/config/config.php";
require_once __DIR__ . "/../src/MphpD.php";

use FloFaber\MphpD\MphpD;
use PHPUnit\Framework\TestCase;

class OutputTest extends TestCase
{

  private MphpD $mphpd;

  public function __construct(?string $name = null, array $data = [], $dataName = '')
  {
    $this->mphpd = new MphpD(MPD_CONFIG);
    $this->mphpd->connect();
    parent::__construct($name, $data, $dataName);
  }

  public function setUp(): void
  {
    parent::setUp(); // TODO: Change the autogenerated stub
    $outputs = $this->mphpd->outputs();
    foreach($outputs as $output){
      $this->mphpd->output($output["outputid"])->enable();
    }
  }

  public function testToggle()
  {
    $enabled = $this->mphpd->outputs()[0]["outputenabled"];
    $this->assertTrue($this->mphpd->output(0)->toggle());

    if($enabled){
      $s = 0;
    }else{
      $s = 1;
    }

    $this->assertSame($s, $this->mphpd->outputs()[0]["outputenabled"]);
  }

  public function testEnable()
  {
    $this->assertTrue($this->mphpd->output(0)->enable());
    $this->assertSame(1, $this->mphpd->outputs()[0]["outputenabled"]);
  }

  // @ToDo
  /*public function testSet()
  {
  }*/

  public function testDisable()
  {
    $this->assertTrue($this->mphpd->output(0)->disable());
    $this->assertSame(0, $this->mphpd->outputs()[0]["outputenabled"]);
  }
}
