<?php
class clsApiNodeProductgroup extends clsApiNodeParser {

    public function parse(){

        $oParentNode = $this->getParentNode();

        if (empty($oParentNode)) {
            return;
        }
        
        $oParentParentNode = $oParentNode->getParentNode();

        $aData = array( $oParentNode->getNodeName() => array (
            $this->getNodeName() => $this->getData()
        ));
        
        $args = array();
        if ($oParentParentNode->getNodeName() == 'subcategory') {
            $aParentData = $oParentParentNode->getData();
            $args = array('category_id' => (int)$aParentData['id']);
        }
        clsApiParser::$productsGroups[$aParentData['id']][] = array($this->getNodeName() => $this->getData());
    }

    public function allowClean() {

        return true;
    }
}