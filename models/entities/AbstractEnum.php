<?php

namespace entities;

/**
 * Abstract ENUM type
 *
 * Class AbstractEnumType
 *
 * @package entities\Type
 */
abstract class AbstractEnum
{
    /**
     * @var string
     */
    protected static $name;

    /**
     * @var array
     */
    protected static $values = array();

    /**
     * Returns available values for this type
     * @param int $min
     * if exists, function return values from this number
     * @param int $max
     * if exists, function return values before this number
     * @return array
     */
    public static function getValues($min = 0, $max = 0)
    {
        $values = static::$values;
        $min = (int)$min;
        $max = (int)$max;
        if ($min > 0) {
            $values = array_slice($values, $min, NULL, true);
        }
        if ($max > 0 && $max > $min) {
            $values = array_slice($values, 0, ($max - $min), true);
        }
        return $values;
    }

    /**
     * Return assoc array with ('id','name')
     */
    public static function getValuesByAssoc(){
        $arr = static::getValues();
        $result = array();
        $i = 0;
        foreach($arr as $key=>$value){
            $result[$i]['id'] = $key;
            $result[$i]['name'] = $value;
            $i++;
        }
        return $result;
    }

    /**
     * Name of the Type
     *
     * @return string
     */
    public function getName()
    {
        return static::$name;
    }
    
    /**
     * Return key of constant value in values
     * 
     * @param string $constant
     * @return false|integer
     */
    public static function getValueByConstant($constant){
        
        return array_search($constant, static::$values);
    }
}
