<?php
/**
 * Barzahlen Payment Module SDK
 *
 * @copyright   Copyright (c) 2014 Cash Payment Solutions GmbH (https://www.barzahlen.de)
 * @author      Alexander Diebler
 * @license     The MIT License (MIT) - http://opensource.org/licenses/MIT
 */

class NotificationTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test that empty arrays are decleared not valid.
     *
     * @expectedException Barzahlen_Exception
     */
    public function testValidateWithEmptyNotification()
    {
        $params = array();

        $notification = new Barzahlen_Notification(SHOPID, NOTIFICATIONKEY, $params);
        $notification->validate();
        $this->assertFalse($notification->isValid());
    }

    /**
     * Test that incomplete arrays are decleared not valid.
     *
     * @expectedException Barzahlen_Exception
     */
    public function testValidateWithIncompleteNotification()
    {
        $params = array('state' => 'paid',
            'transaction_id' => '5');

        $notification = new Barzahlen_Notification(SHOPID, NOTIFICATIONKEY, $params);
        $notification->validate();
        $this->assertFalse($notification->isValid());
    }

    /**
     * Test function with invalid values. (Transaction ID)
     *
     * @expectedException Barzahlen_Exception
     */
    public function testValidateWithInvalidValueTransactionId()
    {
        $params = array('state' => 'paid',
            'transaction_id' => '<hack>',
            'shop_id' => '10483',
            'customer_email' => 'foo@bar.com',
            'amount' => '24.95',
            'currency' => 'EUR',
            'order_id' => '1',
            'custom_var_0' => 'PHP SDK',
            'custom_var_1' => 'Euro 2012',
            'custom_var_2' => 'Barzahlen v.1.3.3.7',
            'hash' => 'fb4393c37919371968f786a174b5a9b7340bc7e397fc480dd0d81e97873f87303c6799e855bc0a36d8673957bf00392b5b9a23f772660e67719534f13ac6d5c1'
        );

        $notification = new Barzahlen_Notification(SHOPID, NOTIFICATIONKEY, $params);
        $notification->validate();
        $this->assertFalse($notification->isValid());
    }

    /**
     * Test function with invalid values. (Shop ID)
     *
     * @expectedException Barzahlen_Exception
     */
    public function testValidateWithInvalidValueShopId()
    {
        $params = array('state' => 'paid',
            'transaction_id' => '1',
            'shop_id' => '12345',
            'customer_email' => 'foo@bar.com',
            'amount' => '24.95',
            'currency' => 'EUR',
            'order_id' => '1',
            'custom_var_0' => 'PHP SDK',
            'custom_var_1' => 'Euro 2012',
            'custom_var_2' => 'Barzahlen v.1.3.3.7',
            'hash' => 'fb4393c37919371968f786a174b5a9b7340bc7e397fc480dd0d81e97873f87303c6799e855bc0a36d8673957bf00392b5b9a23f772660e67719534f13ac6d5c1'
        );

        $notification = new Barzahlen_Notification(SHOPID, NOTIFICATIONKEY, $params);
        $notification->validate();
        $this->assertFalse($notification->isValid());
    }

    /**
     * Test function with invalid values. (Amount)
     *
     * @expectedException Barzahlen_Exception
     */
    public function testValidateWithInvalidValueAmount()
    {
        $params = array('state' => 'paid',
            'transaction_id' => '1',
            'shop_id' => '10483',
            'customer_email' => 'foo@bar.com',
            'amount' => '2004.95',
            'currency' => 'EUR',
            'order_id' => '1',
            'custom_var_0' => 'PHP SDK',
            'custom_var_1' => 'Euro 2012',
            'custom_var_2' => 'Barzahlen v.1.3.3.7',
            'hash' => 'f37e091346df8f8a9dfed61772d62d1dae22bd30e159836fa1c01f21c4ce2933c0153fe66e8629601c695c3b28a6d61f20f1bfa3d66e54c362637b432e3dc265'
        );

        $notification = new Barzahlen_Notification(SHOPID, NOTIFICATIONKEY, $params);
        $notification->validate();
        $this->assertFalse($notification->isValid());
    }

    /**
     * Test function with invalid values. (Refund Transaction ID)
     *
     * @expectedException Barzahlen_Exception
     */
    public function testValidateWithInvalidValueRefundTransactionId()
    {
        $params = array('state' => 'refund_completed',
            'refund_transaction_id' => '123abc',
            'origin_transaction_id' => '1',
            'shop_id' => '10483',
            'customer_email' => 'foo@bar.com',
            'amount' => '24.95',
            'currency' => 'EUR',
            'origin_order_id' => '1',
            'custom_var_0' => 'PHP SDK',
            'custom_var_1' => 'Euro 2012',
            'custom_var_2' => 'Barzahlen v.1.3.3.7',
            'hash' => '55b3b182caf79881f5ac9a4fd7ac4f84824267fc8ac8a18dcfd25535b48a646eb28a0acf864faaff006365fd5f0480c09341930bf15dbcbe3ad27e4fa0d5c9f5',
            'page' => 'ipn/barzahlen'
        );

        $notification = new Barzahlen_Notification(SHOPID, NOTIFICATIONKEY, $params);
        $notification->validate();
        $this->assertFalse($notification->isValid());
    }

    /**
     * Test function with invalid values. (Origin Transaction ID)
     *
     * @expectedException Barzahlen_Exception
     */
    public function testValidateWithInvalidValueOriginTransactionId()
    {
        $params = array('state' => 'refund_completed',
            'refund_transaction_id' => '1',
            'origin_transaction_id' => '<iframe src="example.com">1</iframe>',
            'shop_id' => '10483',
            'customer_email' => 'foo@bar.com',
            'amount' => '24.95',
            'currency' => 'EUR',
            'origin_order_id' => '1',
            'custom_var_0' => 'PHP SDK',
            'custom_var_1' => 'Euro 2012',
            'custom_var_2' => 'Barzahlen v.1.3.3.7',
            'hash' => '55b3b182caf79881f5ac9a4fd7ac4f84824267fc8ac8a18dcfd25535b48a646eb28a0acf864faaff006365fd5f0480c09341930bf15dbcbe3ad27e4fa0d5c9f5',
            'page' => 'ipn/barzahlen'
        );

        $notification = new Barzahlen_Notification(SHOPID, NOTIFICATIONKEY, $params);
        $notification->validate();
        $this->assertFalse($notification->isValid());
    }

    /**
     * Test function with invalid hash paid notification.
     *
     * @expectedException Barzahlen_Exception
     */
    public function testValidateWithInvalidHash()
    {
        $params = array('state' => 'paid',
            'transaction_id' => '1',
            'shop_id' => '10483',
            'customer_email' => 'foo@bar.com',
            'amount' => '24.95',
            'currency' => 'EUR',
            'order_id' => '1',
            'custom_var_0' => 'PHP SDK',
            'custom_var_1' => 'Euro 2012',
            'custom_var_2' => 'Barzahlen v.1.3.3.7',
            'hash' => '85d13e7eda95276a655ef86947409f095be8ccd1736579d54a88fc9ce2ac5353964b33d8143439354ee46fa3ce0a7ea07c49429ae3bdbfeca4f2ab1990c15367'
        );

        $notification = new Barzahlen_Notification(SHOPID, NOTIFICATIONKEY, $params);
        $notification->validate();
        $this->assertFalse($notification->isValid());
    }

    /**
     * Test function with valid paid notification.
     */
    public function testValidateWithValidPaidNotification()
    {
        $params = array('state' => 'paid',
            'transaction_id' => '1',
            'shop_id' => '10483',
            'customer_email' => 'foo@bar.com',
            'amount' => '24.95',
            'currency' => 'EUR',
            'order_id' => '1',
            'custom_var_0' => 'PHP SDK',
            'custom_var_1' => 'Euro 2012',
            'custom_var_2' => 'Barzahlen v.1.3.3.7',
            'hash' => '85d13e7eda95276a655ef86947409f095be8ccd1736579d54a88fc9ce2ac5353964b33d8143439354ee46fa3ce0a7ea07c49429ae3bdbfeca4f2ab1990c15366'
        );

        $notification = new Barzahlen_Notification(SHOPID, NOTIFICATIONKEY, $params);

        $this->assertEquals(null, $notification->getState());
        $this->assertEquals(null, $notification->getTransactionId());
        $this->assertEquals(null, $notification->getShopId());

        $notification->validate();

        $this->assertTrue($notification->isValid());
        $this->assertEquals('payment', $notification->getNotificationType());
        $this->assertEquals('paid', $notification->getState());
        $this->assertEquals('1', $notification->getTransactionId());
        $this->assertEquals('10483', $notification->getShopId());
        $this->assertEquals('foo@bar.com', $notification->getCustomerEmail());
        $this->assertEquals('24.95', $notification->getAmount());
        $this->assertEquals('EUR', $notification->getCurrency());
        $this->assertEquals('1', $notification->getOrderId());
        $this->assertEquals('PHP SDK', $notification->getCustomVar0());
        $this->assertEquals('Euro 2012', $notification->getCustomVar1());
        $this->assertEquals('Barzahlen v.1.3.3.7', $notification->getCustomVar2());
        $this->assertEquals(array('PHP SDK', 'Euro 2012', 'Barzahlen v.1.3.3.7'), $notification->getCustomVar());

        $this->assertEquals($params, $notification->getNotificationArray());
    }

    /**
     * Test function with valid refund notification.
     */
    public function testValidateWithValidRefundNotification()
    {
        $params = array('state' => 'refund_completed',
            'refund_transaction_id' => '1',
            'origin_transaction_id' => '1',
            'shop_id' => '10483',
            'customer_email' => 'foo@bar.com',
            'amount' => '24.95',
            'currency' => 'EUR',
            'origin_order_id' => '1',
            'custom_var_0' => 'PHP SDK',
            'custom_var_1' => 'Euro 2012',
            'custom_var_2' => 'Barzahlen v.1.3.3.7',
            'hash' => '55b3b182caf79881f5ac9a4fd7ac4f84824267fc8ac8a18dcfd25535b48a646eb28a0acf864faaff006365fd5f0480c09341930bf15dbcbe3ad27e4fa0d5c9f5',
            'page' => 'ipn/barzahlen'
        );

        $notification = new Barzahlen_Notification(SHOPID, NOTIFICATIONKEY, $params);

        $this->assertEquals(null, $notification->getState());
        $this->assertEquals(null, $notification->getOriginTransactionId());
        $this->assertEquals(null, $notification->getShopId());

        $notification->validate();

        $this->assertTrue($notification->isValid());
        $this->assertEquals('refund', $notification->getNotificationType());
        $this->assertEquals('refund_completed', $notification->getState());
        $this->assertEquals('1', $notification->getRefundTransactionId());
        $this->assertEquals(null, $notification->getTransactionId());
        $this->assertEquals('1', $notification->getOriginTransactionId());
        $this->assertEquals('10483', $notification->getShopId());
        $this->assertEquals('foo@bar.com', $notification->getCustomerEmail());
        $this->assertEquals('24.95', $notification->getAmount());
        $this->assertEquals('EUR', $notification->getCurrency());
        $this->assertEquals(null, $notification->getOrderId());
        $this->assertEquals('1', $notification->getOriginOrderId());
        $this->assertEquals('PHP SDK', $notification->getCustomVar0());
        $this->assertEquals('Euro 2012', $notification->getCustomVar1());
        $this->assertEquals('Barzahlen v.1.3.3.7', $notification->getCustomVar2());
        $this->assertEquals(array('PHP SDK', 'Euro 2012', 'Barzahlen v.1.3.3.7'), $notification->getCustomVar());

        $this->assertEquals($params, $notification->getNotificationArray());
    }

    /**
     * Test function with valid expired notification without custom vars.
     */
    public function testValidateWithValidExpiredShortNotification()
    {
        $params = array('state' => 'expired',
            'transaction_id' => '1',
            'shop_id' => '10483',
            'customer_email' => 'foo@bar.com',
            'amount' => '24.95',
            'currency' => 'EUR',
            'order_id' => '1',
            'hash' => '48cad9d7f1f19ce612212b1717b43ca0f2e2ff2589b16d28d82a94ab6f5ade4ff69f551b1c2d9f6f8fff726609b04c91c08951d417e3c64e724f8a7722085df6'
        );

        $notification = new Barzahlen_Notification(SHOPID, NOTIFICATIONKEY, $params);
        $notification->validate();
        $this->assertTrue($notification->isValid());
    }

    /**
     * Test function with valid refund notification without custom vars and origin order id.
     */
    public function testValidateWithValidShortRefundNotification()
    {
        $params = array('state' => 'refund_completed',
            'refund_transaction_id' => '1',
            'origin_transaction_id' => '1',
            'shop_id' => '10483',
            'customer_email' => 'foo@bar.com',
            'amount' => '24.95',
            'currency' => 'EUR',
            'hash' => 'da5ec940ce1be06fab76f96aa192953db093e0997f0c5e62a5e47ebde3b1f5791daf29fe5eafeea176f07c07e470bae8d9ced710bfd90f62dd238bf621d20717',
            'page' => 'ipn/barzahlen'
        );

        $notification = new Barzahlen_Notification(SHOPID, NOTIFICATIONKEY, $params);
        $notification->validate();
        $this->assertTrue($notification->isValid());
    }
}
