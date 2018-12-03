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
        foreach ($this->data as $data) {
            $row = new Row($data);
            $this->export[] = $row->get();
        }
    }

    /**
     * Get the export object
     *
     * @return
     *  The export object
     */
    public function get()
    {
        return $this->export;
    }
}
