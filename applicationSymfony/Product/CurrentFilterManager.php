<?php

namespace App\Product;

use App\Entity\Product;
use App\Product\Entity\FilterStatus;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Knp\DictionaryBundle\Dictionary\Collection;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Serializer\SerializerInterface;

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
        } else {
            $filterStatus->attach($this->entityManager);
        }
        $this->currentFilterStatus = $filterStatus;

        return $this->currentFilterStatus;
    }

    public function setCurrentFilterStatus(FilterStatus $currentFilterStatus): void
    {
        $this->currentFilterStatus = $currentFilterStatus;
        $this->session->set('product_filter', $currentFilterStatus);
    }
}
