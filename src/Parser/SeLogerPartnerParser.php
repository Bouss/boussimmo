<?php

namespace App\Parser;

use App\Enum\Provider;

class SeLogerPartnerParser extends AbstractParser
{
    protected const PROVIDER = Provider::SELOGER;

    protected const SELECTOR_AD_WRAPPER  = 'a[_label="annonce entiere"] + table';
    protected const SELECTOR_DESCRIPTION = 'tr:nth-child(3) tr:nth-child(2) a';
}
