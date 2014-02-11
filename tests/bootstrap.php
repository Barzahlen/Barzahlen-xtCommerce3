<?php
/**
 * Barzahlen Payment Module (xt:Commerce 3)
 *
 * NOTICE OF LICENSE
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 2 of the License
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @copyright   Copyright (c) 2012 Zerebro Internet GmbH (http://www.barzahlen.de)
 * @author      Alexander Diebler
 * @license     http://opensource.org/licenses/GPL-2.0  GNU General Public License, version 2 (GPL-2.0)
 */

require_once('src/lang/german/modules/payment/barzahlen.php');
$_SESSION['language_code'] = 'de';
define('DEFAULT_LANGUAGE','de');
define('DEFAULT_CURRENCY','EUR');

define('DIR_FS_CATALOG','src/');
define('DIR_WS_CATALOG','../');
define('FILENAME_CHECKOUT_PROCESS','');
define('FILENAME_CHECKOUT_PAYMENT','');
define('DB_HOST','localhost');
define('DB_USER','xtcommerce');
define('DB_PASSWORD','xtcommerce');
define('DB_DATABASE','xtcommerce_3copy');

define('TABLE_CONFIGURATION','configuration');
define('TABLE_ORDERS','orders');
define('TABLE_ORDERS_STATUS_HISTORY','orders_status_history');
define('TABLE_ORDERS_TOTAL','orders_total');

define('MODULE_PAYMENT_BARZAHLEN_SANDBOX','True');
define('MODULE_PAYMENT_BARZAHLEN_DEBUG','True');
define('MODULE_PAYMENT_BARZAHLEN_STATUS','True');
define('MODULE_PAYMENT_BARZAHLEN_TMP_STATUS','0');
define('MODULE_PAYMENT_BARZAHLEN_NEW_STATUS','1');
define('MODULE_PAYMENT_BARZAHLEN_PAID_STATUS','2');
define('MODULE_PAYMENT_BARZAHLEN_EXPIRED_STATUS','3');
define('MODULE_PAYMENT_BARZAHLEN_ALLOWED','DE');
define('MODULE_PAYMENT_BARZAHLEN_SHOPID','10003');
define('MODULE_PAYMENT_BARZAHLEN_PAYMENTKEY','20a7e7235b2de0e0fda66ff8ae06665fb2470b69');
define('MODULE_PAYMENT_BARZAHLEN_NOTIFICATIONKEY','20bc75e9ca4b72f4b216bf623299295a5a814541');
define('MODULE_PAYMENT_BARZAHLEN_MAXORDERTOTAL','999.99');
define('MODULE_PAYMENT_BARZAHLEN_SORT_ORDER','0');

/**
 * DB-Handler
 */
class db_handler {

  /**
   * Sets up database before every test.
   */
  public function __construct() {
    exec('mysql -h' . DB_HOST . ' -u' . DB_USER . ' --password=' . DB_PASSWORD . ' ' . DB_DATABASE . '< tests/xtcommerce_3copy.sql');

    mysql_connect('localhost',DB_USER,DB_PASSWORD);
    mysql_select_db(DB_DATABASE);
  }

  /**
   * Removes all database data after a test.
   */
  public function __destruct() {

    $delete = mysql_query("TRUNCATE TABLE ". TABLE_CONFIGURATION);
    $delete = mysql_query("TRUNCATE TABLE ". TABLE_ORDERS);
    $delete = mysql_query("TRUNCATE TABLE ". TABLE_ORDERS_STATUS_HISTORY);
    $delete = mysql_query("TRUNCATE TABLE ". TABLE_ORDERS_TOTAL);
    mysql_close();

    $fh = fopen( 'src/includes/modules/payment/barzahlen.log', 'w' );
    fclose($fh);
  }
}

/**
 * gambio DB functions
 */
function xtc_db_query($query) {
  return mysql_query($query);
}
function xtc_db_num_rows($query) {
  return mysql_num_rows($query);
}
function xtc_db_fetch_array($query) {
  return mysql_fetch_array($query);
}

/**
 * other gambio methods
 */
function xtc_image($path) {
  return $path;
}
function xtc_redirect($path) {
  return $path;
}
function xtc_href_link($file, $settings, $ssl) {
  return $file;
}

/**
 * Fake order.
 */
class order {

  var $customer = array();
  var $info = array();

  function order($id) {

    switch ($id) {
      case '5':
        $this->customer['email_address'] = 'foo@bar.com';
        $this->info['total'] = '122.07';
        $this->info['currency'] = 'EUR';
        $this->customer['street_address'] = 'Musterstr. 1a';
        $this->customer['postcode'] = '12345';
        $this->customer['city'] = 'Musterstadt';
        $this->customer['country'] = array('iso_code_2' => 'DE');
      default:
        break;
    }
  }
}

global $order;
$order = new order('5');
global $insert_id;
$insert_id = '5';
?>