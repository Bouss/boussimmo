<?php

namespace App\Tests\Parser;

use App\Parser\OuestFranceImmoParser;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class OuestFranceImmoParserTest extends TestCase
{
    /**
     * @var OuestFranceImmoParser
     */
    private $parser;

    public function setUp(): void
    {
        parent::setUp();

        $this->parser = new OuestFranceImmoParser($this->prophesize(LoggerInterface::class)->reveal());
    }

    public function testParseOuestFranceImmoReturnsPropertyAd(): void
    {
        $html = <<<HTML
    <a href="/immobilier/vente/appartement/nantes-44-44109/3-pieces-13162857.htm" title="Vente appartement Nantes" class="annLink  ">
        <div id="annonce_13162857" data-id="13162857">
            <img src="https://www.ouestfrance-immo.com/photo-vente-appartement-nantes-44/201/appartement-a-vendre-nantes-13162857_1_1553218506_1bce07ea0af1ba78fc5ce4aff473b525_crop_295-222_.jpg" class="annPhoto lazy" data-original="https://www.ouestfrance-immo.com/photo-vente-appartement-nantes-44/201/appartement-a-vendre-nantes-13162857_1_1553218506_1bce07ea0af1ba78fc5ce4aff473b525_crop_295-222_.jpg" alt="Vente Appartement 3 piècesNantes" style="display: block;">
            <div class="annBlocDesc">
                <h3>
                    <span class="annPrix">118 490&nbsp;€</span>
                    <span class="annTitre">Appartement 3 pièces</span>
                </h3>
                <span class="annVille">Nantes</span>
                <span class="annTexte hidden-phone">À deux pas d'Atlantis, près de la rocade ouest à vendre appartement de type 3 traversant, situé au RDC IDEAL POUR UNE PERSONNE...</span>
                <span class="annCriteres">62m² | 2 chb | 2 sdb</span>
                <span class="annDebAff">27/07/19</span>
                <div class="annInteractions hidden-phone">
                    <div class="annFavoris">
                        <span class="icon-heart-outlined" data-id="13162857"></span>
                    </div>
                    <div class="annTel" data-infos="{&quot;id&quot;:&quot;13162857&quot;,&quot;pro&quot;:&quot;1&quot;,&quot;premium&quot;:false,&quot;baisseDePrix&quot;:false,&quot;transaction&quot;:&quot;vente&quot;,&quot;categorie&quot;:&quot;agence&quot;,&quot;dep&quot;:&quot;44&quot;}" id="JsTel_13162857">
                        <span class="icon-phone"></span> <span>Voir téléphone</span>
                    </div>
                    <span class="annBtnDetail">Voir détail</span>
                </div>
            </div>
            <span class="annBlocNbPhotos">
                <span class="annNbPhotos">3 photos</span>
            </span>
        </div>
    </a>
HTML;

        $ads = $this->parser->parse($html);
        $this->assertCount(1, $ads);
        $ad = $ads[0];
        $this->assertEquals('ouestfrance-immo', $ad->getSite());
        $this->assertEquals('13162857', $ad->getExternalId());
        $this->assertEquals('/immobilier/vente/appartement/nantes-44-44109/3-pieces-13162857.htm', $ad->getUrl());
        $this->assertEquals(118490, $ad->getPrice());
        $this->assertEquals(62, $ad->getArea());
        $this->assertEquals(3, $ad->getRoomsCount());
        $this->assertEquals('Nantes', $ad->getLocation());
        $this->assertEquals('2019-07-27 12:00:00', $ad->getPublishedAt()->format('Y-m-d H:i:s'));
        $this->assertEquals('Appartement 3 pièces', $ad->getTitle());
        $this->assertEquals('À deux pas d\'Atlantis, près de la rocade ouest à vendre appartement de type 3 traversant, situé au RDC IDEAL POUR UNE PERSONNE...<br>62m² | 2 chb | 2 sdb', $ad->getDescription());
        $this->assertEquals('https://www.ouestfrance-immo.com/photo-vente-appartement-nantes-44/201/appartement-a-vendre-nantes-13162857_1_1553218506_1bce07ea0af1ba78fc5ce4aff473b525_crop_295-222_.jpg', $ad->getPhoto());
        $this->assertEquals(null, $ad->getRealEstateAgent());
    }
}
