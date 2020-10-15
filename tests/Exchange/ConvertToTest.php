<?php
declare(strict_types = 1);

namespace Tests\Kaizen\Exchange;

use Kaizen\Money;
use Kaizen\Exchange;
use Kaizen\Currency;
use PHPUnit\Framework\TestCase;

class ConvertToTest extends TestCase {

  /**
   * @dataProvider convertToProvider
   */
  public function testConvertToCurrency(Currency $currency, array $rates, Currency $toCurrency, Money $amount, Money $expected):void {
    $exchange = new Exchange($currency);

    foreach ($rates as $rate) {
      $exchange->add($rate);
    }

    $this->assertEquals($expected, $exchange->convertTo($toCurrency, $amount));
  }

  public function convertToProvider():array {
    $usd = new Currency('USD');
    $eur = new Currency('EUR');
    $btc = new Currency('BTC');

    return [
      //USD
      '100 USD to 80 EUR' => [
        $usd,
        [new Money(0.8, $eur, false)],
        $eur,
        new Money(100, $usd, false),
        new Money(80, $eur, false)],
      '5.77 USD to 0.00079972 BTC' => [
        $usd,
        [new Money(0.00013860, $btc, false)],
        $btc,
        new Money(5.77, $usd, false),
        new Money(0.00079972, $btc, false)],
      '5.77 EUR to 0.0009993 BTC' => [
        $usd,
        [ new Money(0.00013860, $btc, false),
          new Money(0.8, $eur, false),],
        $btc,
        new Money(5.77, $eur, false),
        new Money(0.00099930, $btc, false)],

      //EUR
      '100 EUR to 125 USD' => [
        $eur,
        [new Money(1.25, $usd, false)],
        $usd,
        new Money(100, $eur, false),
        new Money(125, $usd, false),],
      '0.88 EUR to 0.00012196 BTC'=> [
        $eur,
        [new Money(1.25, $usd, false),
         new Money(0.00013860, $btc, false)],
        $btc,
        new Money(0.88, $eur, false),
        new Money(0.00012196, $btc, false)],

      //BTC
      '1.67156971 BTC to 12056.23 USD' => [
        $btc,
        [new Money(7212.52, $usd, false)],
        $usd,
        new Money(1.67156971, $btc, false),
        new Money(12056.22, $usd, false)],
      '0.6723411 BTC to 3879.42 EUR' => [
        $btc,
        [new Money(7212.52, $usd, false),
          new Money(5770.02, $eur, false),],
        $eur,
        new Money(0.6723411, $btc, false),
        new Money(3879.42, $eur, false)],
    ];
  }
}