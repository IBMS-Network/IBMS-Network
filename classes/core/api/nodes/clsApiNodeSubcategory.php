<?php
class clsApiNodeSubcategory extends clsApiNodeParser {

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
        if ($oParentParentNode->getNodeName() == 'data') {
//            $aParentData = $oParentParentNode->getData();
//            $args = array('category_id' => (int)$aParentData['id']);
            $this->oApi->callParser($aData);
        } else {
            clsApiParser::$subcategories[] = array($this->getNodeName() => $this->getData());
        }
    }

    public function allowClean() {

        return true;
    }
}