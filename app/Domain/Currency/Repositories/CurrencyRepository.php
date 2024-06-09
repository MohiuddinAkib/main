<?php

namespace App\Domain\Currency\Repositories;

use App\Domain\Currency\Contracts\CurrencyRepository as ContractsCurrencyRepository;
use Illuminate\Support\Collection;
use App\Domain\Currency\Resources\CurrencyResource;
use App\Domain\Currency\Resources\DenominationResource;

class CurrencyRepository implements ContractsCurrencyRepository
{
    public function getCurrencies(): Collection
    {
        return collect(config('wallet.currencies', []))
            ->map(fn (array $entry, string $code) => new CurrencyResource(
                $code,
                data_get($entry, 'name')
            ))
            ->values();
    }

    public function getDenominations(string $currency): Collection
    {
        /**
         * @param array{name: string, value: float|int} $denomination
         */
        $coins = collect(config("wallet.currencies.{$currency}.coins", []))
            ->map(fn (array $denomination) => new DenominationResource(
                name: $denomination['name'],
                value: $denomination['value'],
                type: 'coin'
            ));

        /**
         * @param array{name: string, value: float|int} $denomination
         */
        $bills = collect(config("wallet.currencies.{$currency}.bills", []))
            ->map(fn (array $denomination) => new DenominationResource(
                name: $denomination['name'],
                value: $denomination['value'],
                type: 'bill'
            ));

        return $coins->merge($bills)->values()->unique(fn (DenominationResource $denomination) => $denomination->name . $denomination->type . $denomination->value);
    }

    public function isCurrencySupported(string $currency): bool
    {
        return config()->has("wallet.currencies.{$currency}");
    }
}