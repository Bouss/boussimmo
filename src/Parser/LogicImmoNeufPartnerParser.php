<?php

namespace App\Parser;

use App\Enum\Provider;

class LogicImmoNeufPartnerParser extends AbstractParser
{
    protected const PROVIDER = Provider::LOGIC_IMMO_NEUF;

    protected const SELECTOR_AD_WRAPPER    = 'body > table:first-of-type tr:nth-child(6) > td > table[width="600"]';
    protected const SELECTOR_LOCATION      = 'td.mea_txt_ad3';
    protected const SELECTOR_BUILDING_NAME = 'td.mea_txt_ad1';
}
