<?php

namespace App\Parser\EmailParser;

use App\Definition\SiteEnum;
use App\Parser\AbstractParser;

class OuestFranceImmoParser extends AbstractParser
{
    protected const SITE = SiteEnum::OUESTFRANCE_IMMO;
    protected const SELECTOR_AD_WRAPPER = '.blocAnn';
    protected const SELECTOR_EXTERNAL_ID = '';
    protected const SELECTOR_TITLE = '';
    protected const SELECTOR_DESCRIPTION = '';
    protected const SELECTOR_LOCATION = 'td[style$="padding-top: 5px;padding-bottom:0;"] div:nth-child(2) span';
    protected const SELECTOR_PUBLISHED_AT = '';
    protected const SELECTOR_URL = 'a:first-child';
    protected const SELECTOR_PRICE = 'b:first-child';
    protected const SELECTOR_AREA = 'td[style$="font-size:12px;padding-top:0"] font:nth-child(2) b';
    protected const SELECTOR_ROOMS_COUNT = 'td[style$="font-size:12px;padding-top:0"] font:nth-child(1) b';
    protected const SELECTOR_PHOTO = 'img';
    protected const SELECTOR_REAL_AGENT_ESTATE = '';
    protected const SELECTOR_NEW_BUILD = '';
    protected const PUBLISHED_AT_FORMAT = '';
}
