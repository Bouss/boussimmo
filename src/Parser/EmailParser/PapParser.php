<?php

namespace App\Parser\EmailParser;

use App\Definition\SiteEnum;
use App\Parser\AbstractParser;

class PapParser extends AbstractParser
{
    protected const SITE = SiteEnum::PAP;
    protected const SELECTOR_AD_WRAPPER = 'table[width="550"] tr:nth-child(n+3):not(:last-child)';
    protected const SELECTOR_EXTERNAL_ID = '';
    protected const SELECTOR_TITLE = '';
    protected const SELECTOR_DESCRIPTION = 'td:nth-child(2)';
    protected const SELECTOR_LOCATION = 'td:nth-child(2) b';
    protected const SELECTOR_PUBLISHED_AT = '';
    protected const SELECTOR_URL = 'a:first-child';
    protected const SELECTOR_PRICE = 'td:nth-child(2)';
    protected const SELECTOR_AREA = 'td:nth-child(2)';
    protected const SELECTOR_ROOMS_COUNT = 'td:nth-child(2)';
    protected const SELECTOR_PHOTO = 'img';
    protected const SELECTOR_REAL_AGENT_ESTATE = '';
    protected const SELECTOR_NEW_BUILD = '';
    protected const PUBLISHED_AT_FORMAT = '';
}
