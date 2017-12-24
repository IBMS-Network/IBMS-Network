<?php

class clsApiNodeCategory extends clsApiNodeParser {

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
        if ($oParentParentNode->getNodeName() == 'metacategory') {
            $aParentData = $oParentParentNode->getData();
            $args = array('meta_category_id' => (int) $aParentData['id']);
        }

        clsApiParser::$categories[] = array($this->getNodeName() => $this->getData());
    }

    public function allowClean() {

        return true;
    }

}