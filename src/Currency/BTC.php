<?php
/**
 * Kaizen Framework
 *
 * @company    Kaizen Web-Productions (http://www.kaizen-web.com)
 * @copyright  Copyright(C), Kaizen Web-Productions, 2004-2020, All Rights Reserved.
 * @package    KaizenFramework
 */

declare(strict_types = 1);

namespace Kaizen\Money\Currency;

/**
 * Bitcoin Currency Engine
 *
 * Converts correctly for Bitcoin currency
 *
 * @author     Sven Arild Helleland
 * @company    Kaizen Web-Productions (http://www.kaizen-web.com)
 * @copyright  Copyright(C), Kaizen Web-Productions, 2004-2020, All Rights Reserved.
 * @package    Currency
 * @subpackage Bitcoin
 */
final class BTC extends Engine {

  /**
   * Convert Places
   *
   * How many decimal places we should remove from the number
   *
   * Note. This should be with as many decimal places the system can receive
   *
   * @access protected
   * @var int
   */
  protected $_convert_places = 8;

  /**
   * Convert Multiplier
   *
   * 1 + Each convert place is one zero
   *
   * Example:
   * 2 = 100
   * 3 = 1000
   *
   * @access protected
   * @var int
   */
  protected $_convert_multiplier = 100000000;

  /**
   * Display Precision
   *
   * How many decimal places to have when displaying the value
   *
   * @access protected
   * @var int
   */
  protected $_precision = 8;

  /**
   * Display Decimal Divider
   *
   * @access protected
   * @var string
   */
  protected $_decimal_divider = '.';

  /**
   * Display Thousand Divider
   *
   * @access protected
   * @var string
   */
  protected $_thousand_divider = ',';

  /**
   * Currency ISO
   *
   * @access protected
   * @var string
   */
  protected $_currency_iso = 'BTC';

  /**
   * Currency Symbol
   *
   * @access protected
   * @var string
   */
  protected $_currency_symbol = "\xC9\x83";

  /**
   * Get Display Format
   *
   * Returns the amount in a human friendly and correct display form
   *
   * @param string $internalAmount
   * @return string
   */
  public function getDisplayFormat(string $internalAmount):string {
    return $this->_currency_symbol.$this->convertBack($internalAmount);
  }


  /**
   * Convert To String
   *
   * @access protected
   * @param string $amount
   * @return string
   */
  protected function _convertToString(string $amount):string {
    $amount = number_format((float) $amount, $this->_precision, $this->_decimal_divider, $this->_thousand_divider);
    $strip = 0;

    for ($num=(strlen($amount)-1);$num >= 0;--$num) {

      if ($amount[$num] != '0') {
        break 1;
      }

      ++$strip;
    }

    if (empty($strip)) {
      return $amount;
    }

    //Strip the zeroes and verify if we need to remove the comma separator as well
    $amount = substr($amount, 0, -$strip);

    if ($amount[-1] == '.') {
      $amount = rtrim($amount, '.');
    }

    return $amount;
  }
}