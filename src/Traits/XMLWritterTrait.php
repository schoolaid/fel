<?php

namespace SchoolAid\FEL\Traits;

trait XMLWritterTrait
{
    public function buildXML(string $rootElement, array $attributes = [], string|array $element = []): string
    {
        $xw = xmlwriter_open_memory();
        xmlwriter_set_indent($xw, 1);

        xmlwriter_start_element($xw, $rootElement);

        foreach ($attributes as $key => $value) {
            xmlwriter_start_attribute($xw, $key);
            xmlwriter_text($xw, $value);
            xmlwriter_end_attribute($xw);
        }

        if (is_string($element)) {
            xmlwriter_write_raw($xw, $element);
        }

        if (is_array($element)) {
            foreach ($element as $subElementName => $subElementValue) {
                xmlwriter_start_element($xw, $subElementName);
                xmlwriter_text($xw, $subElementValue);
                xmlwriter_end_element($xw);
            }
        }

        xmlwriter_end_element($xw);

        return xmlwriter_output_memory($xw);
    }
}
