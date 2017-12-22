<?php

namespace MNC\DoctrineCarbonBundle\Doctrine;

use Carbon\Carbon;
use DateTime;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Config\Definition\Exception\InvalidTypeException;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\Validator\Constraints\Date;

class CarbonDateListener implements EventSubscriber
{
    /**
     * @var Carbon
     */
    private $carbon;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var array
     */
    private $properties;

    /**
     * @var array
     */
    private $exludedClasses;

    /**
     * CarbonDateListener constructor.
     * @param Carbon $carbon
     * @param array|null $properties
     */
    public function __construct(
        Carbon $carbon,
        LoggerInterface $logger,
        array $properties,
        array $excludedClasses
    )
    {
        $this->carbon = $carbon;
        $this->logger = $logger;
        $this->properties = $properties;
        $this->exludedClasses = $excludedClasses;
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
        if ($this->classIsExcluded(get_class($entity))) {
            return;
        }
        $this->setCarbonInstances($entity);
    }

    /**
     * Checks if the current class is excluded of the converting process.
     * @param \ReflectionClass $classMeta
     * @return bool
     */
    public function classIsExcluded($className)
    {
        if ($this->exludedClasses !== null ) {
            return in_array($className, $this->exludedClasses);
        }
        return false;
    }

    /**
     * Sets the Carbon Instances over the DateTime ones for each indicated property.
     * @param $entity
     */
    public function setCarbonInstances($entity)
    {
        foreach ($this->properties as $property) {
            $getter = 'get'.ucfirst($property);
            $setter = 'set'.ucfirst($property);
            if (method_exists($entity, $setter) && method_exists($entity, $getter)) {
                if ($entity->{$getter}() instanceof DateTime && $entity->{$getter}() !== null) {
                    $entity->{$setter}($this->carbon::instance($entity->{$getter}()));
                    $this->logger->debug("Property $property converted to Carbon");
                    continue;
                }
            }
            $this->logger->debug("Property $property NOT converted to Carbon");
        }
    }
}