<?php

namespace PartnerGatewayTest;

require __DIR__ . '/../vendor/autoload.php';

use PartnerGateway\Client;
use GuzzleHttp\Client as HttpClient;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase {

  public function testClient() {
    $httpClient = new HttpClient();
    $client = new Client($httpClient, "http://localhost:8086");

    try {
      $ret = $client->getCampaigns("bing", "36003133");
    }catch(\Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    }

    $adgroups = $client->getCampaignAdGroups("bing", "36003133", 73230359);
    echo var_dump($adgroups);

    $parts = $client->getAdGroupPartitions("bing", "36003133", 4808704983);
    echo var_dump($parts);
  }
}

