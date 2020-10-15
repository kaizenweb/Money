<?php
/**
 * Created by PhpStorm.
 * User: Sven
 * Date: 21.12.2017
 * Time: 14.11
 */

declare(strict_types = 1);

namespace Tests\Kaizen\Exchange;

use Kaizen\Money;
use Kaizen\Exchange;
use Kaizen\Currency;
use PHPUnit\Framework\TestCase;

class InformationTest extends TestCase {

  /**
   * @dataProvider currencyRates
   */
  public function testGetRates(Currency $currency, array $rates, array $expected):void {
    $exchange = new Exchange($currency);

    foreach ($rates as $rate) {
      $exchange->add($rate);
    }

    $this->assertEquals($expected, $exchange->getRates());
  }

  public function currencyRates():array {
    $usd = new Currency('USD');
    $eur = new Currency('EUR');
    $btc = new Currency('BTC');
    $nok = new Currency('NOK');

    return [
      //USD
      'USD - One Record' => [
        $usd,
        [new Money(0.8, $eur, false)],
        ['currency' => $usd,
          'exchange' => [
            new Money(0.8, $eur, false),
          ]]],
      'USD - Several Records' => [
        $usd,
        [new Money(0.8, $eur, false),
         new Money(0.00013860, $btc, false),
         new Money(8.79, $nok, false)],
        ['currency' => $usd,
         'exchange' => [
           new Money(0.00013860, $btc, false),
           new Money(0.8, $eur, false),
           new Money(8.79, $nok, false),
         ]]],

      //EUR
      'EUR - One Record' => [
        $eur,
        [new Money(1.25, $usd, false)],
        ['currency' => $eur,
         'exchange' => [
           new Money(1.25, $usd, false),
         ]]],
      'EUR - Several Records' => [
        $eur,
        [new Money(1.25, $usd, false),
         new Money(0.00013860, $btc, false),
         new Money(8.79, $nok, false)],
        ['currency' => $eur,
         'exchange' => [
           new Money(0.00013860, $btc, false),
           new Money(8.79, $nok, false),
           new Money(1.25, $usd, false),
         ]]],
    ];
  }


  public function testGetRate():void {
    $usd = new Money(1, new Currency('USD'), false);
    $eur = new Money(0.8, new Currency('EUR'), false);
    $btc = new Money(0.000017, new Currency('BTC'), false);
    $nok = new Money(8.79, new Currency('NOK'), false);

    $exchange = new Exchange($usd->getCurrency());

    $exchange->add($eur);
    $exchange->add($btc);
    $exchange->add($nok);

    $this->assertEquals($eur, $exchange->getRate('EUR'));
    $this->assertEquals($nok, $exchange->getRate('NOK'));
    $this->assertEquals($btc, $exchange->getRate('BTC'));
  }

  public function testGetRateWhenCurrencyDontExist():void {
    $usd = new Money(1, new Currency('USD'), false);
    $nok = new Money(8.79, new Currency('NOK'), false);

    $exchange = new Exchange($usd->getCurrency());

    $exchange->add($nok);

    $this->assertNull($exchange->getRate('EUR'));
  }

}