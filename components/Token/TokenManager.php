<?php

namespace app\components\Token;

use yii\base\Component;

class TokenManager extends Component
{

    /**
     * @var TokenInterface
     */
    public $classModel;

    /**
     * @var array
     */
    public $availableTokenIds;

    public function getTokens()
    {
        return $this->classModel::getTokens($this->availableTokenIds ?? []);
    }
}
