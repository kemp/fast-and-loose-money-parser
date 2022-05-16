<?php


use PHPUnit\Framework\TestCase;

class FastAndLooseMoneyParserTest extends TestCase
{
    /**
     * @test
     * @dataProvider moneyProvider
     */
    public function it_handles_negatives_in_parens($moneyString, $expected)
    {
        $currencies = new \Money\Currencies\ISOCurrencies();
        $parser = new \Kemp\FastAndLooseMoneyParser\FastAndLooseMoneyParser($currencies);

        $money = $parser->parse($moneyString, new \Money\Currency('USD'));

        $this->assertEquals($expected, $money->getAmount());
    }

    public function moneyProvider()
    {
        return [
            ['($1,234.56)', '-123456'],
            ['$1,234.56', '123456'],
            ['$1,234', '123400'],
            ['1,234', '123400'],
        ];
    }

    /** @test */
    public function it_fails_with_other_currencies()
    {
        $currencies = new \Money\Currencies\ISOCurrencies();
        $parser = new \Kemp\FastAndLooseMoneyParser\FastAndLooseMoneyParser($currencies);

        $this->expectException(InvalidArgumentException::class);

        $parser->parse('1.00', new \Money\Currency('EUR'));
    }

    /** @test */
    public function it_errors_if_no_matching_parens()
    {
        $currencies = new \Money\Currencies\ISOCurrencies();
        $parser = new \Kemp\FastAndLooseMoneyParser\FastAndLooseMoneyParser($currencies);

        $this->expectException(\Money\Exception\ParserException::class);

        $parser->parse('$(1,234.56', new \Money\Currency('USD'));
    }
}
