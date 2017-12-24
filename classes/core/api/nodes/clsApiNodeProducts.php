<?php
	class clsApiNodeProducts extends clsApiNodeParser {
		
		public function allowClean() {

			$bResult = true;
			
			if (($oParentNode = $this->getParentNode())) {
				switch ($oParentNode->getNodeName()) {
					case 'order':
                    case 'promotion':
						$bResult = false;
						break;
				}
			}
			
			return $bResult;
			
		}
		
	}