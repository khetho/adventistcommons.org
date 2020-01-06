<?php

namespace App\Product;

use App\Entity\Attachment;
use App\Entity\DownloadLog;
use App\Entity\Product;
use App\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

class DownloadLogger
{
    private $manager;
    private $security;
    private $request;

    public function __construct(
        EntityManagerInterface $manager,
        Security $security,
        RequestStack $requestStack
    ) {
        $this->manager = $manager;
        $this->security = $security;
        $this->request = $requestStack->getCurrentRequest();
    }
    
    public function log($object)
    {
        if (!($object instanceof Product) && !($object instanceof Project) && !($object instanceof Attachment)) {
            throw new \Exception('To log a download, you must provide product, project or attachment');
        }

        $log = new DownloadLog();
        if ($object instanceof Product) {
            $log->setProduct($object);
        }
        if ($object instanceof Project) {
            $log->setProject($object);
        }
        if ($object instanceof Attachment) {
            $log->setAttachment($object);
        }
        $log->setUser($this->security->getUser());
        $log->setPath($this->request->getUri());
        $this->manager->persist($log);
        $this->manager->flush();
    }
}
