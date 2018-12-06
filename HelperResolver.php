<?php

namespace Peak\View;

use Peak\Blueprint\Common\ResourceResolver;
use Peak\View\Exception\InvalidHelperException;
use Psr\Container\ContainerInterface;

/**
 * Class HelperResolver
 * @package Peak\View
 */
class HelperResolver implements ResourceResolver
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * HelperResolver constructor.
     * @param ContainerInterface|null $container
     */
    public function __construct(?ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param mixed $helper
     * @return mixed|object
     * @throws InvalidHelperException
     * @throws \ReflectionException
     */
    public function resolve($helper)
    {
        if (is_string($helper)) {
            $closure = $this->resolverString($helper);
        }

        if (!is_object($helper)) {
            throw new InvalidHelperException($closure);
        }

        return $helper;
    }

    /**
     * @param $helper
     * @return mixed|object
     * @throws \ReflectionException
     */
    public function resolverString($helper)
    {
        // resolve using a container
        if (null !== $this->container) {
            if ($this->container->has($helper)) { // psr-11
                $helperInstance = $this->container->get($helper);
            } elseif ($this->container instanceof \Peak\Di\Container) {
                $helperInstance = $this->container->create($helper);
            }
        } elseif (class_exists($helper)) {
            $helperInstance = new $helper();
        }

        return $helperInstance;
    }
}