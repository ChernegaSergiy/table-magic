<?php

namespace ChernegaSergiy\TableMagic\Exporters;

use ChernegaSergiy\TableMagic\Interfaces\TableExporterInterface;
use ChernegaSergiy\TableMagic\Table;
use Exception;
use SimpleXMLElement;

class XmlTableExporter implements TableExporterInterface
{
    /**
     * Exports a Table object to an XML string representation.
     *
     * @param  Table  $table  The Table object to export.
     * @return string The XML string representation of the table.
     *
     * @throws Exception If an error occurs during XML conversion.
     */
    public function export(Table $table) : string
    {
        $xml = $this->createSimpleXMLElement('<table/>');

        $headers_element = $this->addChildToElement($xml, 'headers');
        if (false === $headers_element || null === $headers_element) {
            throw new Exception('Failed to add headers element to XML');
        }
        foreach ($table->headers as $header) {
            $child = $this->addChildToElement($headers_element, 'header', $header);
            if (false === $child || null === $child) {
                throw new Exception('Failed to add header child element to XML');
            }
        }
        $rows_element = $this->addChildToElement($xml, 'rows');
        if (false === $rows_element || null === $rows_element) {
            throw new Exception('Failed to add rows element to XML');
        }
        foreach ($table->getRows() as $row) {
            $row_xml = $this->addChildToElement($rows_element, 'row');
            if (false === $row_xml || null === $row_xml) {
                throw new Exception('Failed to add row element to XML');
            }
            foreach ($row as $key => $cell) {
                if (isset($table->headers[$key])) {
                    $child = $this->addChildToElement($row_xml, $table->headers[$key], htmlspecialchars($cell));
                    if (false === $child || null === $child) {
                        throw new Exception('Failed to add cell child element to XML');
                    }
                }
            }
        }

        $xml_output = $this->convertXmlToString($xml);
        if (false === $xml_output) {
            throw new Exception('Failed to generate XML');
        }

        return (string) $xml_output;
    }

    protected function createSimpleXMLElement(string $name) : SimpleXMLElement
    {
        return new SimpleXMLElement($name);
    }

    /**
     * @return SimpleXMLElement|false
     */
    protected function addChildToElement(SimpleXMLElement $element, string $name, ?string $value = null) : SimpleXMLElement|false|null
    {
        return $element->addChild($name, $value);
    }

    protected function convertXmlToString(SimpleXMLElement $element) : string|false
    {
        return $element->asXML();
    }
}
