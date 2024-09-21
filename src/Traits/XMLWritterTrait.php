<?php

namespace SchoolAid\FEL\Traits;

trait XMLWritterTrait
{
    public function buildXML(string $rootElement, array $attributes, string $type): string
    {
        $xw = xmlwriter_open_memory();
        xmlwriter_set_indent($xw, 1);
        xmlwriter_start_element($xw, $rootElement);

        if ($type === 'element') {
            foreach ($attributes as $key => $value) {
                xmlwriter_start_element($xw, $key);
                xmlwriter_text($xw, $value);
                xmlwriter_end_element($xw);
            }
            xmlwriter_end_element($xw);
        } else {
            foreach ($attributes as $key => $value) {
                xmlwriter_start_attribute($xw, $key);
                xmlwriter_text($xw, $value);
                xmlwriter_end_attribute($xw);
            }
        }

        xmlwriter_end_element($xw);

        return xmlwriter_output_memory($xw);
    }
}
