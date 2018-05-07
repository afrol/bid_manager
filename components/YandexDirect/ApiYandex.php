<?php

namespace app\components\YandexDirect;

use app\components\ApiUser;
use app\components\Token\TokenManager;
use app\models\ApiToken;
use Biplane\YandexDirect\Api\V5\AdGroups;
use Biplane\YandexDirect\Api\V5\Bids;
use Biplane\YandexDirect\Api\V5\Contract\AdGroupFieldEnum;
use Biplane\YandexDirect\Api\V5\Contract\AdGroupsSelectionCriteria;
use Biplane\YandexDirect\Api\V5\Contract\BidFieldEnum;
use Biplane\YandexDirect\Api\V5\Contract\BidSetItem;
use Biplane\YandexDirect\Api\V5\Contract\BidsSelectionCriteria;
use Biplane\YandexDirect\Api\V5\Contract\CampaignsSelectionCriteria;
use Biplane\YandexDirect\Api\V5\Contract\CheckCampaignsRequest;
use Biplane\YandexDirect\Api\V5\Contract\CheckDictionariesRequest;
use Biplane\YandexDirect\Api\V5\Contract\GetAdGroupsRequest;
use Biplane\YandexDirect\Api\V5\Contract\GetBidsRequest;
use Biplane\YandexDirect\Api\V5\Contract\GetCampaignsRequest;
use Biplane\YandexDirect\Api\V5\Contract\LimitOffset;
use Biplane\YandexDirect\Api\V5\Contract\SetBidsRequest;
use Biplane\YandexDirect\Api\V5\Contract\StatusEnum;
use Biplane\YandexDirect\Api\V5\Contract\CampaignFieldEnum;
use Exception;
use yii\base\Component;

class ApiYandex extends Component
{

    /**
     * @var ApiUser
     */
    public $client;

    /**
     * @var ApiUser[]
     */
    private $clients;

    /**
     * @var ApiToken
     */
    private $tokens;

    /**
     * @var string
     */
    public $token;

    /**
     * @var string
     */
    public $login;

    /**
     * @var array
     */
    public $soapOptions;

    /**
     * @var bool
     */
    public $sandbox;


    public function init()
    {
        parent::init();

        /** @var  TokenManager */
        $this->tokens = \Yii::$app->token->getTokens();

        $this->createClient();
    }

    private function createClient()
    {
        /** @var  ApiToken $token */
        foreach ($this->tokens as $token) {
            $this->clients[$token->api_token_id] = (new ApiUser([
                'access_token' => $token->token,
                'locale' => ApiUser::LOCALE_RU,
                'sandbox' => $this->sandbox ?? false,
            ]))->setTokenId($token->api_token_id);
        }

        return $this->clients;
    }

    /**
     * @return ApiUser[]
     */
    public function getClients()
    {
        return $this->clients;
    }

    /**
     * @param ApiUser $client
     */
    public function setCurrentClient(ApiUser $client)
    {
        $this->client = $client;
    }

    /**
     * @return array
     */
    public function getCampaigns()
    {
        $results = [];

        $criteria = CampaignsSelectionCriteria::create();

        $payload = GetCampaignsRequest::create()
            ->setSelectionCriteria($criteria)
            ->setFieldNames([
                CampaignFieldEnum::ID,
                CampaignFieldEnum::NAME,
                CampaignFieldEnum::TYPE,
                CampaignFieldEnum::STATE,
            ]);

        /** @var \stdClass $response */
        $response = $this->client->getCampaignsService()->get($payload);

        if ($response && isset($response->Campaigns)) {
            $results = $response->Campaigns;
        }

        return $results;
    }

    /**
     * @param array $campaignIds
     * @return array
     * @throws \Exception
     */
    public function getAdGroups(array $campaignIds)
    {
        if (!$campaignIds) {
            return [];
        }

        $criteria = AdGroupsSelectionCriteria::create()
            ->setCampaignIds($campaignIds)
            ->setStatuses([StatusEnum::ACCEPTED]);

        $payload = GetAdGroupsRequest::create()
            ->setSelectionCriteria($criteria)
            ->setFieldNames([
                AdGroupFieldEnum::ID,
                AdGroupFieldEnum::NAME,
                AdGroupFieldEnum::CAMPAIGN_ID,
                AdGroupFieldEnum::STATUS,
                AdGroupFieldEnum::TYPE,
            ]);

        $recursiveResponse = function (AdGroups $service, &$offset = 0) use ($payload) {
            $result = [];

            do {
                if ($offset) {
                    $payload = $payload->setPage(
                        LimitOffset::create()
                            ->setOffset($offset)
                    );
                }

                /** @var \stdClass $response */
                $response = $service->get($payload);
                if ($response && isset($response->AdGroups)) {
                    $result = array_merge($result, $response->AdGroups);
                    $offset = $response->LimitedBy ?? 0;
                }

            } while ($offset > 0);

            return $result;
        };

        return $recursiveResponse($this->client->getAdGroupsService());
    }

    public function getBids(array $adGroupIds)
    {
        if (!$adGroupIds) {
            return [];
        }

        $criteria = BidsSelectionCriteria::create()
            ->setAdGroupIds($adGroupIds);

        $payload = GetBidsRequest::create()
            ->setSelectionCriteria($criteria)
            ->setFieldNames([
                BidFieldEnum::CAMPAIGN_ID,
                BidFieldEnum::AD_GROUP_ID,
                BidFieldEnum::BID,
                BidFieldEnum::CONTEXT_BID,
                BidFieldEnum::CURRENT_SEARCH_PRICE,
                BidFieldEnum::MIN_SEARCH_PRICE,
            ]);

        $recursiveResponse = function (Bids $service, &$offset = 0) use ($payload) {
            $result = [];

            do {
                if ($offset) {
                    $payload = $payload->setPage(
                        LimitOffset::create()
                            ->setOffset($offset)
                    );
                }

                /** @var \stdClass $response */
                $response = $service->get($payload);
                if ($response && isset($response->Bids)) {
                    $result = array_merge($result, $response->Bids);
                    $offset = $response->LimitedBy ?? 0;
                }

            } while ($offset > 0);

            return $result;
        };

        return $recursiveResponse($this->client->getBidsService());
    }

    /**
     * @param BidSetItem[] $bidItems
     * @return array
     * @throws Exception
     */
    public function setBids(array $bidItems)
    {
        $payload = SetBidsRequest::create()
            ->setBids($bidItems);

        /** @var \stdClass $response */
        $response = $this->client
            ->getBidsService()
            ->set($payload);

        if (isset($response->SetResults->Errors)) {
            throw new Exception($response->SetResults->Errors->Message, $response->SetResults->Errors->Code);
        }

        return $response->SetResults ? array_column($response->SetResults, 'AdGroupId') : [];
    }
}
