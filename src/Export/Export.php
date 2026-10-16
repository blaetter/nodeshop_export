<?php

namespace Drupal\nodeshop_export\Export;

use Drupal\nodeshop_export\Export\Row;

/**
 * Class lightshop export
 *  Handles the export into blaetter db
 *
 */
class Export
{

    // the export object
    private $export;

    // the data that has to be converted into an export object
    private $data;

    // the problems of the values, that are exported unchanged
    private $warnings = [];

    public function __construct($data)
    {
        $this->export = [];
        $this->data = $data;
        $this->create();
    }

    /**
     * Creates a export with instances of rows in it
     *
     */
    private function create()
    {
        // create header row with empty data array
        $header = new Row([], true);
        $this->export[] = $header->get();
        foreach ($this->data as $data) {
            $row = new Row($data);
            $this->export[] = $row->get();
            $this->warnings = array_merge($this->warnings, $row->getWarnings());
        }
    }

    /**
     * Get the export object
     *
     * @return array $export
     *  The export object
     */
    public function get()
    {
        return $this->export;
    }

    /**
     * Returns the problems of the exported values, e.g. values, that shift the columns of the row.
     *
     * @return string[]
     */
    public function getWarnings()
    {
        return $this->warnings;
    }
}
