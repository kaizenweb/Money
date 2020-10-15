<?php
/**
 * Kaizen Framework
 *
 * @company    Kaizen Web-Productions (http://www.kaizen-web.com)
 * @copyright  Copyright(C), Kaizen Web-Productions, 2004-2020, All Rights Reserved.
 * @package    KaizenFramework
 */

declare(strict_types = 1);

namespace Kaizen;

/**
 * Immutable Money Value Object todo or call it entity?
 *
 * Used to handle all money values, using currency's smallest unit
 *
 *
 * @author     Sven Arild Helleland
 * @company    Kaizen Web-Productions (http://www.kaizen-web.com)
 * @copyright  Copyright(C), Kaizen Web-Productions, 2004-2020, All Rights Reserved.
 * @package    Money
 */
final class Money implements \JsonSerializable {

  /**
   * Immutable
   *
   * @var bool
   */
  private $_mutable = true;

  /**
   * Currency Engine
   *
   * @var Currency
   */
  private $_currency;

  /**
   * The Amount
   *
   * @var string
   */
  private $_amount;


  /**
   * Constructor
   *
   * Note. By default it assume that the amount passed along has been converted. The reason for this, is due to internally
   * this is how you will use the objects the most!
   *
   * @param int|float|string $amount
   * @param Currency $currency
   * @param bool $alreadyConverted        If the amount has already been converted into integer only format
   *
   * @throws \Exception
   */
  public function __construct($amount, Currency $currency, bool $alreadyConverted=true) {
    //todo somehow allow the precision (decimals) to be set as well...
    //todo this will make it possible to very easily do better precision if required for accounting etc...
    //todo if done, we need to make certain that the highest precision is kept if two of same currency is combined!
    //todo this can be very helpful down the road, to easily implement different degrees of accuracy...
    //todo THE MAIN issue here is we need to be able to parse this with the currency as well...!

    if ($this->_mutable === false) {
      throw new \BadMethodCallException('Attempt to use immutable value object as mutable.');
    }

    $this->_amount = $currency->convertFrom((string) $amount, $alreadyConverted);
    $this->_currency = $currency;

    $this->_mutable = false;
  }


  /**
   * JSON Serialize
   *
   * When a Money instance is JSON encoded, this automatically save the information required to recreate it
   *
   * @return array
   */
  public function jsonSerialize():array {
    return array('class' => 'Money',
                 'amount' => $this->_amount,
                 'currency' => $this->_currency->getCurrencyIso());
  }


  /**
   * To String
   *
   * Returns the amount in a human friendly and correct display form when the Money instance is printed to a string
   *
   * @return string
   */
  public function __toString():string {
    return $this->_currency->getDisplayFormat($this->_amount);
  }


  /**
   * Get Amount
   *
   * Returns the internal calculation value of the amount,
   * i.e. without decimal places
   *
   * @return string
   */
  public function getAmount():string {
    return $this->_amount;
  }


  /**
   * Get Base Amount
   *
   * This return the actual amount converted back
   * i.e. can contain decimal places
   *
   * @return string
   */
  public function getBaseAmount():string {
    return $this->_currency->convertBack($this->_amount);
  }


  /**
   * Get Currency Instance
   *
   * @return Currency
   */
  public function getCurrency():Currency {
    return $this->_currency;
  }


  /**
   * Add Amount
   *
   * @param Money $amount
   * @return Money
   */
  public function add(Money $amount):Money {
    $this->_confirmSameCurrency($amount);

    $amount = bcadd($this->_amount, $amount->getAmount(), 0);

    return $this->_newMoney($amount);
  }


  /**
   * Subtract Amount
   *
   * @param Money $amount
   * @return Money
   */
  public function subtract(Money $amount):Money {
    $this->_confirmSameCurrency($amount);
    $subtract = $amount->getAmount();

    if ($amount->greaterThanZero() === false) { //Ensure [negative] - [negative] = [more negative]
      $subtract = (string) abs($amount->getAmount());
    }

    $amount = bcsub($this->_amount, $subtract, 0);

    return $this->_newMoney($amount);
  }


