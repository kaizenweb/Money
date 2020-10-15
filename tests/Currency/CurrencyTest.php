<?php
/**
 * Created by PhpStorm.
 * User: Sven
 * Date: 21.12.2017
 * Time: 14.11
 */

declare(strict_types = 1);

namespace Tests\Kaizen\Currency;

use Kaizen\Currency;
use PHPUnit\Framework\TestCase;

class CurrencyTest extends TestCase {




  /**
   * USD TESTS
   */

  public function testCanGetCurrencyIsoUSD():void {
    $currency = new Currency('USD');

    $this->assertEquals('USD', $currency->getCurrencyIso());
    $this->assertNotEquals('BTC', $currency->getCurrencyIso());
  }


  public function testVerifyDisplayFormatUSD():void {
    $currency = new Currency('USD');

    $this->assertEquals('$134.00', $currency->getDisplayFormat('13400'));
    $this->assertEquals('$1.94', $currency->getDisplayFormat('194'));
    $this->assertEquals('$1,023.20', $currency->getDisplayFormat('102320'));
    $this->assertEquals('$99.98', $currency->getDisplayFormat('9998'));
  }


  public function testVerifyConvertFromUSD():void {
    $currency = new Currency('USD');

    $this->assertEquals('45600', $currency->convertFrom('456.00', false));
    $this->assertEquals('998', $currency->convertFrom('9.98', false));
    $this->assertEquals('123510', $currency->convertFrom('1235.10', false));
    $this->assertEquals('1239959', $currency->convertFrom('12399.59', false));

    $this->assertEquals('45600', $currency->convertFrom('45600', true));
    $this->assertEquals('998', $currency->convertFrom('998', true));

    $this->expectExceptionMessage('Too many decimals passed along for currency type.');

    $currency->convertFrom('37.987', false);
  }


  public function testVerifyConvertBackUSD():void {
    $currency = new Currency('USD');

    $this->assertEquals('456.00', $currency->convertBack('45600'));
    $this->assertEquals('9.98', $currency->convertBack('998'));
    $this->assertEquals('1,235.10', $currency->convertBack('123510'));
    $this->assertEquals('12,399.59', $currency->convertBack('1239959'));
  }


  /**
   * BITCOIN TESTS
   */

  public function testCanGetCurrencyIsoBTC():void {
    $currency = new Currency('BTC');

    $this->assertEquals('BTC', $currency->getCurrencyIso());
    $this->assertNotEquals('USD', $currency->getCurrencyIso());
  }


  public function testVerifyDisplayFormatBTC():void {
    $currency = new Currency('BTC');

    $this->assertEquals("\xC9\x83".'0.00001', $currency->getDisplayFormat('1000'));
    $this->assertEquals("\xC9\x83".'1', $currency->getDisplayFormat('100000000'));
    $this->assertEquals("\xC9\x83".'1,023.2', $currency->getDisplayFormat('102320000000'));
    $this->assertEquals("\xC9\x83".'99.98', $currency->getDisplayFormat('9998000000'));
  }


  public function testVerifyConvertFromBTC():void {
    $currency = new Currency('BTC');

    $this->assertEquals('2500000', $currency->convertFrom('0.025', false));
    $this->assertEquals('500000000', $currency->convertFrom('5', false));
    $this->assertEquals('1', $currency->convertFrom('0.00000001', false));
    $this->assertEquals('100023000', $currency->convertFrom('1.00023', false));

    $this->assertEquals('2500000', $currency->convertFrom('2500000', true));
    $this->assertEquals('998', $currency->convertFrom('998', true));

    $this->expectExceptionMessage('Too many decimals passed along for currency type.');

    $currency->convertFrom('37.987849239', false);
  }


  public function testVerifyConvertBackBTC():void {
    $currency = new Currency('BTC');

    $this->assertEquals('0.025', $currency->convertBack('2500000'));
    $this->assertEquals('5', $currency->convertBack('500000000'));
    $this->assertEquals('1.00023', $currency->convertBack('100023000'));
    $this->assertEquals('0.00000998', $currency->convertBack('998'));
  }
}