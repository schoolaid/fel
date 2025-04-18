<?php

namespace Schoolaid\Fel\Xml\Elements;

use Schoolaid\Fel\Exceptions\XmlGenerationException;
use Schoolaid\Fel\Xml\Builder\XmlDocumentBuilder;
use Schoolaid\Fel\Xml\Contracts\XmlSerializable;
use Schoolaid\Fel\Xml\Enums\DocumentXmlTags;

class GTDocument implements XmlSerializable
{
    protected XmlDocumentBuilder $builder;
    protected SATElement $sat;
    
    public function __construct(SATElement $sat)
    {
        $this->builder = new XmlDocumentBuilder();
        $this->sat = $sat;
    }

    /**
     * @throws XmlGenerationException
     */
    public function asXML(): string
    {
        $namespaces = [
            'ds' => 'http://www.w3.org/2000/09/xmldsig#',
            'dte' => 'http://www.sat.gob.gt/dte/fel/0.2.0',
            'xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
        ];
        
        $attributes = [
            DocumentXmlTags::Version->value => '0.1',
            DocumentXmlTags::SchemaLocation->value => 'http://www.sat.gob.gt/dte/fel/0.1.0'
        ];
        
        return $this->builder->buildDocument(
            $this->getXmlTagName(),
            $attributes,
            $this->sat->asXML(),
            $namespaces
        );
    }
    
    public function getXmlTagName(): string
    {
        return DocumentXmlTags::GTDocument->value;
    }
} 