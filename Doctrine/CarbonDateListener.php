<?php

namespace MNC\DoctrineCarbonBundle\Doctrine;

use Carbon\Carbon;
use DateTime;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Exception;
use Symfony\Component\Config\Definition\Exception\InvalidTypeException;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;

class CarbonDateListener implements EventSubscriber
{
    /**
     * @var Carbon
     */
    private $carbon;

    public function __construct(Carbon $carbon)
    {
        $this->carbon = $carbon;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return ['postLoad'];
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity =  $args->getEntity();
        if (method_exists($entity, 'setCreatedAt')
            && method_exists($entity, 'getCreatedAt')
            && $entity->getCreatedAt() instanceof DateTime)
        {
            $entity->setCreatedAt($this->carbon::instance($entity->getCreatedAt()));
        }

        if (method_exists($entity, 'setUpdatedAt')
            && method_exists($entity, 'getUpdatedAt')
            && $entity->getUpdatedAt() instanceof DateTime)
        {
            $entity->setUpdatedAt($this->carbon::instance($entity->getUpdatedAt()));
        }

        if (method_exists($entity, 'setDeletedAt')
            && method_exists($entity, 'getDeletedAt')
            && $entity->getDeletedAt() instanceof DateTime)
        {
            $entity->setDeletedAt($this->carbon::instance($entity->getDeletedAt()));
        }

        if (property_exists($entity, 'timeFields')) {
            if (!is_array($entity->timeFields)) {
                throw new InvalidTypeException('Property timeField of class ' . get_class($entity) . ' must be of the type Array');
            }
            foreach ($entity->timeFields as $timeField) {

                if (!property_exists($entity, $timeField)) {
                    throw new NoSuchPropertyException('Property ' . $timeField . 'not found in ' . get_class($entity) . ' class.');
                }

                $setter = 'set' . ucfirst($timeField);
                $getter = 'get' . ucfirst($timeField);

                if (!method_exists($entity, $setter) || !method_exists($entity, $getter)) {
                    throw new Exception('Cannot find ' . $getter . '() or ' . $setter . '() methods in ' . get_class($entity) . ' class. Did you forget adding the getters and setters?');
                }

                if ($entity->$getter() != null) {
                    $entity->$setter($this->carbon::instance($entity->$getter()));
                }

            }
        }
    }
}