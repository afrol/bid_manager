<?php

namespace app\components\Token;


interface TokenInterface
{

    const STATUS_ENABLED = 1;

    const STATUS_DISABLED = 2;

    const STATUS_TEST_ACCOUNT = 3;

    public static function getTokens(array $tokensIds = []);
}
