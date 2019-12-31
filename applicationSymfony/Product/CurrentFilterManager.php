<?php

namespace App\Product;

use App\Product\Entity\FilterStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CurrentFilterManager
{
    private $session;
    
    private $entityManager;

    private $currentFilterStatus;

    public function __construct(
        SessionInterface $session,
        EntityManagerInterface $entityManager
    ) {
        $this->session = $session;
        $this->entityManager = $entityManager;
    }
    
    public function reset()
    {
        $this->session->set('product_filter', null);
    }

    public function getCurrentFilterStatus(): FilterStatus
    {
        if ($this->currentFilterStatus) {
            return $this->currentFilterStatus;
        }

        /** @var FilterStatus $filterStatus */
        $filterStatus = $this->session->get('product_filter');
        if (!$filterStatus || !($filterStatus instanceof FilterStatus)) {
            $filterStatus = new FilterStatus();
        }
        $filterStatus->attach($this->entityManager);
        $this->currentFilterStatus = $filterStatus;

        return $this->currentFilterStatus;
    }

    public function setCurrentFilterStatus(FilterStatus $currentFilterStatus): void
    {
        $this->currentFilterStatus = $currentFilterStatus;
        $this->session->set('product_filter', $currentFilterStatus);
    }
}
