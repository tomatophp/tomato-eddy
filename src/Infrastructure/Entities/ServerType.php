<?php

namespace TomatoPHP\TomatoEddy\Infrastructure\Entities;

use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;

class ServerType
{
    public readonly string $name;

    public function __construct(
        public readonly string $id,
        public readonly int $cpuCores,
        public readonly int $memoryInMb,
        public readonly int $storageInGb,
        public readonly ?int $monthlyPriceAmount = null,
        public readonly ?string $monthlyPriceCurrency = null,
    ) {

        $memoryInGb = $this->memoryInMb / 1024;

        $name = "{$this->id}: {$this->cpuCores} CPU, {$memoryInGb} GB RAM, {$this->storageInGb} GB";

        if ($monthlyPriceAmount && $monthlyPriceCurrency) {
            $money = new Money($monthlyPriceAmount, new Currency($monthlyPriceCurrency));
            $currencies = new ISOCurrencies();

            $numberFormatter = new \NumberFormatter('en_US', \NumberFormatter::DECIMAL);

            $name .= ' ('.(new IntlMoneyFormatter($numberFormatter, $currencies))->format($money).'/month)';
        }

        $this->name = $name;
    }
}
