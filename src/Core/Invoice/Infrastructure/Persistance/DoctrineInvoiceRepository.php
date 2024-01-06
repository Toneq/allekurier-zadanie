<?php

namespace App\Core\Invoice\Infrastructure\Persistance;

use App\Core\Invoice\Domain\Invoice;
use App\Core\Invoice\Domain\Repository\InvoiceRepositoryInterface;
use App\Core\Invoice\Domain\Status\InvoiceStatus;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

class DoctrineInvoiceRepository implements InvoiceRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {}

    //zmiana z InvoiceStatus $invoiceStatus na string $status który jest przesłany
    public function getInvoicesWithGreaterAmountAndStatus(int $amount, string $status): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('i')
            ->from(Invoice::class, 'i')
            ->where('i.status = :invoice_status')
            ->andWhere('i.amount > :invoice_amount') //dodanie warunku który wyświetla tylko faktury powyżej kwoty
            ->setParameter(':invoice_status', $status) //tutaj dodanie statusu (przesłanego)
            ->setParameter(':invoice_amount', $amount) //tutaj dodanie amount (przesłanego)
            ->getQuery()
            ->getResult();
    }

    public function save(Invoice $invoice): void
    {
        $this->entityManager->persist($invoice);

        $events = $invoice->pullEvents();
        foreach ($events as $event) {
            $this->eventDispatcher->dispatch($event);
        }
    }

    public function flush(): void
    {
        $this->entityManager->flush();
    }
}
