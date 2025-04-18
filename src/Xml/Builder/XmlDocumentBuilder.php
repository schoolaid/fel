<?php

namespace Schoolaid\Fel\Xml\Builder;

use Schoolaid\Fel\Exceptions\XmlGenerationException;
use Schoolaid\Fel\Xml\Contracts\XmlBuilder;

class XmlDocumentBuilder implements XmlBuilder
{
    /**
     * {@inheritdoc}
     * @throws XmlGenerationException
     */
    public function buildElement(
        string            $elementName,
        array             $attributes = [],
        string|array|null $children = null
    ): string
    {
        try {
            $xw = xmlwriter_open_memory();
            xmlwriter_set_indent($xw, 1);
            xmlwriter_set_indent_string($xw, '    ');

            xmlwriter_start_element($xw, $elementName);

            foreach ($attributes as $name => $value) {
                if ($value !== null && $value !== '') {
                    xmlwriter_write_attribute($xw, $name, (string)$value);
                }
            }

            $this->writeChildren($xw, $children);

            xmlwriter_end_element($xw);

            return xmlwriter_output_memory($xw);
        } catch (\Throwable $e) {
            throw new XmlGenerationException(
                "Error generando elemento XML '{$elementName}': " . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * {@inheritdoc}
     * @throws XmlGenerationException
     */
    public function buildDocument(
        string            $rootElement,
        array             $attributes = [],
        string|array|null $children = null,
        array             $namespaces = []
    ): string
    {
        try {
            $xw = xmlwriter_open_memory();
            xmlwriter_set_indent($xw, 1);
            xmlwriter_set_indent_string($xw, '    ');

            xmlwriter_start_document($xw, '1.0', 'UTF-8');

            xmlwriter_start_element($xw, $rootElement);

            foreach ($namespaces as $prefix => $uri) {
                $nsAttribute = $prefix === '' ? 'xmlns' : "xmlns:{$prefix}";
                xmlwriter_write_attribute($xw, $nsAttribute, $uri);
            }

            foreach ($attributes as $name => $value) {
                if ($value !== null && $value !== '') {
                    xmlwriter_write_attribute($xw, $name, (string)$value);
                }
            }

            $this->writeChildren($xw, $children);

            xmlwriter_end_element($xw);
            xmlwriter_end_document($xw);

            return xmlwriter_output_memory($xw);
        } catch (\Throwable $e) {
            throw new XmlGenerationException(
                "Error generando documento XML: " . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Escribe elementos hijos en un documento XML
     *
     * @param resource $xw Recurso XMLWriter
     * @param string|array|null $children Contenido hijo a escribir
     */
    protected function writeChildren($xw, string|array|null $children): void
    {
        if (is_string($children) && !empty($children)) {
            xmlwriter_write_raw($xw, $children);
        } elseif (is_array($children)) {
            foreach ($children as $name => $value) {
                if ($value === null || $value === '') {
                    continue;
                }

                if (is_string($name) && !is_numeric($name)) {
                    xmlwriter_start_element($xw, $name);

                    if (is_array($value)) {
                        $this->writeChildren($xw, $value);
                    } elseif (is_string($value) && $this->isXmlString($value)) {
                        xmlwriter_write_raw($xw, $value);
                    } else {
                        xmlwriter_text($xw, (string)$value);
                    }

                    xmlwriter_end_element($xw);
                } else {
                    if (is_string($value)) {
                        xmlwriter_write_raw($xw, $value);
                    }
                }
            }
        }
    }

    /**
     * Validate if a string is a valid XML string
     *
     * @param string $string
     * @return bool
     */
    protected function isXmlString(string $string): bool
    {
        return preg_match('/<\w+(\s+[^>]*)?>(.*?)<\/\w+>/', $string) === 1 ||
            preg_match('/<\w+(\s+[^>]*)?\/?>/', $string) === 1;
    }
}