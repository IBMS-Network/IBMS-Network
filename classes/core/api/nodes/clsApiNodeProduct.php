<?php
	class clsApiNodeProduct  extends clsApiNodeParser {
		
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
            if ($oParentParentNode->getNodeName() == 'product_group') {
                $aParentData = $oParentParentNode->getData();
                clsApiParser::$productsTmp[$aParentData['id']][] = array($this->getNodeName() => $this->getData());
            } elseif($oParentParentNode->getNodeName() == 'order') {
                $aParentData = $oParentParentNode->getData();
                $args = array('item_id' => (int)$aParentData['id'],
                              'from' => 'order');
                $this->oApi->callParser($aData, $args);
            }
            
            return true;
		}
		
		public function allowClean() {
			$oParentNode = $this->getParentNode();
			
			if (empty($oParentNode)) {
				return true;
			}
			
			return $oParentNode->allowClean();
		}        
	}    

