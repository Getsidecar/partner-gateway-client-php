<?php

namespace PartnerGateway;

use GuzzleHttp\Psr7\Request;

function newRequest($method, $uri, $options = [])
{
    return new Request($method, $uri, $options);
}

class ClientException extends \Exception {
  public function __toString() {
    return __CLASS__ . ": " . $this->message;
  }
}

class Client {
  protected $endpoint_;

  protected $client_;

  protected $maxRetries_ = 4;

    /**
     * Client constructor.
     * @param $client
     * @param $endpoint
     */
    public function __construct($client, $endpoint) {
        $this->client_ = $client;
        $this->endpoint_ = $endpoint;
    }

    /**
     * @param $partner
     * @param $accountId
     * @return mixed
     */
    public function getCampaigns($partner, $accountId) {
        $uri = sprintf("%s/v1/%s/accounts/%s/campaigns", $this->endpoint_, $partner, $accountId);
        $req = newRequest("GET", $uri);
        return $this->doRequest($req);
    }

    /**
     * @param $partner
     * @param $accountId
     * @param $campaignId
     * @return mixed
     */
    public function getCampaignAdGroups($partner, $accountId, $campaignId) {
        $uri = sprintf("%s/v1/%s/accounts/%s/campaigns/%d/adgroups", $this->endpoint_, $partner, $accountId, $campaignId);
        $req = newRequest("GET", $uri);
        return $this->doRequest($req);
    }

    /**
     * @param $partner
     * @param $accountId
     * @param $adGroupId
     * @return mixed
     */
    public function getAdGroupPartitions($partner, $accountId, $adGroupId) {
        $uri = sprintf("%s/v1/%s/accounts/%s/adgroups/%d/partitions", $this->endpoint_, $partner, $accountId, $adGroupId);
        $req = newRequest("GET", $uri);
        return $this->doRequest($req);
    }

    /**
     * @param $partner
     * @param $accountId
     * @param $campaign
     * @return mixed
     */
    public function getCampaignCriteria($partner, $accountId, $campaign) {
        $uri = sprintf("%s/v1/%s/accounts/%s/campaigns/%d/criteria", $this->endpoint_, $partner, $accountId, $campaign);
        $req = newRequest("GET", $uri);
        return $this->doRequest($req);
    }

    private function isRetryableError($code) {
        switch($code) {
        case "CONCURRENT_MODIFICATION":
        case "UNEXPECTED_INTERNAL_API_ERROR":
        case "RATE_EXCEEDED":
        case "OAUTH_TOKEN_EXPIRED":
        case "OAUTH_TOKEN_INVALID":
        case "AuthenticationTokenExpired":
        case "InvalidCredentials":
        case "InternalError":
        case "CallRateExceeded":
        case "NetErrorTemporary":
          return true;
        default:
          return false;
        }
    }

    /**
     * @param $req
     * @return mixed
     * @throws \Exception
     */
    private function doRequest($req) {
        $i = 1;
        for(;;) {
          $res = $this->client_->send($req, ['http_errors' => false]);

          $pr = json_decode((string)$res->getBody(), true);
          if ($pr["status"] == 200) {
            return $pr["data"];
          }

          if (!$this->isRetryableError($pr["code"])) {
            throw new ClientException("unretrable error code: " . $pr["errors"][0]);
          }

          if ($i >= $this->maxRetries_) {
            throw new ClientException("giving up after " . $i . "attempts, with error code: " . $pr["code"]);
          }

          sleep(2 + pow(2, $i));
          $i++;
        }
    }
}
