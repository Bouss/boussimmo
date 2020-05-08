<?php

namespace App\Parser;

use App\Enum\Provider;

class OuestFranceImmoNeuf2Parser extends AbstractParser
{
    protected const PROVIDER = Provider::OUESTFRANCE_IMMO;
    protected const SELECTOR_AD_WRAPPER = 'table[style*="border: 1px solid #e6e6e6; float: left;"]';
    protected const SELECTOR_TITLE = '';
    protected const SELECTOR_DESCRIPTION = '';
    protected const SELECTOR_LOCATION = 'span[style="font-size: 14px;"]';
    protected const SELECTOR_PUBLISHED_AT = '';
    protected const SELECTOR_URL = 'a:first-child';
    protected const SELECTOR_PRICE = 'font[size="4"] b';
    protected const SELECTOR_AREA = '';
    protected const SELECTOR_ROOMS_COUNT = '';
    protected const SELECTOR_PHOTO = 'img';
    protected const SELECTOR_NEW_BUILD = '';
}
