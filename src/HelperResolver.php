<?php

declare(strict_types=1);

namespace Peak\View;

use Peak\Blueprint\Common\ResourceResolver;
use Peak\View\Exception\InvalidHelperException;
use Psr\Container\ContainerInterface;

use function class_exists;
use function is_string;
use function is_object;

class HelperResolver implements ResourceResolver
{
    /**
     * @var ContainerInterface|null
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
     * @return mixed|string
     * @throws InvalidHelperException
     */
    public function resolve($helper)
    {
        if (is_string($helper)) {
            $helper = $this->resolveString($helper);
        }

        if (!is_object($helper)) {
            throw new InvalidHelperException($helper);
        }

        return $helper;
    }

    /**
     * @param string $helper
     * @return mixed|string
     */
    private function resolveString(string $helper)
    {
        $helperInstance = $helper;

        // resolve using a container
        if (isset($this->container)) {
            $helperInstance = $this->container->get($helper);
        } elseif (class_exists($helper)) {
            $helperInstance = new $helper();
        }

        return $helperInstance;
    }
}
