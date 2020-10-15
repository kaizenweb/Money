<?php
/**
 * Kaizen Framework
 *
 * @company    Kaizen Web-Productions (http://www.kaizen-web.com)
 * @copyright  Copyright(C), Kaizen Web-Productions, 2004-2020, All Rights Reserved.
 * @package    KaizenFramework
 * @subpackage Money
 */

declare(strict_types = 1);

namespace Kaizen;

/**
 * Currency Adapter
 *
 * Used together with the Money value object to handle the money, and also convert decimal money value to internal integer value
 *
 * @author     Sven Arild Helleland
 * @company    Kaizen Web-Productions (http://www.kaizen-web.com)
 * @copyright  Copyright(C), Kaizen Web-Productions, 2004-2020, All Rights Reserved.
 * @package    Currency
 * @subpackage Adapter
 */
final class Currency implements \JsonSerializable {

  /**
   * Immutable
   *
   * @access protected
   * @var bool
   */
  private $_mutable = true;

  /**
   * The Currency Instance
   *
   * @access protected
   * @var Money\Currency\Engine
   */
  private $_engine;

  /**
   * Constructor
   *
   * @param string $currency
   * @param null|int $precision
   *
   * @throws \Exception
   */
  public function __construct(string $currency) {

    if ($this->_mutable === false) {
      throw new \BadMethodCallException('Attempt to use immutable value object as mutable.');
    }

    $class = "\\Kaizen\\Money\\Currency\\{$currency}";

    if (!class_exists($class)) {
      throw new \Exception('Currency class does not exist!'); //todo change to new method type
    }

    $this->_engine = new $class();
    $this->_mutable = false;
  }

  /**
   * JSON Serialize
   *
   * When a Money instance is JSON encoded, this automatically save the information required to recreate it
   *
   * @return string
   */
  public function jsonSerialize():string {
    return $this->_engine->getIso();
  }


  /**
   * Get Currency ISO
   *
   * @return string
   */
  public function getCurrencyIso():string { //todo change to just iso?
    return $this->_engine->getIso();
  }


  /**
   * Get Display Format
   *
   * Returns the amount in a human friendly and correct display form
   *
   * @param string $internalAmount
   * @return string
   */
  public function getDisplayFormat(string $internalAmount):string {
    return $this->_engine->getDisplayFormat($internalAmount);
  }


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
    return $this->_engine->convertFrom($amount, $alreadyConverted);
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
    return $this->_engine->convertBack($internalAmount);
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
  public function convertBackToBaseAmount(string $internalAmount):string { //todo is this needed, if we do not use it anywhere????
    return $this->_engine->convertBackToBaseAmount($internalAmount);
  }


  public function getPrecision():int {
    return $this->_engine->getPrecision();
  }


  /**
   * Check If Both Currencies Are The Same
   *
   * @param Currency $currency
   * @return bool
   */
  public function confirmSame(Currency $currency):bool {
    return $this->_engine->confirmSame($currency);
  }
}