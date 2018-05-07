<?php

namespace app\components\YandexDirect;

use app\components\ApiUser;
use app\models\AdGroup;
use app\models\CampaignSearch;
use app\models\Enum\AdGroupTypesEnum;
use app\models\Enum\StatusEnum;
use Biplane\YandexDirect\Api\V5\Contract\AdGroupGetItem;
use stdClass;

class AdGroupService extends ModelBase
{

    const EVENT_PROCESSING = 'event_' . self::class;

    /**
     * @var array
     */
    private $types;

    /**
     * @var array
     */
    private $status;


    public function init()
    {
        parent::init();

        $this->status = StatusEnum::findAll();
        $this->types = AdGroupTypesEnum::findAll();
    }

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

        $model = new AdGroup(['scenario' => AdGroup::SCENARIO_SAVE]);

        /** @var ApiUser $client */
        foreach ($clientList as $client) {
            $this->yandex->setCurrentClient($client);
            $page = 0;

            do {
                $this->event->info = 'Process token id = ' . $client->getTokenId() . ', page = ' . $page;
                $this->trigger(self::EVENT_PROCESSING, $this->event);

                $campaignIds = CampaignSearch::findByTokenId($client->getTokenId(), $page++)->column();
                if ($campaignIds) {
                    $adGroups = $this->yandex->getAdGroups($campaignIds);

                    $this->event->info = 'Get groups amount = ' . count($adGroups);
                    $this->trigger(self::EVENT_PROCESSING, $this->event);

                    $rows = $this->adapterData($adGroups);
                    $numberRows += $model->insertIgnore($rows);

                    $this->event->info = 'Save models amount = ' . $numberRows;
                    $this->trigger(self::EVENT_PROCESSING, $this->event);
                }

            } while ($campaignIds || $page > $limitRecurs);
        }

        return $numberRows;
    }

    /**
     * @param int $campaignId
     * @return array
     * @throws \Exception
     */
    public function getDataByCampaign(int $campaignId)
    {
        $clientList = $this->yandex->getClients();
        if (!$clientList) {
            throw new \Exception('Empty Yandex client');
        }

        $campaign = CampaignSearch::findOne(['campaign_id' => $campaignId]);
        if (!$campaign) {
            throw new \Exception('Campaign not found');
        }

        $this->yandex->setCurrentClient($clientList[$campaign->api_token_id]);

        return $this->yandex->getAdGroups([$campaignId]);
    }

    /**
     * @param AdGroupGetItem[]|null $value
     * @return array
     */
    protected function adapterData(array $value = null) : array
    {
        $data = [];
        /** @var stdClass $item */
        foreach ($value as $item) {
            $data[] = [
                'User' => $item->Id,
                'campaign_id' => $item->CampaignId,
                'name' => $item->Name,
                'type_id' => $this->getTypeId($item->Type),
                'status_id' => $this->getStatusId($item->Status),
            ];
        }

        return $data;
    }

    /**
     * @param string $name
     * @return int
     */
    protected function getTypeId(string $name) : int
    {
        return array_search($name, $this->types) ?? 1;
    }

    /**
     * @param string $name
     * @return int
     */
    protected function getStatusId(string $name) : int
    {
        return array_search($name, $this->status) ?? 1;
    }
}
