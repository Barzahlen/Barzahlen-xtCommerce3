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

class BZ_Notification {

  var $requiredAttributes = array('state','transaction_id','shop_id','customer_email','amount',
                                  'currency','hash'); //!< all necessary attributes for a valid notification

  /**
   * Checks the received data step by step.
   *
   * @param array $receivedGet received get array
   * @return FALSE if an error occurred
   * @return array with received information which where validated
   */
  function checkReceivedData(array $receivedGet) {

    if(!$this->checkExistenceForAllAttributes($receivedGet)) {
      return false;
    }

    if(!$this->checkAttributeValues($receivedGet)) {
      return false;
    }

    return true;
  }

  /**
   * Checks that all necessary attributes are set in the array.
   *
   * @param array $expected the expected values
   * @param array $testArray array that should be tested
   * @return FALSE if at least one attribute is missing
   * @return TRUE if all attributes are set
   */
  function checkExistenceForAllAttributes(array $receivedGet) {

    foreach($this->requiredAttributes as $attribute) {
      if(!array_key_exists($attribute, $receivedGet)) {
        $this->_bzLog('model/notification: At least the following attribute is missing: ' .
                      $attribute . ' - ' . serialize($receivedGet));
        return false;
      }
    }
    return true;
  }

  /**
   * Checks that all attribute values are valid.
   *
   * @param array $receivedGet array that should be tested
   * @return FALSE if at least one attribute is missing
   * @return TRUE if all attributes are set
   */
  function checkAttributeValues(array $receivedGet) {

    if(!preg_match('/^\d+$/', $receivedGet['transaction_id'])) {
      $this->_bzLog('model/notification: Transaction id is no valid value - ' . serialize($receivedGet));
      return false;
    }

    if(array_key_exists('order_id', $receivedGet) && !preg_match('/^\d+$/', $receivedGet['order_id'])) {
      $this->_bzLog('model/notification: Order id is no valid value - ' . serialize($receivedGet));
      return false;
    }

    if($receivedGet['currency'] != substr($receivedGet['currency'], 0, 3)) {
      $this->_bzLog('model/notification: Currency is no valid value - ' . serialize($receivedGet));
      return false;
    }

    return true;
  }

  /**
   * Logs errors into Barzahlen log file.
   *
   * @param string $message error message
   */
  function _bzLog($message) {

    $time = date("[Y-m-d H:i:s] ");
    $logFile = DIR_FS_CATALOG . 'logfiles/barzahlen.log';

    error_log($time . $message . "\r", 3, $logFile);
  }
}
?>