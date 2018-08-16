<?php

declare(strict_types=1);

namespace Peak\Common\Pipeline;

/**
 * Interface ProcessorInterface
 * @package Peak\Pipelines
 */
interface ProcessorInterface
{
    /**
     * Process pipes
     *
     * @param array $pipes
     * @param mixed $payload
     * @return mixed
     */
    public function process(array $pipes, $payload);
}