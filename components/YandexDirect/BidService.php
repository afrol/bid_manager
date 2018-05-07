<?php

namespace app\components\YandexDirect;

use app\components\ApiUser;
use app\components\ServiceEvent;
use app\models\AdGroupSearch;
use app\models\Bid;
use Biplane\YandexDirect\Api\V5\Contract\BidGetItem;
use stdClass;
use yii\base\Event;

class BidService extends ModelBase
{

    const EVENT_PROCESSING = 'event_' . self::class;


    /**
     * @return int
     * @throws \Exception
     */
    public function getData() : int
    {
        $numberRows = 0;
        $limitRecurs = 1000;

        $clientList = $this->yandex->getClients();
        if (!$clientList) {
            throw new \Exception('Empty Yandex client');
        }

        $model = new Bid(['scenario' => Bid::SCENARIO_SAVE]);

        /** @var ApiUser $client */
        foreach ($clientList as $client) {
            $this->yandex->setCurrentClient($client);
            $page = 0;

            do {
                $adGroups = AdGroupSearch::findByTokenId($client->getTokenId(), $page++)->column();
                $this->event->info = 'Process token id = ' . $client->getTokenId() . ', page = ' . $page;
                $this->trigger(self::EVENT_PROCESSING, $this->event);

                if ($adGroups) {
                    $bids = $this->yandex->getBids($adGroups);

                    $this->event->info = 'Get bids amount = ' . count($bids);
                    $this->trigger(self::EVENT_PROCESSING, $this->event);

                    if ($bids) {
                        $rows = $this->adapterData($bids);
                        $numberRows += $model->insertIgnore($rows);
                    }

                    $this->event->info = 'Save models amount = ' . $numberRows;
                    $this->trigger(self::EVENT_PROCESSING, $this->event);
                }

            } while ($adGroups || $page > $limitRecurs);
        }

        return $numberRows;
    }

    /**
     * @param int $groupId
     * @return array
     * @throws \Exception
     */
    public function checkedBid(int $groupId)
    {
        $tokenId = 0;
        if ($model = Bid::findOne(['ad_group_id' => $groupId])) {
            $tokenId = $model->group->campaign->api_token_id;
        }

        $clientList = $this->yandex->getClients();

        $bid = function ($client) use ($groupId) {
            $this->yandex->setCurrentClient($client);
            $bids = $this->yandex->getBids([$groupId]);
            return $this->adapterData($bids);
        };

        if ($tokenId && !empty($clientList[$tokenId])) {
            $response = $bid($clientList[$tokenId]);
            if ($response) {
                return $response;
            }
        }

        foreach ($clientList as $client) {
            $response = $bid($client);
            if ($response) {
                return $response;
            }
        }

        throw new \Exception('Group not found Id = ' . $groupId);
    }

    /**
     * @param  BidGetItem[]|null $value
     * @return array
     */
    protected function adapterData(array $value = null) : array
    {
        $data = [];
        /** @var stdClass $item */
        foreach ($value as $item) {
            $data[] = [
                'ad_group_id' => $item->AdGroupId,
                'campaign_id' => $item->CampaignId,
                'bid' => $item->Bid ?? null,
                'context_bid' => $item->ContextBid ?? null,
                'min_search_price' => $item->MinSearchPrice ?? null,
                'current_search_price' => $item->CurrentSearchPrice ?? null,
            ];
        }

        return $data;
    }
}
