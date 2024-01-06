<?php

namespace App\Core\Invoice\Application\Query\GetInvoicesByStatusAndAmountGreater;

class GetInvoicesByStatusAndAmountGreaterQuery
{
    //trzeba dodać public readonly string $status
    public function __construct(public readonly int $amount, public readonly string $status)
    {
    }
}
