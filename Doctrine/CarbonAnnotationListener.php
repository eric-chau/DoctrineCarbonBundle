<?php

namespace MNC\DoctrineCarbonBundle\Doctrine;


use Carbon\Carbon;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Doctrine\Common\Annotations\Reader;

class CarbonAnnotationListener implements EventSubscriber
{
    /**
     * @var Carbon
     */
    private $carbon;

    /**
     * @var Reader
     */
    private $annotationsReader;
    /**
     * CarbonAnnotationListener constructor.
     * @param Carbon $carbon
     */
    public function __construct(Carbon $carbon, Reader $annotationsReader)
    {
        $this->carbon = $carbon;
        $this->annotationsReader = $annotationsReader;
    }

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return ['postLoad'];
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity =  $args->getEntity();

        $refl = new \ReflectionClass($entity);
        $props = $refl->getProperties();

        $pa = PropertyAccess::createPropertyAccessor();

        foreach ($props as $prop) {
            if ($carbonAnnot = $this->annotationsReader->getPropertyAnnotation($prop, 'MNC\DoctrineCarbonBundle\Annotations\Carbon')) {
                if (
                    $pa->getValue($entity, $prop->getName()) !== null
                    && $pa->isWritable($entity, $prop->getName())
                    && $pa->isReadable($entity, $prop->getName())
                    && $pa->getValue($entity, $prop->getName()) instanceof \DateTime
                    && !$pa->getValue($entity, $prop->getName()) instanceof Carbon
                ) {
                    $pa->setValue($entity, $prop->getName(), $this->carbon::instance($pa->getValue($entity, $prop->getName())));
                }
            }
        }
    }

}