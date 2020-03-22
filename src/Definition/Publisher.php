<?php

namespace App\Definition;

use App\Entity\Definition;
use Symfony\Component\HttpKernel\KernelInterface;
use Doctrine\Persistence\ManagerRegistry;

class Publisher
{
    const PATH = '/public/config/glossary.json';

    private $registry;
    private $path;

    public function __construct(ManagerRegistry $registry, KernelInterface $kernel)
    {
        $this->registry = $registry;
        $this->path = $kernel->getProjectDir().self::PATH;
    }

    public function publishAll()
    {
        $repo = $this->registry->getRepository(Definition::class);
        $iterableResult = $repo->findAllIterator();
        $words = [];
        $fileHandler = fopen($this->path, 'w');
        fwrite($fileHandler, '{');
        $started = false;
        while (($result = $iterableResult->next()) !== false) {
            foreach ($result as $row) {
                $words[$row['word']] = $row['description'];
            }
            if ($started) {
                fwrite($fileHandler, ',');
            }
            fwrite($fileHandler, substr(json_encode($words), 1, -1));
            $started = true;
            $words = [];
        }
        fwrite($fileHandler, '}');
    }
}
