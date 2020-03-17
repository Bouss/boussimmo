<?php

namespace App\Service;

use App\Enum\Provider;

class ProviderService
{
    private const PROVIDER_LOGOS = [
        Provider::BIENICI => 'bienici.svg',
        Provider::FNAIM => 'fnaim.png',
        Provider::LEBONCOIN => 'leboncoin.svg',
        Provider::LOGIC_IMMO => 'logic_immo.png',
        Provider::OUESTFRANCE_IMMO => 'ouestfrance_immo.svg',
        Provider::PAP => 'pap.png',
        Provider::SELOGER => 'seloger.svg'
    ];

    /**
     * @param string $provider
     *
     * @return string
     */
    public function getLogo(string $provider): string
    {
        return self::PROVIDER_LOGOS[$provider];
    }
}