  /**
   * Multiple Amount
   *
   * Note: Keep in mind on negative amounts $roundUp act accordingly!
   *
   * @param int|float|string $factor
   * @param bool $roundUp     If a possible fraction should be rounded up or down
   * @return Money
   *
   * @throws \Exception
   */
  public function multiply($factor, bool $roundUp=false):Money {

    if ($factor <= 0) {
      throw new \Exception('The factor passed cannot be negative or zero.');
    }

    $amount = bcmul($this->_amount, (string) $factor, 1);

    if ($roundUp === true) {
      return $this->_newMoney((string) ceil($amount));
    }

    return $this->_newMoney((string) floor($amount));
  }


  //todo make a add percentage???? i.e. if we do 21, it add 21 to the value?
  //todo make a remove percentage???? i.e. same as above just opposite?
  //todo make a get percentage???? i.e. get how much the percentage is of the amount

  /**
   * Divide Amount
   *
   * Note: Keep in mind on negative amounts $roundUp act accordingly!
   *
   * @param int|float|string $factor
   * @param bool $roundUp     If a possible fraction should be rounded up or down
   * @return Money
   *
   * @throws \Exception
   */
  public function divide($factor, bool $roundUp=false):Money {

    if ($factor <= 0) {
      throw new \Exception('The factor passed cannot be negative or zero.');
    }

    $amount = bcdiv($this->_amount, (string) $factor, 1);

    if ($roundUp === true) {
      return $this->_newMoney((string) ceil($amount));
    }

    return $this->_newMoney((string) floor($amount));
  }


  /**
   * Equal To
   *
   * @param Money $amount
   * @return bool
   */
  public function equals(Money $amount):bool {
    return $this->_compareTo($amount) == 0;
  }


  /**
   * Greater Than
   *
   * @param Money $amount
   * @return bool
   */
  public function greaterThan(Money $amount):bool {
    return $this->_compareTo($amount) == 1;
  }


  /**
   * Greater Than Or Equal
   *
   * @param Money $amount
   * @return boolean
   */
  public function greaterThanOrEqual(Money $amount) {
    return $this->greaterThan($amount) || $this->equals($amount);
  }


  /**
   * Greater Than Zero
   *
   * @return bool
   */
  public function greaterThanZero():bool {
    return $this->_amount > 0;
  }


  /**
   * Less Than
   *
   * @param Money $amount
   * @return bool
   */
  public function lessThan(Money $amount):bool {
    return $this->_compareTo($amount) == -1;
  }


  /**
   * Less Than Or Equal
   *
   * @param Money $amount
   * @return bool
   */
  public function lessThanOrEqual(Money $amount):bool {
    return $this->lessThan($amount) || $this->equals($amount);
  }


  /**
   * Compare The Money Object To Another
   *
   * Return:
   * -1 if less than
   *  0 if equal than
   *  1 if greater than
   *
   * @access private
   * @param Money $money
   * @return int
   */
  private function _compareTo(Money $money):int {
    $this->_confirmSameCurrency($money);

    return $this->_amount <=> $money->getAmount();
  }


  /**
   * Confirm That The Currency Is The Same
   *
   * @param Money $money
   * @return void
   *
   * @throws \Exception
   */
  private function _confirmSameCurrency(Money $money):void {

    if ($this->getCurrency()->confirmSame($money->getCurrency()) === false) {
      throw new \Exception('The currency on the money objects are not the same!');
    }
  }


  /**
   * Create New Money Object
   *
   * Used internally to make the value object immutable
   *
   * @access private
   * @param string $amount
   * @return Money
   */
  private function _newMoney(string $amount):Money {
    $clone = clone $this;
    $clone->_amount = $amount;

    return $clone;
  }
}