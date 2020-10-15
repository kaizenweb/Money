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
 * USD Currency Engine
 *
 * Converts correctly for USD currency
 *
 * @author     Sven Arild Helleland
 * @company    Kaizen Web-Productions (http://www.kaizen-web.com)
 * @copyright  Copyright(C), Kaizen Web-Productions, 2004-2020, All Rights Reserved.
 * @package    Currency
 * @subpackage USD
 */
final class USD extends Engine {

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
  protected $_convert_places = 2;

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
  protected $_convert_multiplier = 100;

  /**
   * Display Precision
   *
   * How many decimal places to have when displaying the value
   *
   * @access protected
   * @var int
   */
  protected $_precision = 2;

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
  protected $_currency_iso = 'USD';

  /**
   * Currency Symbol
   *
   * @access protected
   * @var string
   */
  protected $_currency_symbol = '$';

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
}