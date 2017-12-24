<?php
	class clsApiNodeMetas extends clsApiNodeParser {
		
		public function parse(){
			
			$oParentNode = $this->getParentNode();
			
			if (empty($oParentNode)) {
				return;
			}
            
//            $oParentParentNode = $oParentNode->getParentNode();
			
			$aData = array( $oParentNode->getNodeName() => array (
				$this->getNodeName() => $this->getData()
			));
            
            $args = array();
            if($oParentNode->getNodeName() == 'page') {
                $aParentData = $oParentNode->getData();
                $args = array('page_type_id' => clsApiParser::getPageTypeIdByName('static'),
                              'item_id' => (int)$aParentData['id']);
            } elseif($oParentNode->getNodeName() == 'newsitem') {
                $aParentData = $oParentNode->getData();
                $args = array('page_type_id' => clsApiParser::getPageTypeIdByName('news'),
                              'item_id' => (int)$aParentData['id']);
            } elseif($oParentNode->getNodeName() == 'product_group') {
                $aParentData = $oParentNode->getData();
                $args = array('page_type_id' => clsApiParser::getPageTypeIdByName('product_group'),
                              'item_id' => (int)$aParentData['id']);
            } else {
                $aParentData = $oParentNode->getData();
                $args = array('page_type_id' => clsApiParser::getPageTypeIdByName('category'),
                              'item_id' => (int)$aParentData['id']);
                clsApiParser::$metas[] = array($aParentData['id'] => $this->getData());
            }
			$this->oApi->callParser($aData, $args);
		}
		
		public function allowClean() {
            
			return true;
		}
	}