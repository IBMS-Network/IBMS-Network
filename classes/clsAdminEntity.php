<?php
/**
 * Admin entity class
 * @author Anatoly.Bogdanov
 *
 */
namespace engine;

use classes\core\clsDB;
use classes\core\clsCommon;
use classes\core\clsError;

abstract class clsAdminEntity {

    protected $errors;

    function __construct() {
        $this->errors = clsError::getInstance();
    }

    /**
     * Building of filter clause for query
     * @param array $filter
     * array of filters, where keys - filter names
     * @param string $tName
     * alias of table, if exist
     * @param string $entityName
     * entity name for searching existing column names of table
     * @param array $intFields
     * that need an exact match
     * @param array $filterOr
     * array of filters, where keys - filter names (implode by OR)
     * @return string
     * sub query
     */
    protected function getElmFilter($filter, $entityName, $tName = '', $intFields = array(), $filterOr = array())
    {
        $whereClause = '';
        if (!empty($filter) || !empty($filterOr)) {
            $cm = clsDB::getInstance()->getClassMetadata($entityName);
            $tableFields = array_merge($cm->getFieldNames(), $cm->getAssociationNames());
            if (!empty($tName)) {
                $tName .= '.';
            }
//            var_dump($tName);
            $whereClause = $this->parseFilters($filter, $tableFields, $tName, $intFields, 'AND');
            $whereClauseOr = $this->parseFilters($filterOr, $tableFields, $tName, $intFields, 'OR');

            if (!empty($whereClause) && !empty($whereClauseOr)){
                $whereClause .= ' AND (' . $whereClauseOr . ')';
            } elseif (!empty($whereClauseOr)) {
                $whereClause = $whereClauseOr;
            }
        }
        return $whereClause;
    }

    /**
     * parse filters array to where string
     * @param array $filter
     * filter : key - field name, value - field value
     * @param $tableFields
     * existing table fields
     * @param $tName
     * alias of table
     * @param $intFields
     * that need an exact match
     * @param string $implode
     * AND | OR
     * @return string
     * where string to clause
     */
    protected function parseFilters($filter, $tableFields, $tName, $intFields, $implode = 'AND')
    {
        $arWhereClause = array();
        $whereClause = '';
        if (!empty($filter) && is_array($filter)) {
            foreach ($filter as $kFilter => $vFilter) {
                if (in_array($kFilter, $tableFields) && (trim($vFilter) != '' || is_null($vFilter))) {
                    if (is_null($vFilter)) {
                        $arWhereClause[] = $tName . $kFilter . " IS NULL ";
                    } else {
                        if (in_array($kFilter, $intFields)) {
                            $exprWhere = '=';
                            $exprValue = clsCommon::isInt($vFilter);
                        } else {
                            $exprWhere = 'LIKE';
                            $exprValue = '%' . $vFilter . '%';
                        }
                        $arWhereClause[] = $tName . $kFilter . " " . $exprWhere . " '" . $exprValue . "'";
                    }
                }
            }
            $whereClause = implode(" " . $implode . " ", $arWhereClause);
        }
//        var_dump($whereClause);
        return $whereClause;
    }
}