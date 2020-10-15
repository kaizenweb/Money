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
 * Abstract Currency Engine
 *
 * Used together with the Money value object to handle the money, and also convert decimal money value to internal integer value
 *
 * @author     Sven Arild Helleland
 * @company    Kaizen Web-Productions (http://www.kaizen-web.com)
 * @copyright  Copyright(C), Kaizen Web-Productions, 2004-2020, All Rights Reserved.
 * @package    Currency
 * @subpackage Engine
 */
abstract class Engine {

  /**
   * Convert Multiplier
   *
   * 1 + Each convert place is one zero
   *
   * Example:
   * 2 = 100
   * 3 = 1000
   *
   * @var int
   */
  protected $_convert_multiplier;

  /**
   * Display Precision
   *
   * How many decimal places to have when displaying the value
   *
   * @var int
   */
  protected $_precision;

  /**
   * Display Decimal Divider
   *
   * @var string
   */
  protected $_decimal_divider;

  /**
   * Display Thousand Divider
   *
   * @var string
   */
  protected $_thousand_divider;

  /**
   * Currency ISO
   *
   * @var string
   */
  protected $_currency_iso;

  /**
   * Currency Symbol
   *
   * @var string
   */
  protected $_currency_symbol;

  /**
   * Get Currency ISO
   *
   * @return string
   */
  public function getIso():string {
    return $this->_currency_iso;
  }


  /**
   * Get Precision
   *
   * @return int
   */
  public function getPrecision():int {
    return $this->_precision;
  }


  /**
   * Get Display Format
   *
   * Returns the amount in a human friendly and correct display form
   *
   * @param string $internalAmount
   * @return string
   */
  abstract public function getDisplayFormat(string $internalAmount):string;


  /**
   * Convert From
   *
   * Takes the external amount, and convert it internal format
   *
   * @param string $amount
   * @param bool $alreadyConverted
   * @return string
   *
   * @throws
   */
  public function convertFrom(string $amount, bool $alreadyConverted):string {

    if (strpos($amount, '.') !== false || $alreadyConverted === false) {
      $amount = (string) ($amount * $this->_convert_multiplier);
    }

    if (strpos($amount, '.') !== false) {
      throw new \Exception('Too many decimals passed along for currency type.');
    } elseif (strlen(trim($amount)) === 0) {
      return '0';
    }

    return $amount;
  }


  /**
   * Convert Back
   *
   * Takes the internal amount, and convert it to external format
   *
   * @param string $internalAmount
   * @return string
   */
  public function convertBack(string $internalAmount):string {
    return $this->_convertBack($internalAmount);
  }


  /**
   * Convert Back To Base Amount
   *
   * Takes the internal amount, and convert it to external format usable for math operations
   * (without thousands separator)
   *
   * @param string $internalAmount
   * @return string
   */
  public function convertBackToBaseAmount(string $internalAmount):string {
    $thousand_divider = $this->_thousand_divider;
    $decimal_divider = $this->_decimal_divider;
    $this->_decimal_divider = '.';
    $this->_thousand_divider = '';

    $amount = $this->_convertBack($internalAmount);

    $this->_decimal_divider = $decimal_divider;
    $this->_thousand_divider = $thousand_divider;

    return $amount;
  }


  /**
   * Convert Back
   *
   * Takes the internal amount, and convert it to external format
   *
   * @param string $internalAmount
   * @return string
   */
  protected function _convertBack(string $internalAmount):string {
    $amount = '';
    $added_decimal = false;

    $temp = $internalAmount;

    for ($num=(strlen($temp)-1), $pass=1;$num >= 0;--$num, ++$pass) {

      $amount = $temp[$num].$amount;

      if ($this->_precision == $pass) {
        $amount = '.'.$amount;
        $added_decimal = true;
      }
    }

    //If we have yet to set the decimal point, include it
    if ($added_decimal === false) {
      $amount = '0.'.str_repeat('0', ($this->_precision - strlen($amount))).$amount;
    }

    return $this->_convertToString($amount);
  }


  /**
   * Confirm Same Currency
   *
   * @param \Kaizen\Currency $currency
   * @return bool
   */
  public function confirmSame(\Kaizen\Currency $currency):bool {

    if ($this->getIso() !== $currency->getCurrencyIso()) {
      return false;
    }

    return true;
  }


  /**
   * Convert To String
   *
   * @param string $amount
   * @return string
   */
  protected function _convertToString(string $amount):string {
    return number_format((float) $amount, $this->_precision, $this->_decimal_divider, $this->_thousand_divider);
  }
}