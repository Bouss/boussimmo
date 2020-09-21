<?php

namespace App\Parser;

use App\Enum\Provider;

class SeLogerNeufParser extends AbstractParser
{
    protected const PROVIDER = Provider::SELOGER_NEUF;

    protected const SELECTOR_AD_WRAPPER = 'td[style*="padding-left:20px"] > .outer';
    protected const SELECTOR_NAME       = '.contents tr:nth-child(2) span:first-child';
    protected const SELECTOR_LOCATION   = '.contents tr:nth-child(2) span b';
}
