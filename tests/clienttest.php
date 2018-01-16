<?php

namespace PartnerGatewayTest;

require __DIR__ . '/../vendor/autoload.php';

use PartnerGateway\Client;
use PartnerGateway\ClientException;
use GuzzleHttp\Client as HttpClient;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase {

  public function zztestClient() {
    $httpClient = new HttpClient();
    $client = new Client($httpClient, "http://localhost:12345");

    try {
      $ret = $client->getCampaigns("bing", "36003133");
      echo var_dump($ret);
    }catch(\Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    }

    $adgroups = $client->getCampaignAdGroups("bing", "36003133", 73230359);
    echo var_dump($adgroups);

    $parts = $client->getAdGroupPartitions("bing", "36003133", 4808704983);
    echo var_dump($parts);

    $ccs = $client->getCampaignCriteria("bing", "93021", 283270096);
    echo var_dump($ccs);
  }

  public function testRetry() {
    $responses = [
      ["status" => 502, "code" => "CONCURRENT_MODIFICATION"],
      ["status" => 502, "code" => "NetErrorTemporary"],
      ["status" => 200, "data" => "ok"],
    ];
    $httpClient = new FakeClient($responses);
    $client = new Client($httpClient, "gateway.test");

    $res = $client->getCampaigns("partner", "accountid");

    $this->assertEquals($res, "ok");
    $this->assertEquals($httpClient->called(), 3);
  }

  public function testRetryFail() {
    $responses = [
      ["status" => 502, "code" => "CONCURRENT_MODIFICATION"],
      ["status" => 502, "code" => "NetErrorTemporary"],
      ["status" => 502, "code" => "NetErrorTemporary"],
      ["status" => 502, "code" => "NetErrorTemporary"],
      ["status" => 502, "code" => "NetErrorTemporary"],
      ["status" => 200, "data" => "ok"],
    ];
    $httpClient = new FakeClient($responses);
    $client = new Client($httpClient, "gateway.test");

    $this->expectException(ClientException::class);

    $res = $client->getCampaigns("partner", "accountid");
  }

  public function testUnretryable() {
    $responses = [
      ["status" => 502, "code" => "unretrable", "errors" => ["some error"]],
      ["status" => 200, "data" => "ok"],
    ];
    $httpClient = new FakeClient($responses);
    $client = new Client($httpClient, "gateway.test");

    $this->expectException(ClientException::class);

    $res = $client->getCampaigns("partner", "accountid");
  }
}


class FakeClientResponse {
  protected $body_;

  public function __construct($body) {
    $this->body_ = $body;
  }

  public function getBody() {
    return $this->body_;
  }
}

class FakeClient {
  protected $responses_;
  protected $call_ = 0;

  public function __construct($responses) {
    $this->responses_ = $responses;
  }

  public function send($req) {
    return new FakeClientResponse(json_encode($this->responses_[$this->call_++]));
  }

  public function called() {
    return $this->call_;
  }
}
