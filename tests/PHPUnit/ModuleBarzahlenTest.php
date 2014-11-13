<?php
/**
 * Barzahlen Payment Module (xt:Commerce 3)
 *
 * @copyright   Copyright (c) 2014 Cash Payment Solutions GmbH (https://www.barzahlen.de)
 * @author      Alexander Diebler
 * @license     http://opensource.org/licenses/GPL-2.0  GNU General Public License, version 2 (GPL-2.0)
 */

require_once('src/includes/modules/payment/barzahlen.php');

class ModuleBarzahlenTest extends PHPUnit_Framework_TestCase
{
    /**
     * Set everything that is needed for the testing up.
     */
    public function setUp()
    {
        $this->db = new db_handler;
        $this->object = new barzahlen;
    }

    /**
     * Tests non-used functions.
     */
    public function testNonUsedFunctions()
    {
        $this->assertFalse($this->object->update_status());
        $this->assertFalse($this->object->javascript_validation());
        $this->assertFalse($this->object->pre_confirmation_check());
        $this->assertFalse($this->object->confirmation());
        $this->assertFalse($this->object->process_button());
        $this->assertFalse($this->object->output_error());
        $this->assertFalse($this->object->before_process());
        $this->assertFalse($this->object->after_process());
    }

    /**
     * Tests install / remove methods.
     */
    public function testInstallAndRemove()
    {
        mysql_query("ALTER TABLE `" . TABLE_ORDERS . "` DROP `barzahlen_transaction_id`");
        mysql_query("ALTER TABLE `" . TABLE_ORDERS . "` DROP `barzahlen_transaction_state`");

        $this->object->install();
        $this->object = new barzahlen;
        $this->assertEquals('1', $this->object->check());
        $query = mysql_query("SELECT * FROM " . TABLE_CONFIGURATION);
        $this->assertEquals(13, mysql_num_rows($query));

        $this->object->remove();
        $this->object = new barzahlen;
        $this->assertEquals('0', $this->object->check());
        $query = mysql_query("SELECT * FROM " . TABLE_CONFIGURATION);
        $this->assertEquals(0, mysql_num_rows($query));
    }

    /**
     * Tests payment method selection.
     */
    public function testSelection()
    {
        $expected = array('id' => 'barzahlen', 'module' => MODULE_PAYMENT_BARZAHLEN_TEXT_TITLE);

        $output = $this->object->selection();
        unset($output['description']);

        $this->assertEquals($expected, $output);
    }

    /**
     * Tests payment method selection with an amount higher than 1000.
     */
    public function testSelectionWithTooHighAmount()
    {
        global $order;
        $order->info['total'] = 1000;
        $this->assertFalse($this->object->selection());
    }

    /**
     * Tests payment action with a valid xml answer.
     */
    public function testPaymentActionWithVaildXml()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
                <response>
                  <transaction-id>227840174</transaction-id>
                  <payment-slip-link>https://cdn.barzahlen.de/slip/227840174/c91dc292bdb8f0ba1a83c738119ef13e652a43b8a8f261cf93d3bfbf233d7da2.pdf</payment-slip-link>
                  <expiration-notice>Der Zahlschein ist 10 Tage gueltig.</expiration-notice>
                  <infotext-1><![CDATA[Text mit einem <a href="https://www.barzahlen.de" target="_blank">Link</a>]]></infotext-1>
                  <infotext-2><![CDATA[Text mit einem <a href="https://www.barzahlen.de" target="_blank">Link</a>]]></infotext-2>
                  <result>0</result>
                  <hash>dc5e9ab111eb8ba3bbef491fd61b6e3a943a0e62a4b34d0d8642e90be432b6afe93f7c3dd62117d0b260c3cb912b0948b50c87a3f3b9b8560a0d13029a0fc1c3</hash>
                </response>';

        $api = $this->getMock('Barzahlen_Api', array('_connectToApi'), array(MODULE_PAYMENT_BARZAHLEN_SHOPID, MODULE_PAYMENT_BARZAHLEN_PAYMENTKEY));
        $api->expects($this->once())
            ->method('_connectToApi')
            ->will($this->returnValue($xml));

        $this->object = $this->getMock('barzahlen', array('createApi'));
        $this->object->expects($this->once())
            ->method('createApi')
            ->will($this->returnValue($api));

        $this->object->payment_action();
        $this->assertEquals('Text mit einem <a href="https://www.barzahlen.de" target="_blank">Link</a>', $_SESSION['infotext-1']);
    }

    /**
     * Tests payment action with invalid hash.
     */
    public function testPaymentActionWithInvalidHash()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
                <response>
                  <transaction-id>227840174</transaction-id>
                  <payment-slip-link>https://cdn.barzahlen.de/slip/227840174/c91dc292bdb8f0ba1a83c738119ef13e652a43b8a8f261cf93d3bfbf233d7da2.pdf</payment-slip-link>
                  <expiration-notice>Der Zahlschein ist 10 Tage gueltig.</expiration-notice>
                  <infotext-1><![CDATA[Text mit einem <a href="https://www.barzahlen.de" target="_blank">Link</a>]]></infotext-1>
                  <infotext-2><![CDATA[Text mit einem <a href="https://www.barzahlen.de" target="_blank">Link</a>]]></infotext-2>
                  <result>0</result>
                  <hash>dc5e9ab111eb8ba3bbef491fd61b6e3a943a0e62a4b34d0d8642e90be432b6afe93f7c3dd62117d0b260c3cb912b0948b50c87a3f3b9b8560a0d13029a0fc1c4</hash>
                </response>';

        $api = $this->getMock('Barzahlen_Api', array('_connectToApi'), array(MODULE_PAYMENT_BARZAHLEN_SHOPID, MODULE_PAYMENT_BARZAHLEN_PAYMENTKEY));
        $api->expects($this->once())
            ->method('_connectToApi')
            ->will($this->returnValue($xml));

        $this->object = $this->getMock('barzahlen', array('createApi'));
        $this->object->expects($this->once())
            ->method('createApi')
            ->will($this->returnValue($api));

        $this->object->payment_action();
        $this->assertFalse(array_key_exists('infotext-1', $_SESSION));

        $_GET['payment_error'] = MODULE_PAYMENT_BARZAHLEN_TEXT_PAYMENT_ERROR;
        $this->object->get_error();
    }

    /**
     * Unset everything before the next test.
     */
    public function tearDown()
    {
        unset($this->db);
        unset($this->object);
        unset($_SESSION['infotext-1']);
    }
}
