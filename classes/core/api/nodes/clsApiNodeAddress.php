<?php
class clsApiNodeAddress extends clsApiNodeParser {

    public function parse(){

        $oParentNode = $this->getParentNode();

        if (empty($oParentNode)) {
            return;
        }
        $aData = array( $oParentNode->getNodeName() => array (
            $this->getNodeName() => $this->getData()
        ));
        
        if($oParentNode->getNodeName() != 'company') {
            clsApiParser::$userAddresses[] = $this->getData();
        }
    }

    public function allowClean() {

        return true;
    }
}