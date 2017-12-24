<?php
/**
 * Created by PhpStorm.
 * User: BV
 * Date: 10.10.14
 * Time: 2:22
 */

namespace classes;

use PHPExcel_Cell;

class clsExcelProduct2
{

    private $num = 0;
    protected $product = null;
    protected $columns = [
        'A' => 'brand',
        'B' => 'cat_enemy',
        'C' => 'cat',
        'D' => 'cat2'
    ];

    public function __constructor($num)
    {
        $this->num = $num;
    }

    public function __call($name, $arguments)
    {
        $property = array_pop($arguments);
        $value = array_pop($arguments);
        if (array_key_exists($property, $this->columns)) {
            $this->product[$this->num][$this->columns[$property]] = trim(mb_strtolower($value, 'UTF-8'));
        }
    }

    public function getData(){
        return array_shift($this->product);
    }

} 