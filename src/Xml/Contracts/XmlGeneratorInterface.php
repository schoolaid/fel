<?php

namespace Schoolaid\Fel\Xml\Contracts;

/**
 * Interface for XML generators
 */
interface XmlGeneratorInterface
{
    /**
     * Generate an XML string from a model
     *
     * @param mixed $model
     * @return string
     */
    public function generate($model): string;
} 