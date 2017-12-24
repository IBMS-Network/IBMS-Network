<?php

class clsApiNodeAttribute extends clsApiNodeParser {

    public function parse() {

        $oParentNode = $this->getParentNode();

        if (empty($oParentNode)) {
            return;
        }

        $oParentParentNode = $oParentNode->getParentNode();

        $aData = array($oParentNode->getNodeName() => array(
                $this->getNodeName() => $this->getData()
                ));

        $data = $this->getData();
        $aParentData = $oParentParentNode->getData();
        clsApiParser::$attributesTmp[$oParentParentNode->getNodeName()][$aParentData['id']][] = $aData;
        if ($oParentParentNode->getNodeName() == 'product') {
            if (isset($data['values'])) {
                $values = $this->oApi->getArrayNode($data['values']['value']);
                foreach ($values as $value) {
                    if (isset(clsApiParser::$attributesTmp['attributes'][$data['id']])
                            && isset(clsApiParser::$attributesTmp['attributes'][$data['id']]['values'])) {

                        if (!in_array($value, clsApiParser::$attributesTmp['attributes'][$data['id']]['values']['value'])) {
                            clsApiParser::$attributesTmp['attributes'][$data['id']]['values']['value'][]
                                    = $value;
                        }
                    } else {
                        $data['values']['value'] = $this->oApi->getArrayNode($data['values']['value']);
                        clsApiParser::$attributesTmp['attributes'][$data['id']] = $data;
                    }
                }
            }
        }
    }

    public function allowClean() {

        return true;
    }

}