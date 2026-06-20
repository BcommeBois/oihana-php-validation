<?php

namespace oihana\validations\rules\abstracts ;

use oihana\logging\LoggerTrait;
use oihana\traits\ToStringTrait;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Somnambulist\Components\Validation\Rule;

/**
 * An abstract rule to defines rules with an internal DI container reference.
 */
abstract class ContainerRule extends Rule
{
    /**
     * Creates a new ContainerRule instance.
     *
     * @param ContainerInterface $container The DI container reference.
     * @param array              $init      The options to passed-in the rule.
     *
     * @throws ContainerExceptionInterface If the container encounters an error while retrieving the entry.
     * @throws NotFoundExceptionInterface If no entry was found for the given identifier in the container.
     */
    public function __construct( ContainerInterface $container , array $init = [] )
    {
        $this->container = $container ;
        $this->initializeLogger( $init , $container ) ;
    }

    use LoggerTrait ,
        ToStringTrait ;

    /**
     * The DI container reference.
     */
    protected ContainerInterface $container;
}