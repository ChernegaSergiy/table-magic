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
        foreach ($this->table->rows as $row) {
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
     * @throws Exception
     */
    protected function toCsv() : string
    {
        $output = fopen('php://temp', 'r+');
        if (false === $output) {
            throw new Exception('Failed to open temporary stream');
        }
        fputcsv($output, $this->table->headers);
        foreach ($this->table->rows as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $data = stream_get_contents($output);
        if (false === $data) {
            throw new Exception('Failed to read from temporary stream');
        }
        fclose($output);

        return $data;
    }

    /**
     * Converts the table to JSON format.
     *
     * @return string The JSON representation of the table.
     * @throws Exception
     */
    protected function toJson() : string
    {
        $data = [
            'headers' => $this->table->headers,
            'rows' => $this->table->rows,
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
     * @throws Exception
     */
    protected function toXml() : string
    {
        $xml = new SimpleXMLElement('<table/>');
        $headers_element = $xml->addChild('headers');
        foreach ($this->table->headers as $header) {
            $headers_element->addChild('header', $header);
        }
        $rows_element = $xml->addChild('rows');
        foreach ($this->table->rows as $row) {
            $row_xml = $rows_element->addChild('row');
            foreach ($row as $key => $cell) {
                if (isset($this->table->headers[$key])) {
                    $row_xml->addChild($this->table->headers[$key], htmlspecialchars($cell));
                }
            }
        }

        $data = $xml->asXML();
        if (false === $data) {
            throw new Exception('Failed to generate XML');
        }

        return $data;
    }
}
