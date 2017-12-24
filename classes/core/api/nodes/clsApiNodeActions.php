<?php
	class clsApiNodeActions extends clsApiNodeParser {
		public function parse() {
			$mData = $this->getData();
			if (is_array($mData) && isset($mData['action'])) {
				$this->oApi->setActions(array(
					$mData['action']
				));
			}
		}
		
		public function allowClean() {
			return true;            
		}
		
	}