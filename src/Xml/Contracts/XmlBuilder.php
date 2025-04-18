<?php
namespace Schoolaid\Fel\Xml\Contracts;

interface XmlBuilder
{
    /**
     *
     * @param string $elementName
     * @param array $attributes
     * @param string|array|null $children
     * @return string
     */
    public function buildElement(string $elementName, array $attributes = [], string|array|null $children = null): string;

    /**
     *
     * @param string $rootElement
     * @param array $attributes
     * @param string|array|null $children
     * @param array $namespaces
     * @return string
     */
    public function buildDocument(
        string $rootElement,
        array $attributes = [],
        string|array|null $children = null,
        array $namespaces = []
    ): string;
}