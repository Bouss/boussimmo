<?php

namespace App\Tests\Parser\WebParser;

use App\Parser\WebParser\LogicImmoParser;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class LogicImmoParserTest extends TestCase
{
    /**
     * @var LogicImmoParser
     */
    private $parser;

    public function setUp(): void
    {
        parent::setUp();

        $this->parser = new LogicImmoParser($this->prophesize(LoggerInterface::class)->reveal());
    }

    public function testParseOuestFranceImmoReturnsPropertyAd(): void
    {
        $html = <<<HTML
    <div itemscope="" itemtype="http://schema.org/ApartmentComplex" class="offer-list-item offer-list-item--exclu">
        <div id="header-offer-6895111F-CE04-CCF2-2110-26CDB92153B7" data-result="excluResult" data-position="8" data-agclogourl="https://mmf.logic-immo.com/mmf/agc/9bc/9bc4781a-e84a-8936-5954-d55f10b247fc/logo/260x160_a6952fe7-0aa4-4f66-b51a-90f4e2a62634.jpg" class="offer-block offer-block-exclu clearfix" data-epub-position="9">
            <div class="offer-picture">
                <div class="picture-wrapper">
                    <span class="pictoPhotoNextFakeBlocMea pictoPhotoNextFakeBlocMea--left"></span>
                    <span class="pictoPhotoNextFakeBlocMea pictoPhotoNextFakeBlocMea--right"></span>
    
                    <div class="thumb-link offer-link default-picture imgLiquidFill imgLiquid imgLiquid_bgSize imgLiquid_ready" data-trigger-imgliquid="1" data-imgliquid-fill="true" style="background-image: url(&quot;https://mmf.logic-immo.com/mmf/ads/photo-prop-400x267/689/c/c244d846-b89f-46ef-a786-468f81f01c18.jpg&quot;); background-size: cover; background-position: center center; background-repeat: no-repeat;">
                        <img class="lazy" src="https://mmf.logic-immo.com/mmf/ads/photo-prop-400x267/689/c/c244d846-b89f-46ef-a786-468f81f01c18.jpg" itemprop="image" alt="Vente appartement Nantes • <span class='offer-area-number'>60</span> m² environ • <span class='offer-rooms-number'>3</span> pièces" data-original="https://mmf.logic-immo.com/mmf/ads/photo-prop-400x267/689/c/c244d846-b89f-46ef-a786-468f81f01c18.jpg" style="display: none;">
                    </div>
                    <div class="flag-container">
                        <div class="cap flag flag-blue" itemtype="http://schema.org/Offer" itemscope="">
                            <span class="availability">Exclusivité</span>
                        </div>
                    </div>
                    <a class="add-to-selection grey size_11" data-myselection-id="6895111F-CE04-CCF2-2110-26CDB92153B7" data-myselection-frompage="Liste_de_Resultats" data-myselection-transaction="1" data-myselection-elementtomove="#header-offer-6895111F-CE04-CCF2-2110-26CDB92153B7" data-myselection-mea="" href="javascript:void(0)">
                        <i class="icon-heart-border icon-circle"></i>
                    </a>
                    <a class="remove-from-selection grey size_11" data-myselection-id="6895111F-CE04-CCF2-2110-26CDB92153B7" data-myselection-frompage="Liste_de_Resultats" data-myselection-transaction="1" data-myselection-mea="" data-myselection-mode="" href="javascript:void(0)" style="display:none;">
                        <i class="icon-heart-border icon-circle icon-heart-selected"></i>
                    </a>
    
                    <div class="offer-picture-more">
                        <span class="offer-picture-count">
                        <i class="li-icon--image"></i> 1/4<span> - Voir les photos</span>
                        </span>
                    </div>
                </div>
            </div>
            <div class="offer-details">
                <div class="offer-details-wrapper">
                    <div class="offer-details-price">
                        <p class="offer-price" itemscope="" itemtype="http://schema.org/Offer">
                            <span>184 400 €</span>
                        </p>
                        <p class="offer-bank mea">
                            <a href="https://logs1187.xiti.com/go.impression?xts=591236&amp;atc=PUB-[AdServer]-[WS_mensualites_quel_taux]-[Octobre_2018]-[Lien]-[LI]-[PRAchat]-[MeilleurTaux]&amp;type=AT&amp;url=https://www.meilleurtaux.com/demande-simulation/credit-immobilier/?IDAPPORTEUR=LOGIC_PR_TXT&amp;utm_source=partner&amp;utm_medium=logic-immo&amp;utm_campaign=lien-texte-pr&amp;utm_content=ci_ws_mensualites#xtor=AD-256-[CI]-[WS_mensualites_quel_taux]-[Lien-texte]-[https://www.logic-immo.com/pr]" title="Quel taux pour votre projet ?" class="size_11 red underline" target="_blank" onclick="return xt_click(this, 'C', '1', 'Lien_Partenaire_Bancaire::Lien_Partenaire_Bancaire_Liste_Annonces', 'N');" rel="nofollow">Ou 740 € / mois* <br>Quel taux pour votre projet ?</a> </p>
                    </div>
                    <div class="offer-details-caracteristik">
    
                        <meta itemprop="name" content="Appartement">
                        <meta itemprop="description" content="60 m² . 3 pièces . Nantes (44000)">
                        <a href="https://www.logic-immo.com/detail-vente-6895111f-ce04-ccf2-2110-26cdb92153b7.htm" title="Vente appartement 3 pièces Nantes 60 m²" class="offer-link" onclick="return xt_click(this, 'C', '1', 'Action_Liste_Annonces::Bloc_Annonce::Bloc_Annonce_EXCLUSIVITE::Bloc_Annonce_Type_de_Bien', 'N');" flag="isExclusiveVisibility"><span class="offer-details-type">Vente appartement</span>                                    <span class="offer-details-caracteristik--area">
                            <i class="li-icon li-icon--measuring-tape"></i>
                            <span class="offer-area-number">60</span> m²                    </span>
                                                        <span class="offer-details-caracteristik--rooms">
                            <i class="li-icon li-icon--piece"></i>
                            <span class="offer-rooms-number">3</span> p.                    </span>
                                    </a>
                        <span class="offer-details-caracteristik--bedrooms">
                            <i class="li-icon li-icon--bedroom"></i>
                            <span class="offer-rooms-number">2</span> ch. </span>
                    </div>
                    <div class="offer-details-location">
                        <div class="offer-details-location-half"><span class="offer-details-location--locality">Nantes (44000)</span></div><a href="https://www.logic-immo.com/detail-vente-6895111f-ce04-ccf2-2110-26cdb92153b7.htm" title="Saint-Clément - Chalâtres" class="offer-details-location--sector offer-sector offer-link">Saint-Clément - Chalâtres</a> </div>
                </div>
                <div class="offer-details-agencyLogo noLogo">
    
                </div>
    
                <div class="offer-details-cta clearfix">
                    <span class="logic-btn offer-details-cta--decouvrir"> Voir l'annonce</span>
                    <div class="offer-details-cta--contact">
                        <button class="logic-btn offer-contact offer-details-cta--contact-item offer-details-cta--contact-item--email js-addingMeaTrigger" data-urlcontact="modalMail" data-offerid="6895111F-CE04-CCF2-2110-26CDB92153B7" data-offertransactiontype="1" data-offerflag="isExclusiveVisibility" data-mode="" data-contactauto="" data-aeisource="LICOM-AEI-ACHAT-PR-MAIN-ANNONCE-BTCONTACT-POPINSUGG" data-width="635" data-height="700" data-xtpage="Liste_de_Resultats" data-universe="1" data-source="LICOM-DESKTOP-CONTACT-ACHAT-PR-MAIN-ANNONCE-BTCONTACT-POPINCONTACT" data-pushcontact="1" data-mapper="sale" data-campaign="CONTAGWEBAD1">
                            <i class="li-icon li-icon--email"></i>
                        </button>
                        <button class="logic-btn offer-contact offer-details-cta--contact-item offer-details-cta--contact-item--tel js-addingMeaTrigger" data-urlcontact="modalMail" data-offerid="6895111F-CE04-CCF2-2110-26CDB92153B7" data-offertransactiontype="1" data-offerflag="isExclusiveVisibility" data-mode="" data-contactauto="" data-aeisource="LICOM-AEI-ACHAT-PR-MAIN-ANNONCE-BTCONTACT-POPINSUGG" data-width="635" data-height="700" data-xtpage="Liste_de_Resultats" data-universe="1" data-source="LICOM-DESKTOP-CONTACT-ACHAT-PR-MAIN-ANNONCE-BTCONTACT-POPINCONTACT" data-pushcontact="1" data-mapper="sale" data-campaign="CONTAGWEBAD1">
                            <i class="li-icon li-icon--telephone"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="ads sas_block padding_bottom_10"></div>
    </div>
HTML;

        $ads = $this->parser->parse($html);
        $this->assertCount(1, $ads);
        $ad = $ads[0];
        $this->assertEquals('logic-immo', $ad->getSite());
        $this->assertEquals('header-offer-6895111F-CE04-CCF2-2110-26CDB92153B7', $ad->getExternalId());
        $this->assertEquals('https://www.logic-immo.com/detail-vente-6895111f-ce04-ccf2-2110-26cdb92153b7.htm', $ad->getUrl());
        $this->assertEquals(184400, $ad->getPrice());
        $this->assertEquals(60, $ad->getArea());
        $this->assertEquals(3, $ad->getRoomsCount());
        $this->assertEquals('Nantes (44000) Saint-Clément - Chalâtres', $ad->getLocation());
        $this->assertEquals(null, $ad->getPublishedAt());
        $this->assertEquals(null, $ad->getTitle());
        $this->assertEquals(null, $ad->getDescription());
        $this->assertEquals('https://mmf.logic-immo.com/mmf/ads/photo-prop-400x267/689/c/c244d846-b89f-46ef-a786-468f81f01c18.jpg', $ad->getPhoto());
        $this->assertEquals(null, $ad->getRealEstateAgent());
        $this->assertFalse($ad->isNewBuild());
    }
}
