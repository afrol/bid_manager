<?php

namespace app\components\log;


class LogEnum
{
    const SCHEDULE = 'schedule';
    const SCHEDULE_UP = 'scheduleUP';
    const SCHEDULE_DOWN = 'scheduleDown';
    const SCHEDULE_API = 'scheduleApi';
    const SCHEDULE_ERROR = 'scheduleError';
    const SCHEDULE_PROCESSING = 'scheduleProcessing';
    const SCHEDULE_CREATE = 'scheduleCreate';
    const SCHEDULE_EMPTY = 'scheduleEmpty';
    const SCHEDULE_BID_NOT_FOUND = 'scheduleBidNotFound';
    const SCHEDULE_TEST = 'scheduleTest';

    const GREP_BID = 'grepBid';
    const GREP_GROUP = 'grepGroup';
    const GREP_CAMPAIGN = 'grepCampaign';

    const BID_LOG = 'bidLog';
}
