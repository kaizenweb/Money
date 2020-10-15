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

class BaseTest extends TestCase {

  public function testVerifyItExists():void {
    $exchange = new Exchange(new Currency('USD'));

    $this->assertInstanceOf('\\Kaizen\\Exchange', $exchange);
  }

  public function testGetCurrency():void {
    $usd = new Currency('USD');
    $exchange = new Exchange($usd);

    $this->assertEquals($usd, $exchange->getBaseCurrency());

    $eur = new Currency('EUR');
    $exchange = new Exchange($eur);

    $this->assertEquals($eur, $exchange->getBaseCurrency());
  }

  public function testAddConversionRates():void {
    $usd = new Money(1, new Currency('USD'), false);
    $eur = new Money(0.8, new Currency('EUR'), false);
    $btc = new Money(0.000017, new Currency('BTC'), false);

    $exchange = new Exchange($usd->getCurrency());

    $exchange->add($eur);
    $exchange->add($btc);

    $this->assertTrue(true);
  }

  public function testAddConversionRateTwice():void {
    $usd = new Money(1, new Currency('USD'), false);
    $eur = new Money(0.8, new Currency('EUR'), false);

    $exchange = new Exchange($usd->getCurrency());

    $exchange->add($eur);
    $exchange->add($eur, true);

    $this->expectException(\InvalidArgumentException::class);
    $exchange->add($eur);
  }

  public function testAddConversionRateInSameCurrency():void {
    $usd = new Money(1, new Currency('USD'), false);

    $exchange = new Exchange($usd->getCurrency());

    $this->expectException(\LogicException::class);
    $exchange->add($usd);
  }

  public function testConvertFromRateDontExist():void {
    $usd = new Money(1, new Currency('USD'), false);
    $eur = new Money(0.8, new Currency('EUR'), false);

    $exchange = new Exchange($usd->getCurrency());
    $exchange->add($eur);

    $btc = new Money(1, new Currency('BTC'), false);

    $this->expectException(\InvalidArgumentException::class);
    $exchange->convertFrom($btc);
  }

  public function testConvertToRateDontExist():void {
    $usd = new Money(1, new Currency('USD'), false);
    $eur = new Money(0.8, new Currency('EUR'), false);

    $exchange = new Exchange($usd->getCurrency());
    $exchange->add($eur);

    $btc = new Money(1, new Currency('BTC'), false);

    $this->expectException(\InvalidArgumentException::class);
    $exchange->convertTo($btc->getCurrency(), $usd);
  }
}