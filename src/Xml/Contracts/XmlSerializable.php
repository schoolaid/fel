<?php

namespace Schoolaid\Fel\Xml\Contracts;

interface XmlSerializable
{
    /**
     *
     * @return string
     */
    public function asXML(): string;

    /**
     *
     * @return string
     */
    public function getXmlTagName(): string;
}
