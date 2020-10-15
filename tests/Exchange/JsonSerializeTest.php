<?php
declare(strict_types = 1);

namespace Tests\Kaizen\Exchange;

use Kaizen\Money;
use Kaizen\Exchange;
use Kaizen\Currency;
use PHPUnit\Framework\TestCase;

class JsonSerializeTest extends TestCase {

  /**
   * @dataProvider currencyJsonRates
   */
  public function testCastToJson(Currency $currency, array $rates, array $expected):void {
    $exchange = new Exchange($currency);

    foreach ($rates as $rate) {
      $exchange->add($rate);
    }

    $this->assertEquals(json_encode($expected), json_encode($exchange));
  }

  public function currencyJsonRates():array {
    $usd = new Currency('USD');
    $eur = new Currency('EUR');
    $btc = new Currency('BTC');
    $nok = new Currency('NOK');

    return [
      //USD
      'USD - One Record' => [
        $usd,
        [new Money(0.8, $eur, false)],
        ['class' => 'Exchange', 'currency' => 'USD', 'exchange' => [['class' => 'Money', 'amount' => '80', 'currency' => 'EUR']]]],
      'USD - Several Records' => [
        $usd,
        [new Money(0.8, $eur, false),
         new Money(0.00013860, $btc, false),
         new Money(8.79, $nok, false)],
        ['class' => 'Exchange', 'currency' => 'USD', 'exchange' => [['class' => 'Money', 'amount' => '13860', 'currency' => 'BTC'],
                                                                    ['class' => 'Money', 'amount' => '80', 'currency' => 'EUR'],
                                                                    ['class' => 'Money', 'amount' => '879', 'currency' => 'NOK']]]],

      //EUR
      'EUR - One Record' => [
        $eur,
        [new Money(1.25, $usd, false)],
        ['class' => 'Exchange', 'currency' => 'EUR', 'exchange' => [['class' => 'Money', 'amount' => '125', 'currency' => 'USD']]]],
      'EUR - Several Records' => [
        $eur,
        [new Money(1.25, $usd, false),
         new Money(0.00013860, $btc, false),
         new Money(8.79, $nok, false)],
        ['class' => 'Exchange', 'currency' => 'EUR', 'exchange' => [['class' => 'Money', 'amount' => '13860', 'currency' => 'BTC'],
                                                                    ['class' => 'Money', 'amount' => '879', 'currency' => 'NOK'],
                                                                    ['class' => 'Money', 'amount' => '125', 'currency' => 'USD']]]],
    ];
  }
}