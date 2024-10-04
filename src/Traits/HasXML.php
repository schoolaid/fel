<?php
namespace SchoolAid\FEL\Traits;

trait HasXML
{
    public function buildXML(string $rootElement, array $attributes = [], string | array $element = []): string
    {
        $xw = xmlwriter_open_memory();
        xmlwriter_set_indent($xw, 1);

        xmlwriter_start_element($xw, $rootElement);

        foreach ($attributes as $key => $value) {
            xmlwriter_start_attribute($xw, $key);
            if (is_array($value)) {
                foreach ($value as $subElementName => $subElementValue) {
                    xmlwriter_start_element($xw, $subElementName);
                    xmlwriter_text($xw, $subElementValue);
                    xmlwriter_end_element($xw);
                }
            } else {
                xmlwriter_text($xw, $value);

            }
            xmlwriter_end_attribute($xw);
        }

        if (is_string($element)) {
            xmlwriter_write_raw($xw, $element);
        }

        if (is_array($element)) {
            foreach ($element as $subElementName => $subElementValue) {
                xmlwriter_start_element($xw, $subElementName);
                if (is_string($subElementValue)) {
                    xmlwriter_write_raw($xw, $subElementValue);
                }
                if (is_array($subElementValue)) {
                    foreach ($subElementValue as $subElementName => $subElementValue) {
                        xmlwriter_start_element($xw, $subElementName);
                        xmlwriter_text($xw, $subElementValue);
                        xmlwriter_end_element($xw);
                    }

                } else {
                    xmlwriter_text($xw, $subElementValue);
                }
                xmlwriter_end_element($xw);
            }
        }

        xmlwriter_end_element($xw);

        return xmlwriter_output_memory($xw);
    }

}
