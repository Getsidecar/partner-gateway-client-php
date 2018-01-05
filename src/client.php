<?php

namespace PartnerGateway;

use GuzzleHttp\Psr7\Request;

function newRequest($method, $uri, $options = [])
{
    return new Request($method, $uri, $options);
}

class Client
{
    /**
     * @var $endpoint_
     */
    protected $endpoint_;

    /**
     * @var $client_
     */
    protected $client_;

    /**
     * Client constructor.
     * @param $client
     * @param $endpoint
     */
    public function __construct($client, $endpoint)
    {
        $this->client_ = $client;
        $this->endpoint_ = $endpoint;
    }

    /**
     * @param $partner
     * @param $accountId
     * @return mixed
     */
    public function getCampaigns($partner, $accountId)
    {
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
    public function getCampaignAdGroups($partner, $accountId, $campaignId)
    {
        $uri = sprintf(
            "%s/v1/%s/accounts/%s/campaigns/%d/adgroups",
            $this->endpoint_,
            $partner,
            $accountId,
            $campaignId
        );
        $req = newRequest("GET", $uri);

        return $this->doRequest($req);
    }

    /**
     * @param $partner
     * @param $accountId
     * @param $adGroupId
     * @return mixed
     */
    public function getAdGroupPartitions($partner, $accountId, $adGroupId)
    {
        $uri = sprintf(
            "%s/v1/%s/accounts/%s/adgroups/%d/partitions",
            $this->endpoint_,
            $partner,
            $accountId,
            $adGroupId
        );
        $req = newRequest("GET", $uri);

        return $this->doRequest($req);
    }

    /**
     * @param $partner
     * @param $accountId
     * @param $campaign
     * @return mixed
     */
    public function getCampaignCriteria($partner, $accountId, $campaign)
    {
        $uri = sprintf("%s/v1/%s/accounts/%s/campaigns/%d/criteria", $this->endpoint_, $partner, $accountId, $campaign);
        $req = newRequest("GET", $uri);

        return $this->doRequest($req);
    }

    /**
     * @param $req
     * @return mixed
     * @throws \Exception
     */
    private function doRequest($req)
    {
        $res = $this->client_->send($req, ['http_errors' => false]);

        $pr = json_decode((string)$res->getBody(), true);
        if ($pr["status"] != 200) {
            throw new \Exception($pr["errors"][0]);
        }

        return $pr["data"];
    }
}
