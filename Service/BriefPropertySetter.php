<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 23.7.15
 * Time: 18.27
 */

namespace Soil\CommentsDigestBundle\Service;


use Doctrine\Common\Annotations\AnnotationReader;
use EasyRdf\Literal;
use EasyRdf\Resource;
use Soil\DiscoverBundle\Service\Resolver;

class BriefPropertySetter {

    /**
     * @var Resolver
     */
    protected $resolver;

    /**
     * @var AnnotationReader
     */
    protected $annotationReader;

    public function __construct($resolver)  {
        $this->resolver = $resolver;
        $this->annotationReader = new AnnotationReader();
    }

    public function setBriefProperty($object, $name, $value, $expectedClass = null)   {
        $reflection = new \ReflectionClass($object);

        if (!$reflection->hasProperty($name)) return false;

        $property = $reflection->getProperty($name);
        $property->setAccessible(true);


        if ($value instanceof Resource) {
            $value = $value->getUri();

            $annotation = $this->annotationReader->getPropertyAnnotation($property, 'Soil\CommentsDigestBundle\Annotation\Entity');

            if ($annotation)    {
                $expectedClass = $annotation->value ?: true;
                var_dump($expectedClass);

                try {
                    $entity = $this->resolver->getEntityForURI($value, $expectedClass);
                }
                catch (\Exception $e)   {
                    $entity = null;
                    echo "Problem with discovering";
                    var_dump($value, $expectedClass);
                    //FIXME: Add logging
                }

                if ($entity)    {
                    $value = $entity;
                }
            }


        }
        elseif ($value instanceof Literal)  {
            $value = $value->getValue();
        }

        $property->setValue($object, $value);
    }
} 