<?php
declare(strict_types = 1);

namespace Tests\Kaizen\Money;

use Kaizen\Money;
use Kaizen\Currency;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase {


  public function testVerifyThatItIsImmutable():void {
    $money = new Money(2222.22, new Currency('USD'), false);

    $this->expectExceptionMessage('Attempt to use immutable value object as mutable.');

    $money->__construct(3, new Currency('USD'), false);
  }


  public function testCanBeCreatedWithPositiveAmount():void {
    $money = new Money(2222.22, new Currency('USD'), false);

    $this->assertEquals('222222', $money->getAmount());
  }


  public function testCanBeCreatedWithNegativeAmount():void {
    $money = new Money(-222, new Currency('USD'), false);

    $this->assertEquals(-22200, $money->getAmount());
  }


  public function testCanNotBeCreatedWithTooManyDecimals():void {
    $this->expectExceptionMessage('Too many decimals passed along for currency type.');

    new Money(2222.222, new Currency('USD'), false);
  }


  public function testGetBaseAmountWorks():void {
    $money = new Money(222.22, new Currency('USD'), false);
    $second = new Money(21.01, new Currency('USD'), false);
    $negative = new Money(-19.47, new Currency('USD'), false);
    $negative_second = new Money(-200.13, new Currency('USD'), false);

    $this->assertEquals('222.22', $money->getBaseAmount());
    $this->assertEquals('243.23', $money->add($second)->getBaseAmount());
    $this->assertEquals('202.75', $money->add($negative)->getBaseAmount());
    $this->assertEquals('202.75', $money->subtract($negative)->getBaseAmount());
    $this->assertEquals(-19.47, $negative->getBaseAmount());
    $this->assertEquals(-219.60, $negative->add($negative_second)->getBaseAmount());
    $this->assertEquals(-219.60, $negative->subtract($negative_second)->getBaseAmount());
    $this->assertEquals(1.54, $negative->add($second)->getBaseAmount());
    $this->assertEquals(-40.48, $negative->subtract($second)->getBaseAmount());
  }


  public function testGetCurrencyWorks():void {
    $fiat = new Currency('USD');
    $bitcoin = new Currency('BTC');

    $money = new Money(222.22, $fiat, false);
    $second = new Money(21.01, $bitcoin, false);

    $this->assertEquals($fiat, $money->getCurrency());
    $this->assertEquals($bitcoin, $second->getCurrency());
    $this->assertNotEquals($bitcoin, $money->getCurrency());
    $this->assertNotEquals($fiat, $second->getCurrency());
  }


  public function testDifferentCurrencyDontWork():void { //todo error here, it only process the first one...
    $fiat = new Money(1, new Currency('USD'), false);
    $bitcoin = new Money(4, new Currency('BTC'), false);

    $this->expectExceptionMessage('The currency on the money objects are not the same!');
    $fiat->add($bitcoin);

    $this->expectExceptionMessage('The currency on the money objects are not the same!');
    $fiat->subtract($bitcoin);

    $this->expectExceptionMessage('The currency on the money objects are not the same!');
    $fiat->equals($bitcoin);

    $this->expectExceptionMessage('The currency on the money objects are not the same!');
    $fiat->greaterThan($bitcoin);

    $this->expectExceptionMessage('The currency on the money objects are not the same!');
    $fiat->greaterThanOrEqual($bitcoin);

    $this->expectExceptionMessage('The currency on the money objects are not the same!');
    $fiat->lessThan($bitcoin);

    $this->expectExceptionMessage('The currency on the money objects are not the same!');
    $fiat->lessThanOrEqual($bitcoin);
  }


  public function testAddMoneyWorks():void {
    $one = new Money(1, new Currency('USD'), false);
    $four = new Money(4, new Currency('USD'), false);
    $five_fiftyseven = new Money(5.57, new Currency('USD'), false);
    $twentyone = new Money(21, new Currency('USD'), false);
    $negative = new Money(-19.47, new Currency('USD'), false);
    $negative_second = new Money(-200.13, new Currency('USD'), false);

    $this->assertEquals(500, $one->add($four)->getAmount());
    $this->assertEquals(900, $one->add($four)->add($four)->getAmount());
    $this->assertEquals(2600, $one->add($four)->add($twentyone)->getAmount());
    $this->assertEquals(657, $one->add($five_fiftyseven)->getAmount());
    $this->assertEquals(1214, $one->add($five_fiftyseven)->add($five_fiftyseven)->getAmount());
    $this->assertEquals(-21960, $negative->add($negative_second)->getAmount());
    $this->assertEquals(153, $negative->add($twentyone)->getAmount());
    $this->assertEquals(153, $twentyone->add($negative)->getAmount());
    $this->assertEquals(-1794, $twentyone->add($negative)->add($negative)->getAmount());
  }


  public function testSubtractMoneyWorks():void {
    $one = new Money(1, new Currency('USD'), false);
    $five_fiftyseven = new Money(5.57, new Currency('USD'), false);
    $hundred = new Money(100, new Currency('USD'), false);
    $negative = new Money(-19.47, new Currency('USD'), false);
    $negative_second = new Money(-200.13, new Currency('USD'), false);

    $this->assertEquals(9900, $hundred->subtract($one)->getAmount());
    $this->assertEquals(9343, $hundred->subtract($one)->subtract($five_fiftyseven)->getAmount());
    $this->assertEquals(9343, $hundred->subtract($one)->subtract($five_fiftyseven)->getAmount());
    $this->assertEquals(8886, $hundred->subtract($five_fiftyseven)->subtract($five_fiftyseven)->getAmount());
    $this->assertEquals(8053, $hundred->subtract($negative)->getAmount());
    $this->assertEquals(-9900, $one->subtract($hundred)->getAmount());
    $this->assertEquals(-10013, $hundred->subtract($negative_second)->getAmount());
    $this->assertEquals(-21960, $negative->subtract($negative_second)->getAmount());
    $this->assertEquals(-11947, $negative->subtract($hundred)->getAmount());
    $this->assertEquals(-41973, $negative->subtract($negative_second)->subtract($negative_second)->getAmount());
    $this->assertEquals(-31960, $negative->subtract($negative_second)->subtract($hundred)->getAmount());
  }


  public function testMultiplyMoneyWorks():void {
    $one = new Money(1, new Currency('USD'), false);
    $five_fiftyseven = new Money(5.57, new Currency('USD'), false);
    $twentyone = new Money(21, new Currency('USD'), false);
    $negative = new Money(-19.47, new Currency('USD'), false);

    $this->assertEquals(401, $one->multiply(4.01)->getAmount());
    $this->assertEquals(2020, $one->multiply(4)->multiply(5.05)->getAmount());
    $this->assertEquals(557, $five_fiftyseven->multiply(1)->getAmount());
    $this->assertEquals(11697, $twentyone->multiply(5.57)->getAmount());
    $this->assertEquals(278, $five_fiftyseven->multiply(0.5)->getAmount());
    $this->assertEquals(279, $five_fiftyseven->multiply(0.5, true)->getAmount());
    $this->assertEquals(-974, $negative->multiply(0.5)->getAmount());
    $this->assertEquals(-973, $negative->multiply(0.5, true)->getAmount());
  }


  public function testCanNotMultiplyWithNegativeAmount():void {
    $this->expectExceptionMessage('The factor passed cannot be negative or zero.');

    (new Money(1, new Currency('USD'), false))->multiply(-1);

    $this->expectExceptionMessage('The factor passed cannot be negative or zero.');

    (new Money(1, new Currency('USD'), false))->multiply(0);
  }


  public function testAddMoneyWorksBigInt():void {
    $one = new Money(1, new Currency('BTC'), false);
    $four = new Money(4, new Currency('BTC'), false);
    $five_fiftyseven = new Money(5.57777777, new Currency('BTC'), false);
    $twentyone = new Money(210, new Currency('BTC'), false);
    $negative = new Money(-19.47, new Currency('BTC'), false);
    $negative_second = new Money(-200.13, new Currency('BTC'), false);


    //Making certain it can handle values over 32bit limitation in case Bitcoin or other crypto currency is used
    $this->assertEquals(500000000, $one->add($four)->getAmount());
    $this->assertEquals(900000000, $one->add($four)->add($four)->getAmount());
    $this->assertEquals(21500000000, $one->add($four)->add($twentyone)->getAmount());
    $this->assertEquals(657777777, $one->add($five_fiftyseven)->getAmount());
    $this->assertEquals(1215555554, $one->add($five_fiftyseven)->add($five_fiftyseven)->getAmount());
    $this->assertEquals(63657777777, $one->add($five_fiftyseven)->add($twentyone)->add($twentyone)->add($twentyone)->getAmount());
    $this->assertEquals(-21960000000, $negative->add($negative_second)->getAmount());
    $this->assertEquals(19053000000, $negative->add($twentyone)->getAmount());
    $this->assertEquals(19053000000, $twentyone->add($negative)->getAmount());
    $this->assertEquals(-960000000, $twentyone->add($negative_second)->add($negative)->getAmount());
  }


  public function testSubtractMoneyWorksBigInt():void {
    $one = new Money(1, new Currency('BTC'), false);
    $five_fiftyseven = new Money(5.57777777, new Currency('BTC'), false);
    $hundred = new Money(100, new Currency('BTC'), false);
    $satoshi = new Money(0.00000001, new Currency('BTC'), false);
    $negative = new Money(-19.47, new Currency('BTC'), false);
    $negative_second = new Money(-200.13, new Currency('BTC'), false);

    $this->assertEquals(9900000000, $hundred->subtract($one)->getAmount());
    $this->assertEquals(9342222223, $hundred->subtract($one)->subtract($five_fiftyseven)->getAmount());
    $this->assertEquals(9342222222, $hundred->subtract($one)->subtract($five_fiftyseven)->subtract($satoshi)->getAmount());
    $this->assertEquals(8884444445, $hundred->subtract($five_fiftyseven)->subtract($five_fiftyseven)->subtract($satoshi)->getAmount());
    $this->assertEquals(8053000000, $hundred->subtract($negative)->getAmount());
    $this->assertEquals(-9900000000, $one->subtract($hundred)->getAmount());
    $this->assertEquals(-10013000000, $hundred->subtract($negative_second)->getAmount());
    $this->assertEquals(-21960000000, $negative->subtract($negative_second)->getAmount());
    $this->assertEquals(-11947000000, $negative->subtract($hundred)->getAmount());
    $this->assertEquals(-41973000000, $negative->subtract($negative_second)->subtract($negative_second)->getAmount());
    $this->assertEquals(-31960000000, $negative->subtract($negative_second)->subtract($hundred)->getAmount());
  }


  public function testMultiplyMoneyWorksBigInt():void {
    $one = new Money(100, new Currency('BTC'), false);
    $five_fiftyseven = new Money(5.57777777, new Currency('BTC'), false);
    $twentyone = new Money(2100, new Currency('BTC'), false);
    $negative = new Money(-19.47777777, new Currency('BTC'), false);

    $this->assertEquals(40100000000, $one->multiply(4.01)->getAmount());
    $this->assertEquals(202000000000, $one->multiply(4)->multiply(5.05)->getAmount());
    $this->assertEquals(557777777, $five_fiftyseven->multiply(1)->getAmount());
    $this->assertEquals(1171333331700, $twentyone->multiply(5.57777777)->getAmount());
    $this->assertEquals(278888888, $five_fiftyseven->multiply(0.5)->getAmount());
    $this->assertEquals(278888889, $five_fiftyseven->multiply(0.5, true)->getAmount());
    $this->assertEquals(-973888889, $negative->multiply(0.5)->getAmount());
    $this->assertEquals(-973888888, $negative->multiply(0.5, true)->getAmount());
  }


  public function testMoneyEqualsToWorks():void {
    $money = new Money(1, new Currency('USD'), false);
    $secondary = new Money(1, new Currency('USD'), false);
    $negative = new Money(-1, new Currency('USD'), false);
    $second_negative = new Money(-1, new Currency('USD'), false);

    $this->assertEquals(true, $money->equals($secondary));
    $this->assertEquals(false, $money->add($secondary)->equals($secondary));
    $this->assertEquals(false, $money->equals($negative));
    $this->assertEquals(true, $negative->equals($second_negative));
    $this->assertEquals(false, $negative->equals($money));
    $this->assertEquals(false, $negative->add($second_negative)->equals($second_negative));

    //Test same just larger amounts
    $money = new Money(1123341.25, new Currency('USD'), false);
    $secondary = new Money(1123341.25, new Currency('USD'), false);
    $negative = new Money(-1239323.48, new Currency('USD'), false);
    $second_negative = new Money(-1239323.48, new Currency('USD'), false);

    $this->assertEquals(true, $money->equals($secondary));
    $this->assertEquals(false, $money->add($secondary)->equals($secondary));
    $this->assertEquals(false, $money->equals($negative));
    $this->assertEquals(true, $negative->equals($second_negative));
    $this->assertEquals(false, $negative->equals($money));
    $this->assertEquals(false, $negative->add($second_negative)->equals($second_negative));
  }

  
  public function testMoneyGreaterThanWorks():void {
    $money = new Money(3, new Currency('USD'), false);
    $secondary = new Money(1, new Currency('USD'), false);
    $negative = new Money(-1, new Currency('USD'), false);
    $second_negative = new Money(-3, new Currency('USD'), false);

    $this->assertEquals(true, $money->greaterThan($secondary));
    $this->assertEquals(false, $secondary->greaterThan($money));
    $this->assertEquals(true, $money->greaterThan($negative));
    $this->assertEquals(true, $negative->greaterThan($second_negative));
    $this->assertEquals(false, $second_negative->greaterThan($negative));
    $this->assertEquals(false, $negative->greaterThan($money));

    //Test same just larger amounts
    $money = new Money(30123231.22, new Currency('USD'), false);
    $secondary = new Money(112321.99, new Currency('USD'), false);
    $negative = new Money(-13829384.87, new Currency('USD'), false);
    $second_negative = new Money(-32390123.34, new Currency('USD'), false);

    $this->assertEquals(true, $money->greaterThan($secondary));
    $this->assertEquals(false, $secondary->greaterThan($money));
    $this->assertEquals(true, $money->greaterThan($negative));
    $this->assertEquals(true, $negative->greaterThan($second_negative));
    $this->assertEquals(false, $second_negative->greaterThan($negative));
    $this->assertEquals(false, $negative->greaterThan($money));
  }


  public function testMoneyGreaterThanOrEqualWorks():void {
    $money = new Money(3, new Currency('USD'), false);
    $secondary = new Money(1, new Currency('USD'), false);
    $third = new Money(3, new Currency('USD'), false);
    $negative = new Money(-1, new Currency('USD'), false);
    $second_negative = new Money(-3, new Currency('USD'), false);
    $third_negative = new Money(-1, new Currency('USD'), false);

    $this->assertEquals(true, $money->greaterThanOrEqual($secondary)); //Greater
    $this->assertEquals(true, $money->greaterThanOrEqual($third)); //Equal
    $this->assertEquals(false, $secondary->greaterThanOrEqual($money)); //Smaller
    $this->assertEquals(true, $money->greaterThanOrEqual($negative)); //Greater
    $this->assertEquals(true, $negative->greaterThanOrEqual($second_negative)); //Greater
    $this->assertEquals(true, $negative->greaterThanOrEqual($third_negative)); //Equal
    $this->assertEquals(false, $second_negative->greaterThanOrEqual($negative)); //Smaller
    $this->assertEquals(false, $negative->greaterThanOrEqual($money)); //Smaller

    //Test same just larger amounts
    $money = new Money(30123231.22, new Currency('USD'), false);
    $secondary = new Money(112321.99, new Currency('USD'), false);
    $third = new Money(30123231.22, new Currency('USD'), false);
    $negative = new Money(-193048239.45, new Currency('USD'), false);
    $second_negative = new Money(-329029123.22, new Currency('USD'), false);
    $third_negative = new Money(-193048239.45, new Currency('USD'), false);

    $this->assertEquals(true, $money->greaterThanOrEqual($secondary)); //Greater
    $this->assertEquals(true, $money->greaterThanOrEqual($third)); //Equal
    $this->assertEquals(false, $secondary->greaterThanOrEqual($money)); //Smaller
    $this->assertEquals(true, $money->greaterThanOrEqual($negative)); //Greater
    $this->assertEquals(true, $negative->greaterThanOrEqual($second_negative)); //Greater
    $this->assertEquals(true, $negative->greaterThanOrEqual($third_negative)); //Equal
    $this->assertEquals(false, $second_negative->greaterThanOrEqual($negative)); //Smaller
    $this->assertEquals(false, $negative->greaterThanOrEqual($money)); //Smaller
  }


  public function testMoneyLessThanWorks():void {
    $money = new Money(1, new Currency('USD'), false);
    $secondary = new Money(3, new Currency('USD'), false);
    $negative = new Money(-1, new Currency('USD'), false);
    $second_negative = new Money(-3, new Currency('USD'), false);

    $this->assertEquals(true, $money->lessThan($secondary));
    $this->assertEquals(false, $secondary->lessThan($money));
    $this->assertEquals(false, $money->lessThan($negative));
    $this->assertEquals(false, $negative->lessThan($second_negative));
    $this->assertEquals(true, $second_negative->lessThan($negative));
    $this->assertEquals(true, $negative->lessThan($money));

    //Test same just larger amounts
    $money = new Money(112321.99, new Currency('USD'), false);
    $secondary = new Money(30123231.22, new Currency('USD'), false);
    $negative = new Money(-139483723.12, new Currency('USD'), false);
    $second_negative = new Money(-339483123.87, new Currency('USD'), false);

    $this->assertEquals(true, $money->lessThan($secondary));
    $this->assertEquals(false, $secondary->lessThan($money));
    $this->assertEquals(false, $money->lessThan($negative));
    $this->assertEquals(false, $negative->lessThan($second_negative));
    $this->assertEquals(true, $second_negative->lessThan($negative));
    $this->assertEquals(true, $negative->lessThan($money));
  }


  public function testMoneyLessThanOrEqualWorks():void {
    $money = new Money(1, new Currency('USD'), false);
    $secondary = new Money(3, new Currency('USD'), false);
    $third = new Money(1, new Currency('USD'), false);
    $negative = new Money(-1, new Currency('USD'), false);
    $second_negative = new Money(-3, new Currency('USD'), false);
    $third_negative = new Money(-1, new Currency('USD'), false);

    $this->assertEquals(true, $money->lessThanOrEqual($secondary)); //Smaller
    $this->assertEquals(true, $money->lessThanOrEqual($third)); //Equal
    $this->assertEquals(false, $secondary->lessThanOrEqual($money)); //Greater
    $this->assertEquals(false, $money->lessThanOrEqual($negative)); //Greater
    $this->assertEquals(false, $negative->lessThanOrEqual($second_negative)); //Greater
    $this->assertEquals(true, $negative->lessThanOrEqual($third_negative)); //Equal
    $this->assertEquals(true, $second_negative->lessThanOrEqual($negative)); //Smaller
    $this->assertEquals(true, $negative->lessThanOrEqual($money)); //Smaller

    //Test same just larger amounts
    $money = new Money(112321.99, new Currency('USD'), false);
    $secondary = new Money(30123231.22, new Currency('USD'), false);
    $third = new Money(112321.99, new Currency('USD'), false);
    $negative = new Money(-193048239.45, new Currency('USD'), false);
    $second_negative = new Money(-329029123.22, new Currency('USD'), false);
    $third_negative = new Money(-193048239.45, new Currency('USD'), false);

    $this->assertEquals(true, $money->lessThanOrEqual($secondary)); //Smaller
    $this->assertEquals(true, $money->lessThanOrEqual($third)); //Equal
    $this->assertEquals(false, $secondary->lessThanOrEqual($money)); //Greater
    $this->assertEquals(false, $money->lessThanOrEqual($negative)); //Greater
    $this->assertEquals(false, $negative->lessThanOrEqual($second_negative)); //Greater
    $this->assertEquals(true, $negative->lessThanOrEqual($third_negative)); //Equal
    $this->assertEquals(true, $second_negative->lessThanOrEqual($negative)); //Smaller
    $this->assertEquals(true, $negative->lessThanOrEqual($money)); //Smaller
  }
}