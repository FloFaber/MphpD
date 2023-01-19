<?php

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . "/../src/mphpd.php";

use FloFaber\MPDException;
use FloFaber\MphpD;
use PHPUnit\Framework\TestCase;

class ChannelTest extends TestCase
{

  protected MphpD $client1, $client2;

  public function __construct(?string $name = null, array $data = [], $dataName = '')
  {
    parent::__construct($name, $data, $dataName);

    $this->client1 = new MphpD();
    $this->client2 = new MphpD();

    $this->client1->connect();
    $this->client2->connect();
  }

  public function testSendmessage()
  {
    $ret = $this->client1->channel("c1")->send_message("test, oida");
    $this->assertFalse($ret);
    $this->assertInstanceOf(MPDException::class, $this->client1->get_error());

    $this->client1->channel("c1")->subscribe();
    $ret = $this->client1->channel("c1")->send_message("HELLO C2");
    $this->assertTrue($ret);

    $this->client2->channel("c1")->subscribe();
    $ret = $this->client2->channel("c1")->send_message("HELLO C1");
    $this->assertTrue($ret);
  }

  public function testReadmessages()
  {

    $ret = $this->client1->channel("c1")->send_message("test, oida");
    $this->assertFalse($ret);
    $this->assertInstanceOf(MPDException::class, $this->client1->get_error());

    $this->client1->channel("c1")->subscribe();
    $ret = $this->client1->channel("c1")->send_message("HELLO C2");
    $this->assertTrue($ret);

    $this->client2->channel("c1")->subscribe();
    $ret = $this->client2->channel("c1")->send_message("HELLO C1");
    $this->assertTrue($ret);

    $ret = $this->client1->channel("c1")->read_messages();
    $this->assertEquals([ "HELLO C2", "HELLO C1" ], $ret);


  }

  public function testUnsubscribe()
  {
    $ret = $this->client1->channel("non-existent")->unsubscribe();
    $this->assertFalse($ret);
    $this->assertInstanceOf(MPDException::class, $this->client1->get_error());

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

    $this->assertInstanceOf(MPDException::class, $this->client1->get_error());
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
