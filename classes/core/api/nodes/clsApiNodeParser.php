<?php
	class clsApiNodeParser {
		protected $oApi = false;
		private $sNodeName = '';
		private $aNodeData = array();
		private $oParentNode = NULL;

		public function __construct() {
		}

		public function __destruct() {
			unset($this->aNodeData);
		}

		/**
		* Set Api
		* 
		* @param clsApiCore $api
		*/
		public function setApi($oApi) {
			$this->oApi = $oApi;
		}
		
		public function start($sNodeName, $oParentNode){
//			dbg(__METHOD__);
			$this->sNodeName = $sNodeName;
			$this->oParentNode = $oParentNode;
		}

		public function parse(){
			if (isset($this->aNodeData[0]) && is_string($this->aNodeData[0]) && count($this->aNodeData) == 1) {
				return false;   
			} else {
				//dbg($this->sNodeName );
				/*
				if ($this->sNodeName == 'subcategory') {
					//dbg('parse: ' . $this->sNodeName . ', data: ' . print_r($this->getData(), true));
				}           
				*/
				//unset($this->aNodeData);
				return true;
			}
		}   

		public function addData($sValue) {
			$this->aNodeData[] = $sValue;
		}

		public function getData() {
			$result = array();
			if (isset($this->aNodeData[0]) && is_string($this->aNodeData[0]) && count($this->aNodeData) == 1) {
				$result = reset($this->aNodeData);
			} else {
				foreach($this->aNodeData as $oItem) {
					$nodeName = $oItem->getNodeName();
					if (isset($result[$nodeName]) && is_array($result[$nodeName]) && isset($result[$nodeName][0])) {
						$result[$nodeName][] = $oItem->getData();
					} elseif (!empty($result[$nodeName])) {
						$result[$nodeName] = array($result[$nodeName], $oItem->getData());
					} else {
						$result[$nodeName] = $oItem->getData();
					}
				}
			}
			return $result;
		}

		public function getNodeName() {
			return $this->sNodeName;
		}

		public function allowClean() {
			$result = false;

			/*
			// clean test
			switch($this->sNodeName){
				case 'product':
				case 'newsitem':
				case 'client':
				case 'order':
				case 'block':                
				case 'promotion':
				case 'action':
				case 'metacategory':
				case 'category':
				case 'synonym':
				case 'secondary':
					$result = true;
					break;
				default:
					//dbg($this->sNodeName);
			}
			*/
			
			return $result;
		}        
		
		public function getParentNode() {
			return $this->oParentNode;
		}
	}
