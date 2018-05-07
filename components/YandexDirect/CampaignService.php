<?php

namespace app\components\YandexDirect;

use app\components\ApiUser;
use app\models\Campaign;
use app\models\CampaignState;
use app\models\CampaignType;
use Biplane\YandexDirect\Api\V5\Contract\CampaignGetItem;
use stdClass;

class CampaignService extends ModelBase
{

    const EVENT_PROCESSING = 'event_' . self::class;

    /**
     * @var integer
     */
    private $tokenId;

    /**
     * @var array
     */
    private $types;

    /**
     * @var array
     */
    private $states;


    public function init()
    {
        parent::init();

        $this->states = CampaignState::find()
            ->indexBy('state_title')
            ->asArray()
            ->all();

        $this->types = CampaignType::find()
            ->indexBy('type_title')
            ->asArray()
            ->all();
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function getData(): int
    {
        $numberRows = 0;

        $clientList = $this->yandex->getClients();
        if (!$clientList) {
            throw new \Exception('Empty Yandex client');
        }

        $model = new Campaign(['scenario' => Campaign::SCENARIO_SAVE]);

        /** @var ApiUser $client */
        foreach ($clientList as $client) {
            $this->yandex->setCurrentClient($client);
            $this->setTokenId($client->getTokenId());

            $this->event->info = 'Process token id = ' . $client->getTokenId();
            $this->trigger(self::EVENT_PROCESSING, $this->event);

            $campaigns = $this->yandex->getCampaigns();
            $this->event->info = 'Get campaigns amount = ' . count($campaigns);
            $this->trigger(self::EVENT_PROCESSING, $this->event);

            if ($campaigns) {
                $rows = $this->adapterData($campaigns);
                $numberRows += $model->insertIgnore($rows);

                $this->event->info = 'Save models amount = ' . $numberRows;
                $this->trigger(self::EVENT_PROCESSING, $this->event);
            }
        }

        return $numberRows;
    }

    /**
     * @return int
     */
    public function getTokenId(): int
    {
        return $this->tokenId;
    }

    /**
     * @param int $tokenId
     * @return self
     */
    public function setTokenId(int $tokenId): self
    {
        $this->tokenId = $tokenId;
        return $this;
    }

    /**
     * @param CampaignGetItem[]|null $value
     * @return array
     */
    protected function adapterData(array $value = null) : array
    {
        $data = [];
        /** @var stdClass $item */
        foreach ($value as $item) {
            $data[] = [
                'campaign_id' => $item->Id,
                'api_token_id' => $this->getTokenId(),
                'name' => $item->Name,
                'type_id' => $this->getTypeId($item->Type),
                'state_id' => $this->getStateId($item->State),
            ];
        }

        return $data;
    }

    /**
     * @param string $type
     * @return int
     */
    protected function getTypeId(string $type) : int
    {
        return $this->types[$type]['campaign_type_id'] ?? 1;
    }

    /**
     * @param string $state
     * @return int
     */
    protected function getStateId(string $state) : int
    {
        return $this->states[$state]['campaign_state_id'] ?? 1;
    }
}
