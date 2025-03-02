<?php

declare(strict_types=1);

namespace Bavix\Wallet\Internal\Query;

interface TransactionQueryInterface
{
    /**
     * Returns an array of UUIDs for the transactions.
     *
     * The array should not be empty and should contain only non-empty-strings or integers.
     *
     * @return non-empty-array<int|string, string> An array of transaction UUIDs.
     */
    public function getUuids(): array;
}
