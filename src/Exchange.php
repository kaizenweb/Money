<?php
/**
 * Kaizen Framework
 *
 * @company    Kaizen Web-Productions (http://www.kaizen-web.com)
 * @copyright  Copyright(C), Kaizen Web-Productions, 2004-2020, All Rights Reserved.
 * @package    KaizenFramework
 */

declare(strict_types=1);

namespace Kaizen;

/**
 * @author     Sven Arild Helleland
 * @company    Kaizen Web-Productions (http://www.kaizen-web.com)
 * @copyright  Copyright(C), Kaizen Web-Productions, 2004-2020, All Rights Reserved.
 * @package    Money
 */
final class Exchange implements \JsonSerializable {

  /**
   * @var Currency
   */
  private $_currency;

  /**
   * @var Money[]
   */
  private $_rates = [];

  public function __construct(Currency $currency) {
    $this->_currency = $currency;
    $this->_rates[$currency->getCurrencyIso()] = new Money(1, $currency, false);
  }

  /**
   * JSON Serialize
   *
   * When a Money instance is JSON encoded, this automatically save the information required to recreate it
   *
   * @return array
   */
  public function jsonSerialize():array {

    return ['class' => 'Exchange'] + $this->getRates();
  }

  public function add(Money $rate, bool $override=false):void {
    $iso = $rate->getCurrency()->getCurrencyIso();

    if ($this->_currency->confirmSame($rate->getCurrency()) === true) {
      throw new \LogicException('Added the same currency as the exchange was created for...');
    }
    elseif (isset($this->_rates[$iso]) && $override === false) {
      throw new \InvalidArgumentException('Currency exchange rate already exists, use override if you want to overwrite it');
    }

    $this->_rates[$iso] = $rate;
  }

  public function getBaseCurrency():Currency {
    return $this->_currency;
  }

  public function getRate(string $currency):?Money {
    $currency = strtoupper($currency);

    if (empty($this->_rates[$currency])) {
      return null;
    }

    return $this->_rates[$currency];
  }

  public function getRates():array {
    $collection = ['currency' => $this->_currency, 'exchange' => []];

    ksort($this->_rates, SORT_STRING);

    foreach ($this->_rates as $iso => $rate) {

      if ($iso == $this->_currency->getCurrencyIso()) {
        continue 1;
      }

      $collection['exchange'][] = $rate;
    }

    return $collection;
  }

  public function convertTo(Currency $currency, Money $money):Money {

    if (empty($this->_rates[$currency->getCurrencyIso()])) {
      throw new \InvalidArgumentException('The exchange rate for this currency has not been added');
    }
    elseif ($money->getCurrency()->confirmSame($this->_currency) === false) {
      $money = $this->convertFrom($money);
    }

    $exchanged = $money->divide($this->_getExchangeRate($currency));

    return new Money($exchanged->getAmount(), $currency);
  }

  public function convertFrom(Money $money):Money {

    if (empty($this->_rates[$money->getCurrency()->getCurrencyIso()])) {
      throw new \InvalidArgumentException('The exchange rate for this currency has not been added');
    }

    $exchanged = $money->multiply($this->_getExchangeRate($money->getCurrency()));

    return new Money($exchanged->getAmount(), $this->_currency);
  }

  private function _getExchangeRate(Currency $iso) {
    return bcdiv($this->_rates[$this->_currency->getCurrencyIso()]->getAmount(), $this->_rates[$iso->getCurrencyIso()]->getAmount(), $this->_getHighestPrecision($iso));
  }

  private function _getHighestPrecision(Currency $iso) {

    if ($iso->getPrecision() > $this->_currency->getPrecision()) {
      return $iso->getPrecision();
    }

    return $this->_currency->getPrecision();
  }
}