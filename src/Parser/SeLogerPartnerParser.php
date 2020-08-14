<?php

namespace App\Parser;

use App\Enum\Provider;

class SeLogerPartnerParser extends AbstractParser
{
    protected const PROVIDER = Provider::SELOGER;

    protected const SELECTOR_AD_WRAPPER  = 'a[_label="annonce entiere"] + table';
    protected const SELECTOR_DESCRIPTION = 'tr:nth-child(3) tr:nth-child(2) a';
    protected const SELECTOR_PRICE       = 'tr:nth-child(2) .column:nth-child(1) .contents tr:nth-child(1) b';
    protected const SELECTOR_AREA        = 'tr:nth-child(2) .column:nth-child(1) .contents tr:nth-child(3) a';
    protected const SELECTOR_ROOMS_COUNT = 'tr:nth-child(2) .column:nth-child(1) .contents tr:nth-child(3) a';
}
