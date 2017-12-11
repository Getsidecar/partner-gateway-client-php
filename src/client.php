<?php

declare(strict_types=1);
namespace PartnerGateway;

use GuzzleHttp\Psr7\Request;

function newRequest($method, $uri, $options = []) {
  return new Request($method, $uri, $options);
}

class Client {
  protected $endpoint_;

  protected $client_;

  public function __construct($client, $endpoint) {
    $this->client_ = $client;
    $this->endpoint_ = $endpoint;
  }

  public function getCampaigns($partner, $accountId) {
    $uri = sprintf("%s/v1/%s/accounts/%s/campaigns", $this->endpoint_, $partner, $accountId);
    $req = newRequest("GET", $uri);
    return $this->doRequest($req);
  }

  public function getCampaignAdGroups($partner, $accountId, $campaignId) {
    $uri = sprintf("%s/v1/%s/accounts/%s/campaigns/%d/adgroups", $this->endpoint_, $partner, $accountId, $campaignId);
    $req = newRequest("GET", $uri);
    return $this->doRequest($req);
  }

  public function getAdGroupPartitions($partner, $accountId, $adGroupId) {
    $uri = sprintf("%s/v1/%s/accounts/%s/adgroups/%d/partitions", $this->endpoint_, $partner, $accountId, $adGroupId);
    $req = newRequest("GET", $uri);
    return $this->doRequest($req);

  }

  private function doRequest($req) {
    //$res = $this->client_->request("GET", $uri);
    $res = $this->client_->send($req, ['http_errors' => false]);

    /*
    if ($res->getStatusCode() != 200) {
      throw new Exception();
    }
     */

    $pr = json_decode((string)$res->getBody(), true);

    if ($pr["status"] != 200) {
      throw new \Exception($pr["errors"][0]);
    }

    return $pr["data"];
  }
}
