<?php

namespace App\Tests\Parser\EmailParser;

use App\Parser\EmailParser\SeLogerParser;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class SeLogerParserTest extends TestCase
{
    /**
     * @var SeLogerParser
     */
    private $parser;

    public function setUp(): void
    {
        parent::setUp();

        $this->parser = new SeLogerParser($this->prophesize(LoggerInterface::class)->reveal());
    }

    public function testParseSeLogerReturnsPropertyAd(): void
    {
        $html = <<<HTML
<tr><td><a href="http://t4.al.alerteimmo.com/r/?id=h304dacb6,10333dfc,10333dff&amp;p1=www.seloger.com/739822/150483977/detail.htm?ali=1&amp;n=CPg0CWk9wA&amp;p=CCBPpWhgATqKtJ7g&amp;idannali=150483977&amp;idali=109558541&amp;cmp=AL-SLG-Classic-new[1]&amp;pvd=SLG" style="text-decoration:none;color:#000" target="_blank" data-saferedirecturl="https://www.google.com/url?q=http://t4.al.alerteimmo.com/r/?id%3Dh304dacb6,10333dfc,10333dff%26p1%3Dwww.seloger.com/739822/150483977/detail.htm?ali%3D1%26n%3DCPg0CWk9wA%26p%3DCCBPpWhgATqKtJ7g%26idannali%3D150483977%26idali%3D109558541%26cmp%3DAL-SLG-Classic-new%5B1%5D%26pvd%3DSLG&amp;source=gmail&amp;ust=1566740531273000&amp;usg=AFQjCNHATI17E1UQadsX1g9aw24NIv73Kw">
	<div>
		<div class="m_2809182883812934679column" style="width:100%;max-width:303px;display:inline-block;vertical-align:top">
			<table style="border-spacing:0;font-family:Arial,sans-serif;color:#262626" width="100%">
				<tbody>
					<tr>
						<td style="padding-right:5px;padding-left:5px;padding-top:16px">
							<a href="http://t4.al.alerteimmo.com/r/?id=h304dacb6,10333dfc,10333e00&amp;p1=www.seloger.com/739822/150483977/detail.htm?ali=1&amp;n=CPg0CWk9wA&amp;p=CCBPpWhgATqKtJ7g&amp;idannali=150483977&amp;idali=109558541&amp;cmp=AL-SLG-Classic-new[1]-image&amp;pvd=SLG" style="text-decoration:none" target="_blank" data-saferedirecturl="https://www.google.com/url?q=http://t4.al.alerteimmo.com/r/?id%3Dh304dacb6,10333dfc,10333e00%26p1%3Dwww.seloger.com/739822/150483977/detail.htm?ali%3D1%26n%3DCPg0CWk9wA%26p%3DCCBPpWhgATqKtJ7g%26idannali%3D150483977%26idali%3D109558541%26cmp%3DAL-SLG-Classic-new%5B1%5D-image%26pvd%3DSLG&amp;source=gmail&amp;ust=1566740531273000&amp;usg=AFQjCNFFdmeBrEwzsKzqv4-zfPJ1Ucf4xQ">
								<span>
									<img src="https://ci5.googleusercontent.com/proxy/54kefo9KNj8IlVMm83XudXkUG8MdT4RBQdThY9djpAwE3qQHoYcBLMKEHgwpCtuWetA6AhpYJYK5s5iixBP7Z4UaQIIdqO3Ca8Wio44Y8gUb_7xnz4sW0brBhvtqBkKrD5L-Liqh1fva21YCKL4l65lERLc=s0-d-e1-ft#http://v.seloger.com/s/crop/300x225/visuels/0/v/a/y/0vay0eg4kdehksfp1po6t8kejdo5un9ssjdimbcw0.jpg" alt="image de l'annonce" style="border-width:0;display:block;min-height:196px;min-width:281px;height:auto;width:100%" class="CToWUd" width="300" height="auto">
									</span>
								</a>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="m_2809182883812934679column" style="width:100%;max-width:303px;display:inline-block;vertical-align:top">
				<table style="border-spacing:0;font-family:sans-serif;color:#262626" width="100%">
					<tbody>
						<tr>
							<td style="padding-left:5px;padding-right:5px;padding-top:16px" valign="top">
								<table class="m_2809182883812934679contents" style="border-spacing:0;font-family:sans-serif;width:100%;font-size:14px;text-align:center;color:#000">
									<tbody>
										<tr>
											<td style="font-family:Helvetica,Arial,sans-serif;font-size:24px;line-height:30px;color:#262626" align="left">
												<a href="http://t4.al.alerteimmo.com/r/?id=h304dacb6,10333dfc,10333e01&amp;p1=www.seloger.com/739822/150483977/detail.htm?ali=1&amp;n=CPg0CWk9wA&amp;p=CCBPpWhgATqKtJ7g&amp;idannali=150483977&amp;idali=109558541&amp;cmp=AL-SLG-Classic-new[1]-desc&amp;pvd=SLG" style="text-decoration:none;color:#000" target="_blank" data-saferedirecturl="https://www.google.com/url?q=http://t4.al.alerteimmo.com/r/?id%3Dh304dacb6,10333dfc,10333e01%26p1%3Dwww.seloger.com/739822/150483977/detail.htm?ali%3D1%26n%3DCPg0CWk9wA%26p%3DCCBPpWhgATqKtJ7g%26idannali%3D150483977%26idali%3D109558541%26cmp%3DAL-SLG-Classic-new%5B1%5D-desc%26pvd%3DSLG&amp;source=gmail&amp;ust=1566740531273000&amp;usg=AFQjCNEVQBjRmvmEL9C2wIvPLkUB7DURrw">
													<b>161&nbsp;
045 €</b>
												</a>
											</td>
										</tr>
										<tr>
											<td style="font-family:Helvetica,Arial,sans-serif;font-size:16px;line-height:25px;color:#262626" align="left">
												<a href="http://t4.al.alerteimmo.com/r/?id=h304dacb6,10333dfc,10333e02&amp;p1=www.seloger.com/739822/150483977/detail.htm?ali=1&amp;n=CPg0CWk9wA&amp;p=CCBPpWhgATqKtJ7g&amp;idannali=150483977&amp;idali=109558541&amp;cmp=AL-SLG-Classic-new[1]-desc&amp;pvd=SLG" style="text-decoration:none;color:#000" target="_blank" data-saferedirecturl="https://www.google.com/url?q=http://t4.al.alerteimmo.com/r/?id%3Dh304dacb6,10333dfc,10333e02%26p1%3Dwww.seloger.com/739822/150483977/detail.htm?ali%3D1%26n%3DCPg0CWk9wA%26p%3DCCBPpWhgATqKtJ7g%26idannali%3D150483977%26idali%3D109558541%26cmp%3DAL-SLG-Classic-new%5B1%5D-desc%26pvd%3DSLG&amp;source=gmail&amp;ust=1566740531273000&amp;usg=AFQjCNFheM8R8ZEIkmSdsvTDm4hI0LAGgg">Appartement • 3 pièces • 64 m² 
													<br>Nantes (44300) 
													</a>
												</td>
											</tr>
											<tr>
												<td height="11"></td>
											</tr>
											<tr>
												<td valign="bottom" align="left">
													<table width="151" cellspacing="0" cellpadding="0" border="0">
														<tbody>
															<tr>
																<td style="border-radius:50px" width="151" height="40" bgcolor="#e00034" align="center">
																	<a href="http://t4.al.alerteimmo.com/r/?id=h304dacb6,10333dfc,10333e03&amp;p1=www.seloger.com/739822/150483977/detail.htm?ali=1&amp;n=CPg0CWk9wA&amp;p=CCBPpWhgATqKtJ7g&amp;idannali=150483977&amp;idali=109558541&amp;cmp=AL-SLG-Classic-new[1]-CTA&amp;pvd=SLG" width="151" style="font-size:16px;font-family:Arial,Helvetica,sans-serif;color:#ffffff;text-decoration:none;border-radius:50px;padding:6px 24px;border:1px solid #e00034;display:inline-block" target="_blank" data-saferedirecturl="https://www.google.com/url?q=http://t4.al.alerteimmo.com/r/?id%3Dh304dacb6,10333dfc,10333e03%26p1%3Dwww.seloger.com/739822/150483977/detail.htm?ali%3D1%26n%3DCPg0CWk9wA%26p%3DCCBPpWhgATqKtJ7g%26idannali%3D150483977%26idali%3D109558541%26cmp%3DAL-SLG-Classic-new%5B1%5D-CTA%26pvd%3DSLG&amp;source=gmail&amp;ust=1566740531273000&amp;usg=AFQjCNFb19NXAmBxLvm4oNZwHvekHPtRsw"> Voir l'annonce
</a>
																</td>
															</tr>
														</tbody>
													</table>
												</td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</a></td></tr>
HTML;

        $ads = $this->parser->parse($html);
        $this->assertCount(1, $ads);
        $ad = $ads[0];
        $this->assertEquals('seloger', $ad->getSite());
        $this->assertEquals(null, $ad->getExternalId());
        $this->assertEquals('http://t4.al.alerteimmo.com/r/?id=h304dacb6,10333dfc,10333dff&p1=www.seloger.com/739822/150483977/detail.htm?ali=1&n=CPg0CWk9wA&p=CCBPpWhgATqKtJ7g&idannali=150483977&idali=109558541&cmp=AL-SLG-Classic-new[1]&pvd=SLG', $ad->getUrl());
        $this->assertEquals(161045, $ad->getPrice());
        $this->assertEquals(64, $ad->getArea());
        $this->assertEquals(3, $ad->getRoomsCount());
        $this->assertEquals('Nantes (44300)', $ad->getLocation());
        $this->assertEquals(null, $ad->getPublishedAt());
        $this->assertEquals(null, $ad->getTitle());
        $this->assertEquals(null, $ad->getDescription());
        $this->assertEquals('https://ci5.googleusercontent.com/proxy/54kefo9KNj8IlVMm83XudXkUG8MdT4RBQdThY9djpAwE3qQHoYcBLMKEHgwpCtuWetA6AhpYJYK5s5iixBP7Z4UaQIIdqO3Ca8Wio44Y8gUb_7xnz4sW0brBhvtqBkKrD5L-Liqh1fva21YCKL4l65lERLc=s0-d-e1-ft#http://v.seloger.com/s/crop/300x225/visuels/0/v/a/y/0vay0eg4kdehksfp1po6t8kejdo5un9ssjdimbcw0.jpg', $ad->getPhoto());
        $this->assertEquals(null, $ad->getRealEstateAgent());
        $this->assertFalse($ad->isNewBuild());
    }
}
