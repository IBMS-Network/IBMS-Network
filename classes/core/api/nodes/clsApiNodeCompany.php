<?php
class clsApiNodeCompany extends clsApiNodeParser {

    public function parse(){

        $oParentNode = $this->getParentNode();

        if (empty($oParentNode)) {
            return;
        }

        $aData = array( $oParentNode->getNodeName() => array (
            $this->getNodeName() => $this->getData()
        ));
        
        clsApiParser::$userCompanies[] = $this->getData();
    }

    public function allowClean() {

        return true;
    }
}