<?php
/**
 * Barzahlen Payment Module SDK
 *
 * @copyright   Copyright (c) 2014 Cash Payment Solutions GmbH (https://www.barzahlen.de)
 * @author      Alexander Diebler
 * @license     The MIT License (MIT) - http://opensource.org/licenses/MIT
 */

class RequestPaymentTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tests different custom variable settings.
     */
    public function testSetCustomVar()
    {
        $payment = new Barzahlen_Request_Payment('mustermann@barzahlen.de', 'Musterstr. 1a', '12345', 'Musterhausen', 'DE', '24.95');

        $payment->setCustomVar('ABC', '{{}}');
        $this->assertAttributeEquals(array('ABC', '{{}}', ''), '_customVar', $payment);

        $payment->setCustomVar('Mein Shopsystem');
        $this->assertAttributeEquals(array('Mein Shopsystem', '', ''), '_customVar', $payment);

        $payment->setCustomVar('Mein Shopsystem', 'OXID', 'eShop');
        $this->assertAttributeEquals(array('Mein Shopsystem', 'OXID', 'eShop'), '_customVar', $payment);

        $payment->setCustomVar();
        $this->assertAttributeEquals(array('', '', ''), '_customVar', $payment);
    }

    /**
     * Testing the construction of a payment request array.
     * Using minimal parameters.
     */
    public function testBuildRequestArrayWithMinimumParameters()
    {
        $payment = new Barzahlen_Request_Payment('mustermann@barzahlen.de', 'Musterstr. 1a', '12345', 'Musterhausen', 'DE', '24.95');

        $requestArray = array('shop_id' => '10483',
            'customer_email' => 'mustermann@barzahlen.de',
            'amount' => '24.95',
            'currency' => 'EUR',
            'language' => 'de',
            'customer_street_nr' => 'Musterstr. 1a',
            'customer_zipcode' => '12345',
            'customer_city' => 'Musterhausen',
            'customer_country' => 'DE',
            'hash' => '19f47aea46673bf41d1e2cd7a0290dcd24e8b7f252b867f879c096666aba59c5174f6875f97639d487ffa55b9ad38a24901a16065fb78d24da4df6c7ec75d117');

        $this->assertEquals($requestArray, $payment->buildRequestArray(SHOPID, PAYMENTKEY, 'de'));
    }

    /**
     * Testing the construction of a payment request array.
     * Using one optional parameter.
     */
    public function testBuildRequestArrayWithCurrency()
    {
        $payment = new Barzahlen_Request_Payment('mustermann@barzahlen.de', 'Musterstr. 1a', '12345', 'Musterhausen', 'DE', '24.95', 'USD');

        $requestArray = array('shop_id' => '10483',
            'customer_email' => 'mustermann@barzahlen.de',
            'amount' => '24.95',
            'currency' => 'USD',
            'language' => 'en',
            'customer_street_nr' => 'Musterstr. 1a',
            'customer_zipcode' => '12345',
            'customer_city' => 'Musterhausen',
            'customer_country' => 'DE',
            'hash' => 'f4f4d5fb728a8e579e877e6fe6b9ba1e2f1d552243405f9b75989479d41335c469aa6d7a3d2c7581a04d9afd8b015eba50af5eda26e822296864babf2376f57f');

        $this->assertEquals($requestArray, $payment->buildRequestArray(SHOPID, PAYMENTKEY, 'en'));
    }

    /**
     * Testing the construction of a payment request array.
     * Using all parameters.
     */
    public function testBuildRequestArrayWithCurrencyAndOrderId()
    {
        $payment = new Barzahlen_Request_Payment('mustermann@barzahlen.de', 'Musterstr. 1a', '12345', 'Musterhausen', 'DE', '24.95', 'EUR', '42');

        $requestArray = array('shop_id' => '10483',
            'customer_email' => 'mustermann@barzahlen.de',
            'amount' => '24.95',
            'currency' => 'EUR',
            'language' => 'de',
            'order_id' => '42',
            'customer_street_nr' => 'Musterstr. 1a',
            'customer_zipcode' => '12345',
            'customer_city' => 'Musterhausen',
            'customer_country' => 'DE',
            'hash' => '8cd5db6d18f32c486c11d9dff9f57c100b3cdfe72142f50c7eacfae1f52168e38905ad208f7a1db19756d310421ee5e83cc30181cc7bb8da1550dc19cee20d28');

        $this->assertEquals($requestArray, $payment->buildRequestArray(SHOPID, PAYMENTKEY, 'de'));
    }

    /**
     * Testing XML parsing with a valid response.
     */
    public function testParseXmlWithValidResponse()
    {
        $xmlResponse = '<?xml version="1.0" encoding="UTF-8"?>
                    <response>
                      <transaction-id>7690927</transaction-id>
                      <payment-slip-link>https://api-sandbox.barzahlen.de:904/download/2001048300000/b3fc66ebb5f60ddfaa20307c73e0db3b73c0d812c1dc7e64984c5e2d4b64799a/Zahlschein_Barzahlen.pdf</payment-slip-link>
                      <expiration-notice>Der Zahlschein ist 14 Tage gültig.</expiration-notice>
                      <infotext-1><![CDATA[Hallo <b>Welt</b>! <a href="http://www.barzahlen.de">Bar zahlen</a> Infütöxt Äinß]]></infotext-1>
                      <infotext-2><![CDATA[Hallo <i>Welt</i>! <a href="http://www.barzahlen.de?a=b&c=d">Bar zahlen</a> Infütöxt 2% & so weiter]]></infotext-2>
                      <result>0</result>
                      <hash>0ab32fef460c2136d1dfefd31271638739aa6a1fc4b9b3a4a089ad298e86451c534cb3c2d0efb3f44584593a3f652d6909a9adc9e464b3b7069e604271eea69b</hash>
                    </response>';

        $payment = new Barzahlen_Request_Payment('mustermann@barzahlen.de', 'Musterstr. 1a', '12345', 'Musterhausen', 'DE', '24.95');
        $payment->parseXml($xmlResponse, PAYMENTKEY);

        $this->assertEquals('7690927', $payment->getTransactionId());
        $this->assertEquals('https://api-sandbox.barzahlen.de:904/download/2001048300000/b3fc66ebb5f60ddfaa20307c73e0db3b73c0d812c1dc7e64984c5e2d4b64799a/Zahlschein_Barzahlen.pdf', $payment->getPaymentSlipLink());
        $this->assertEquals('Der Zahlschein ist 14 Tage gültig.', $payment->getExpirationNotice());
        $this->assertEquals('Hallo <b>Welt</b>! <a href="http://www.barzahlen.de">Bar zahlen</a> Infütöxt Äinß', $payment->getInfoText1());
        $this->assertEquals('Hallo <i>Welt</i>! <a href="http://www.barzahlen.de?a=b&c=d">Bar zahlen</a> Infütöxt 2% & so weiter', $payment->getInfoText2());
        $this->assertTrue($payment->isValid());
    }

    /**
     * Testing XML parsing with an error response.
     *
     * @expectedException Barzahlen_Exception
     */
    public function testParseXmlWithErrorResponse()
    {
        $xmlResponse = '<?xml version="1.0" encoding="UTF-8"?>
                    <response>
                      <result>10</result>
                      <error-message>shop not found</error-message>
                    </response>';

        $payment = new Barzahlen_Request_Payment('mustermann@barzahlen.de', 'Musterstr. 1a', '12345', 'Musterhausen', 'DE', '24.95');
        $payment->parseXml($xmlResponse, PAYMENTKEY);

        $this->assertFalse($payment->isValid());
    }

    /**
     * Testing XML parsing with an empty response.
     *
     * @expectedException Barzahlen_Exception
     */
    public function testParseXmlWithEmptyResponse()
    {
        $xmlResponse = '';

        $payment = new Barzahlen_Request_Payment('mustermann@barzahlen.de', 'Musterstr. 1a', '12345', 'Musterhausen', 'DE', '24.95');
        $payment->parseXml($xmlResponse, PAYMENTKEY);

        $this->assertFalse($payment->isValid());
    }

    /**
     * Testing XML parsing with an incomplete response.
     *
     * @expectedException Barzahlen_Exception
     */
    public function testParseXmlWithIncompleteResponse()
    {
        $xmlResponse = '<?xml version="1.0" encoding="UTF-8"?>
                    <response>
                      <transaction-id>7690927</transaction-id>
                      <payment-slip-link>https://api-sandbox.barzahlen.de:904/download/2001048300000/b3fc66ebb5f60ddfaa20307c73e0db3b73c0d812c1dc7e64984c5e2d4b64799a/Zahlschein_Barzahlen.pdf</payment-slip-link>
                      <expiration-notice>Der Zahlschein ist 14 Tage gültig.</expiration-notice>
                      <result>0</result>
                      <hash>5a175d4002e91f4b16758ff4b8b41ff973ad355e48e73d386195cb8605600d18e443819c4e7044ebb5853a45ff9ffe75b6868e33cc98459494b656301991c18e</hash>
                    </response>';

        $payment = new Barzahlen_Request_Payment('mustermann@barzahlen.de', 'Musterstr. 1a', '12345', 'Musterhausen', 'DE', '24.95');
        $payment->parseXml($xmlResponse, PAYMENTKEY);

        $this->assertFalse($payment->isValid());
    }

    /**
     * Testing XML parsing with an incorrect return value.
     *
     * @expectedException Barzahlen_Exception
     */
    public function testParseXmlWithInvalidResponse()
    {
        $xmlResponse = '<?xml version="1.0" encoding="UTF-8"?>
                    <response>
                      <transaction-id>7690927</transaction-id>
                      <payment-slip-link>https://api-sandbox.barzahlen.de:904/download/2001048300000/b3fc66ebb5f60ddfaa20307c73e0db3b73c0d812c1dc7e64984c5e2d4b64799a/Zahlschein_Barzahlen.pdf</payment-slip-link>
                      <expiration-notice>Der Zahlschein ist 14 Tage gültig.</expiration-notice>
                      <infotext-1><![CDATA[Hallo <b>Welt</b>! <a href="http://www.barzahlen.de">Bar zahlen</a> Infütöxt Äinß]]></infotext-1>
                      <infotext-2><![CDATA[Hallo <i>Welt</i>! <a href="http://www.barzahlen.de?a=b&c=d">Bar zahlen</a> Infütöxt 2% & so weiter]]></infotext-2>
                      <result>0</result>
                      <hash>brokenhash</hash>
                    </response>';

        $payment = new Barzahlen_Request_Payment('mustermann@barzahlen.de', 'Musterstr. 1a', '12345', 'Musterhausen', 'DE', '24.95');
        $payment->parseXml($xmlResponse, PAYMENTKEY);

        $this->assertFalse($payment->isValid());
    }

    /**
     * Testing XML parsing with an invalid xml response.
     *
     * @expectedException Barzahlen_Exception
     */
    public function testParseXmlWithInvalidXML()
    {
        $xmlResponse = '<?xml version="1.0" encoding="UTF-8"?>
                    <response>
                      <transaction-id>7690927</some-id>
                      <payment-slip-link>https://api-sandbox.barzahlen.de:904/download/2001048300000/b3fc66ebb5f60ddfaa20307c73e0db3b73c0d812c1dc7e64984c5e2d4b64799a/Zahlschein_Barzahlen.pdf</payment-slip-link>
                      <expiration-notice>Der Zahlschein ist 14 Tage gültig.</expiration-notice>
                      <infotext-1><![CDATA[Hallo <b>Welt</b>! <a href="http://www.barzahlen.de">Bar zahlen</a> Infütöxt Äinß]]></infotext-1>
                      <infotext-2><![CDATA[Hallo <i>Welt</i>! <a href="http://www.barzahlen.de?a=b&c=d">Bar zahlen</a> Infütöxt 2% & so weiter]]></infotext-2>
                      <result>0</result>
                      <hash>0ab32fef460c2136d1dfefd31271638739aa6a1fc4b9b3a4a089ad298e86451c534cb3c2d0efb3f44584593a3f652d6909a9adc9e464b3b7069e604271eea69b</hash>
                    </response>';

        $payment = new Barzahlen_Request_Payment('mustermann@barzahlen.de', 'Musterstr. 1a', '12345', 'Musterhausen', 'DE', '24.95');
        $payment->parseXml($xmlResponse, PAYMENTKEY);

        $this->assertFalse($payment->isValid());
    }

    /**
     * Tests that the right request type is returned.
     */
    public function testGetRequestType()
    {
        $payment = new Barzahlen_Request_Payment('mustermann@barzahlen.de', 'Musterstr. 1a', '12345', 'Musterhausen', 'DE', '24.95');
        $this->assertEquals('create', $payment->getRequestType());
    }

    /**
     * Tests iso convertion to utf-8 to avoid problems with iso-8859-1 encoding.
     */
    public function testIsoConvert()
    {
        $payment = new Barzahlen_Request_Payment('mustermann@barzahlen.de', 'Musterstr. 1a', '12345', 'Musterhausen', 'DE', '24.95');
        $this->assertEquals('Rübenweg 42', $payment->isoConvert('Rübenweg 42'));
        $this->assertEquals('Rübenweg 42', $payment->isoConvert(utf8_decode('Rübenweg 42')));
    }
}
