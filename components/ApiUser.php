<?php

namespace app\components;

use Biplane\YandexDirect\Api\V5\Bids;
use Biplane\YandexDirect\Api\V5\AdGroups;
use Biplane\YandexDirect\User;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ApiUser extends User
{

    /**
     * @var integer
     */
    private $token_id;

    public function __construct(array $options = [], ?EventDispatcherInterface $dispatcher = null)
    {
        parent::__construct($options, $dispatcher);

        return $this;
    }

    /**
     * @return int
     */
    public function getTokenId(): int
    {
        return $this->token_id;
    }

    /**
     * @param int $token_id
     * @return self
     */
    public function setTokenId(int $token_id): self
    {
        $this->token_id = $token_id;

        return $this;
    }
}
