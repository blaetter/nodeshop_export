<?php

namespace Drupal\nodeshop_export\Export;

/**
 * Class nodeshop export skeleton item
 *  provides the skeleton of a single row item
 *
 */
class SkeletonItem
{
    public $key;
    public $csv_title;
    public $accessable;
    public $validation;
    public $expression;
    public $length;
    public $type;

    /**
     * initialize the value with the given parameters
     *
     */
    public function __construct($key, $csv_title, $accessable, $validation, $expression, $length, $type)
    {
        $this->key = $key;
        $this->csv_title = $csv_title;
        $this->accessable = $accessable;
        $this->length = $length;
        $this->type = $type;
        $this->validation = $validation;
        $this->expression = $expression;

        return $this;
    }

    /**
     * Returns the skeleton item as array
     *
     * @return array
     */
    public function getSkeletonItemArray()
    {
        return [
            'key' => $this->key,
            'csv_title' => $this->csv_title,
            'accessable' => $this->accessable,
            'length' => $this->length,
            'type' => $this->type,
            'validation' => $this->validation,
            'expression' => $this->expression
        ];
    }
}
