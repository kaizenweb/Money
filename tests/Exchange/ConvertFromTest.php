<?php
declare(strict_types = 1);

namespace Tests\Kaizen\Exchange;

use Kaizen\Money;
use Kaizen\Exchange;
use Kaizen\Currency;
use PHPUnit\Framework\TestCase;

class ConvertFromTest extends TestCase {

  /**
   * @dataProvider convertFromProvider
   */
  public function testConvertFromCurrency(Currency $currency, Money $rate, Money $amount, Money $expected):void {
    $exchange = new Exchange($currency);
    $exchange->add($rate);

    $this->assertEquals($expected, $exchange->convertFrom($amount));
  }

  public function convertFromProvider():array {
    $usd = new Currency('USD');
    $eur = new Currency('EUR');
    $btc = new Currency('BTC');

    return [
      //USD
      '80 EUR to 100 USD' => [
        $usd,
        new Money(0.8, $eur, false),
        new Money(80, $eur, false),
        new Money(100, $usd, false)],
      '8.2 EUR to 10.25 USD' => [
        $usd,
        new Money(0.8, $eur, false),
        new Money(8.2, $eur, false),
        new Money(10.25, $usd, false)],
      '0.0008 BTC to 5.77 USD' => [
        $usd,
        new Money(0.00013860, $btc, false),
        new Money(0.0008, $btc, false),
        new Money(5.77, $usd, false)],

      //EUR
      '18 USD to 14.4 EUR' => [
        $eur,
        new Money(1.25, $usd, false),
        new Money(18, $usd, false),
        new Money(14.4, $eur, false)],
      '1.1 USD to 0.88 EUR' => [
        $eur,
        new Money(1.25, $usd, false),
        new Money(1.1, $usd, false),
        new Money(0.88, $eur, false)],

      //BTC
      '15290.56 USD to 2.12000244 BTC' => [
        $btc,
        new Money(7212.52, $usd, false),
        new Money(15290.56, $usd, false),
        new Money(2.12000244, $btc, false)],
      '1451.56 USD to 0.20125559 BTC' => [
        $btc,
        new Money(7212.52, $usd, false),
        new Money(1451.56, $usd, false),
        new Money(0.20125559, $btc, false)],
    ];
  }
}