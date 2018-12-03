<?php

namespace Drupal\nodeshop_export\Export;

/**
 * Class lightshop export row item
 *  Handles the export of a single row item
 *
 */
class RowItem
{

    //The Item
    public $item;
    //The temporary Value of the item. Is not to be set from outside and only for validating purposes
    private $tempvalue;
    //The clean and validated Value of the item
    private $value;
    //The Key of the Value
    private $key;
    //The length of the value
    private $length;
    //The type of the value eg string or int
    private $type;
    //The validation of the value eg regexp or custom function
    private $validation;
    //The expression for validating the value
    private $expression;

    /**
     * initialize the value with the given parameters
     *
     */
    public function __construct($key, $length, $type, $validation, $expression)
    {
        $this->key = $key;
        $this->length = $length;
        $this->type = $type;
        $this->validation = $validation;
        $this->expression = $expression;
        $this->value = ';';
    }

    /**
     * sets the value of the item - only if it validates
     *
     */
    public function set($value)
    {
        $this->tempvalue = $value;
        if ($this->validate()) {
            $this->value = trim($this->tempvalue).';';
        } else {
            throw new Exception('Not a valid Value: '.$value.' for given key: '.$this->key);
        }
    }

    /**
     * Validates a single item
     *  based on the given information - e.g. the type of validation
     *
     * @return
     *  Return value of called function
     */
    private function validate()
    {
        switch ($this->type) {
            case 'custom':
                return $this->validatecustom();
            break;
            case 'int':
                return $this->validateint();
            break;
            case 'string':
            default:
                return $this->validatestring();
            break;
        }
    }

    /**
     * Validates a single item
     *  based on the given regular expression or a called user function
     *
     * @return
     *  Boolean TRUE if item validates, false otherwise.
     *  Throws an exeption if no expression is given
     */
    private function validatecustom()
    {
        if (!empty($this->expression)) {
            if (!empty($this->validation) && function_exists($this->validation)) {
                return $this->validation($this->expression, $this->tempvalue);
            } else {
                return preg_match($this->expression, $this->tempvalue);
            }
        }
        throw new Exception('Unknown Custom Validation format');
    }

    /**
     * Validates a single integer item
     *
     * @return
     *  Boolean TRUE if item validates, false otherwise.
     */
    private function validateint()
    {
        if (!empty($this->expression)) {
            return $this->validatecustom();
        } else {
            return (is_int($this->tempvalue) || preg_match("/^\d+$/", $this->tempvalue));
        }
    }

    /**
     * Validates a single string
     *
     * @return
     *  Boolean TRUE if item validates, false otherwise.
     */
    private function validatestring()
    {
        if (!empty($this->expression)) {
            return $this->validatecustom();
        } else {
            return is_string($this->tempvalue);
        }
    }

    /**
     * Get a single row item
     *
     * @return
     *  The value of the item
     */
    public function get()
    {
        return $this->value;
    }
}
