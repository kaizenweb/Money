<?php
declare(strict_types = 1);

namespace Tests\Kaizen\Currency;

use Kaizen\Currency;
use PHPUnit\Framework\TestCase;

class BaseTest extends TestCase {


  public function testVerifyThatItIsImmutable():void {
    $currency = new Currency('USD');

    $this->expectException(\BadMethodCallException::class);

    $currency->__construct('USD');
  }


  public function testVerifyThatCurrencyDontExist():void {
    $this->expectExceptionMessage('Currency class does not exist!');

    (new Currency('USD2'));
  }


  public function testVerifyConfirmSame():void {
    $currency = new Currency('USD');

    $this->assertFalse($currency->confirmSame((new Currency('BTC'))));
    $this->assertTrue($currency->confirmSame((new Currency('USD'))));
  }

  public function testCastToJson():void {
    $usd = new Currency('USD');
    $eur = new Currency('EUR');

    $this->assertEquals('"USD"', json_encode($usd));
    $this->assertEquals('"EUR"', json_encode($eur));
  }

  public function testGetPrecision():void {
    $usd = new Currency('USD');
    $eur = new Currency('EUR');
    $btc = new Currency('BTC');

    $this->assertEquals(2, $usd->getPrecision());
    $this->assertEquals(2, $eur->getPrecision());
    $this->assertEquals(8, $btc->getPrecision());

    //todo throw excetion if the precision is lower than minimum, and above max?
  }
}