<?php

namespace App\Core\Invoice\Domain\Repository;

use App\Core\Invoice\Domain\Invoice;
use App\Core\Invoice\Domain\Status\InvoiceStatus;

interface InvoiceRepositoryInterface
{
    /**
     * @return Invoice[]
     */
    public function getInvoicesWithGreaterAmountAndStatus(int $amount, string $status): array; //dodać string $status do przekazania

    public function save(Invoice $invoice): void;

    public function flush(): void;
}
