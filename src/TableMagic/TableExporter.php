<?php

namespace ChernegaSergiy\TableMagic;

use Exception;
use SimpleXMLElement;

class TableExporter
{
    private Table $table;

    /**
     * TableExporter constructor.
     *
     * @param  Table  $table  The table to be exported.
     */
    public function __construct(Table $table)
    {
        $this->table = $table;
    }

    /**
     * Exports the table in the specified format.
     *
     * @param  string  $format  The format to export the table ('html', 'csv', 'json', 'xml').
     * @return string The exported table data as a string.
     *
     * @throws Exception If the format is unsupported.
     */
    public function export(string $format) : string
    {
        switch (strtolower($format)) {
            case 'html':
                return $this->toHtml();
            case 'csv':
                return $this->toCsv();
            case 'json':
                return $this->toJson();
            case 'xml':
                return $this->toXml();
            default:
                throw new Exception("Unsupported format: $format");
        }
    }

    /**
     * Converts the table to HTML format.
     *
     * @return string The HTML representation of the table.
     */
    protected function toHtml() : string
    {
        $html = '<table border="1"><thead><tr>';
        foreach ($this->table->headers as $header) {
            $html .= "<th>{$header}</th>";
        }
        $html .= '</tr></thead><tbody>';
        foreach ($this->table->getRows() as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= "<td>{$cell}</td>";
            }
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';

        return $html;
    }

    /**
     * Converts the table to CSV format.
     *
     * @return string The CSV representation of the table.
     *
     * @throws Exception If an error occurs during CSV conversion.
     */
    protected function toCsv() : string
    {
        $output = $this->openTemporaryStream();
        if (false === $output) {
            throw new Exception('Failed to open temporary stream');
        }
        fputcsv($output, $this->table->headers);
        foreach ($this->table->getRows() as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $csv_data = stream_get_contents($output);
        fclose($output);

        return (string) $csv_data;
    }

    /**
     * @return resource|false
     */
    protected function openTemporaryStream()
    {
        return fopen('php://temp', 'r+');
    }

    /**
     * Converts the table to JSON format.
     *
     * @return string The JSON representation of the table.
     *
     * @throws Exception If an error occurs during JSON conversion.
     */
    protected function toJson() : string
    {
        $data = [
            'headers' => $this->table->headers,
            'rows' => $this->table->getRows(),
        ];

        $json = json_encode($data);
        if (false === $json) {
            throw new Exception('Failed to encode data to JSON');
        }

        return $json;
    }

    /**
     * Converts the table to XML format.
     *
     * @return string The XML representation of the table.
     *
     * @throws Exception If an error occurs during XML conversion.
     */
    protected function toXml() : string
    {
        $xml = $this->createSimpleXMLElement('<table/>');


        $headers_element = $this->addChildToElement($xml, 'headers');
        if (false === $headers_element || null === $headers_element) {
            throw new Exception('Failed to add headers element to XML');
        }
        foreach ($this->table->headers as $header) {
            $child = $this->addChildToElement($headers_element, 'header', $header);
            if (false === $child || null === $child) {
                throw new Exception('Failed to add header child element to XML');
            }
        }
        $rows_element = $this->addChildToElement($xml, 'rows');
        if (false === $rows_element || null === $rows_element) {
            throw new Exception('Failed to add rows element to XML');
        }
        foreach ($this->table->getRows() as $row) {
            $row_xml = $this->addChildToElement($rows_element, 'row');
            if (false === $row_xml || null === $row_xml) {
                throw new Exception('Failed to add row element to XML');
            }
            foreach ($row as $key => $cell) {
                if (isset($this->table->headers[$key])) {
                    $child = $this->addChildToElement($row_xml, $this->table->headers[$key], htmlspecialchars($cell));
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
