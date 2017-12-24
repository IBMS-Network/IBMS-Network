<?php
class clsApiNodeClient extends clsApiNodeParser {

    public function parse(){

        $oParentNode = $this->getParentNode();

        if (empty($oParentNode)) {
            return;
        }

        $aData = array( $oParentNode->getNodeName() => array (
            $this->getNodeName() => $this->getData()
        ));

        $this->oApi->callParser($aData);
    }

    public function allowClean() {

        return true;
    }
}