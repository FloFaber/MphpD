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

use FloFaber\MphpD;
use PHPUnit\Framework\TestCase;

class ChannelTest extends TestCase
{

  protected MphpD $client1, $client2;

  public function __construct(?string $name = null, array $data = [], $dataName = '')
  {
    parent::__construct($name, $data, $dataName);

    $this->client1 = new MphpD(MPD_CONFIG);
    $this->client2 = new MphpD(MPD_CONFIG);

    $this->client1->connect();
    $this->client2->connect();
  }

  public function testSendmessage()
  {
    $ret = $this->client1->channel("c1")->send("test, oida");
    $this->assertFalse($ret);
    $this->assertArrayHasKey("code", $this->client1->get_last_error());
    $this->assertArrayHasKey("message", $this->client1->get_last_error());

    $this->client1->channel("c1")->subscribe();
    $ret = $this->client1->channel("c1")->send("HELLO C2");
    $this->assertTrue($ret);

    $this->client2->channel("c1")->subscribe();
    $ret = $this->client2->channel("c1")->send("HELLO C1");
    $this->assertTrue($ret);
  }

  public function testReadmessages()
  {

    $ret = $this->client1->channel("c1")->send("test, oida");
    $this->assertFalse($ret);
    $this->assertArrayHasKey("code", $this->client1->get_last_error());
    $this->assertArrayHasKey("message", $this->client1->get_last_error());

    $this->client1->channel("c1")->subscribe();
    $ret = $this->client1->channel("c1")->send("HELLO C2");
    $this->assertTrue($ret);

    $this->client2->channel("c1")->subscribe();
    $ret = $this->client2->channel("c1")->send("HELLO C1");
    $this->assertTrue($ret);

    $this->client1->channel("c2")->subscribe();
    $ret = $this->client1->channel("c2")->send("HELLO C3");
    $this->assertTrue($ret);

    $this->client2->channel("c2")->subscribe();
    $ret = $this->client2->channel("c2")->send("HELLO C4");
    $this->assertTrue($ret);

    $ret = $this->client1->channel("c1")->read();
    $this->assertEquals([ "HELLO C2", "HELLO C1" ], $ret);

    $ret = $this->client1->channel("c2")->read();
    $this->assertEquals([ "HELLO C3", "HELLO C4" ], $ret);

  }

  public function testUnsubscribe()
  {
    $ret = $this->client1->channel("non-existent")->unsubscribe();
    $this->assertFalse($ret);

    $this->assertArrayHasKey("code", $this->client1->get_last_error());
    $this->assertArrayHasKey("message", $this->client1->get_last_error());

    $this->client1->channel("test")->subscribe();
    $ret = $this->client1->channel("test")->unsubscribe();
    $this->assertTrue($ret);
  }

  public function testSubscribe()
  {
    $ret = $this->client1->channel("test")->subscribe();
    $this->assertTrue($ret);

    $ret = $this->client1->channel("test")->subscribe();
    $this->assertFalse($ret);

    $this->assertArrayHasKey("code", $this->client1->get_last_error());
    $this->assertArrayHasKey("message", $this->client1->get_last_error());
  }

  public function testChannels()
  {
    $this->client1->channel("ct1")->subscribe();
    $this->client1->channel("ct2")->subscribe();
    $this->client1->channel("ct3")->subscribe();

    $ret = $this->client1->channels();

    $this->assertEquals([ "ct1", "ct2", "ct3" ], $ret);
  }

  public function tearDown(): void
  {
    $this->client1->disconnect();
    $this->client2->disconnect();
  }

}
