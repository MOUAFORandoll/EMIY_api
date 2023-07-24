<?php
// src/EventListener/SoftDeleteListener.p
// SoftDeleteSubscriber.php// src/EventListener/SoftDeleteSubscriber.php
namespace App\EventListener;

use App\Traits\SoftDeletableTrait;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class SoftDeleteSubscriber implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return [Events::preRemove];
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if ($entity instanceof SoftDeletableTrait) {
            $entity->setDeletedAt(new \DateTime());
            $args->getObjectManager()->persist($entity);
            $args->getObjectManager()->flush();
            $args->getObjectManager()->detach($entity);
            // $args->setEntityState($entity, \Doctrine\ORM\UnitOfWork::STATE_DETACHED);
            // $args->stopPropagation();
        }
    }
}
