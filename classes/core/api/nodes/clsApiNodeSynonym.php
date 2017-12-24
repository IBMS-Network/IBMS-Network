<?php

class clsApiNodeSynonym extends clsApiNodeParser {

    public function parse() {

        $oParentNode = $this->getParentNode();

        if (empty($oParentNode)) {
            return;
        }

        $oParentParentNode = $oParentNode->getParentNode();

        $aData = array($oParentNode->getNodeName() => array(
                $this->getNodeName() => $this->getData()
                ));

        $args = array();
        $aParentData = $oParentParentNode->getData();
        if ($oParentParentNode->getNodeName() == 'category') {
//            $args = array('entity_type_id' => clsApiParser::getEntityTypeIdByName('category'),
//                          'item_id' => (int)$aParentData['id']);
            clsApiParser::$categorySynonyms[$aParentData['id']][] = array($this->getNodeName() => $this->getData());
        } elseif ($oParentParentNode->getNodeName() == 'subcategory') {
            clsApiParser::$subcategorySynonyms[$aParentData['id']][] = array($this->getNodeName() => $this->getData());
        } elseif ($oParentParentNode->getNodeName() == 'metacategory') {
            clsApiParser::$metacategorySynonyms[$aParentData['id']][] = array($this->getNodeName() => $this->getData());
        } else {
            clsApiParser::$productSynonyms[$aParentData['id']][] = array($this->getNodeName() => $this->getData());
        }

        $this->oApi->callParser($aData, $args);
    }

    public function allowClean() {

        return true;
    }

}