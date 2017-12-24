<?php

class clsApiNodeManufacturer extends clsApiNodeParser {

    public function parse() {

        $oParentNode = $this->getParentNode();

        if (empty($oParentNode)) {
            return;
        }
        
        $aData = array($oParentNode->getNodeName() => array(
                $this->getNodeName() => $this->getData()
                ));
        
        if($oParentNode->getNodeName() == 'product') {
            $aParentData = $oParentNode->getData();
            clsApiParser::$manufacturersProducts[$aParentData['id']] = $this->getData();
        } else {
            $this->oApi->callParser($aData);
        }
    }

    public function allowClean() {

        return true;
    }

}