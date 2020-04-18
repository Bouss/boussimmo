<?php

namespace App\Enum;

class Provider extends AbstractEnum
{
    public const BIENICI = 'bienici';
    public const FNAIM = 'fnaim';
    public const LEBONCOIN = 'leboncoin';
    public const LOGIC_IMMO = 'logic_immo';
    public const OUESTFRANCE_IMMO = 'ouestfrance_immo';
    public const PAP = 'pap';
    public const SELOGER = 'seloger';
    public const SUPERIMMO = 'superimmo';

    // Sub-providers
    public const LOGIC_IMMO_NEUF = 'logic_immo_neuf';
    public const OUESTFRANCE_IMMO_NEUF = 'ouestfrance_immo_neuf';
    public const SELOGER_NEUF = 'seloger_neuf';
    public const SUPERIMMO_NEUF = 'superimmo_neuf';
}
