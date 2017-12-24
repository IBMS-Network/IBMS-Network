<?php
	if (!function_exists('dbg')) {
		/**
		* Display value
		* 
		* @param mixed $value
		* @param mixed $vardump - true - use vardump, false - use print_r
		*/
		function dbg($value, $vardump = false) { 
			echo '<pre>'; 
			if ($vardump) { 
				var_dump($value); 
			} else { 
				print_r($value); 
			} 
			echo '</pre>'; 
		}
	}

	define('API_ERROR_CODE_NOTICE', 1);
	define('API_ERROR_CODE_WARNING', 2);
	define('API_ERROR_CODE_FATAL', 3);
	define('API_ERROR_CODE_ERROR', 4);

	define('API_LOG_TYPE_NOTICE', 1);
	define('API_LOG_TYPE_WARNING', 2);
	define('API_LOG_TYPE_ERROR', 3);

	class clsApiCore2 {

		/**
		* Start microtime
		* 
		* @var mixed
		*/
		protected $startTime = 0;

		/**
		* Errors list
		* 
		* @var array
		*/
		protected $errorsList = array();

		/**
		* Response array
		* 
		* @var array
		*/
		protected $response = array();

		/**
		* Actions list
		* 
		* @var array
		*/
		protected $actions = array();

		/**
		* Canstructor 
		* 
		*/
		public function __construct() {
			ob_start();
			$this->startTime = microtime(true);
		}

		/**
		* Convert array to xml
		* 
		* @param array $data
		* @param string $rootNodeName
		* @param SimpleXMLElement $xmlDoc
		* @param bool $isFirstLevel
		*/
		public function arrayToXml($data, $rootNodeName = 'data', $xmlDoc=null, $isFirstLevel = true) {
			if ($xmlDoc == null) {
				$xmlDoc = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><$rootNodeName />");
			}

			// add attrs to dom node
			$attrs = array();
			if (isset($data['@attributes'])) {
				$attrs = $data['@attributes'];
				foreach($attrs as $attrName=>$attrValue) {
					$xmlDoc->addAttribute($attrName, $attrValue);
				}
				unset($data['@attributes']);
			}


			foreach($data as $nodeKey => $nodeValue) {    
				if (is_numeric($nodeKey)) {
					$nodeKey = $rootNodeName;// get root node name for enums
				}

				// prepare node name
				$nodeKey = preg_replace('/[^a-z0-9\-\_\.\:]/i', '', $nodeKey);           

				if (is_array($nodeValue) || is_object($nodeValue)) {
					if (!isset($nodeValue[0])) { // check node type (num array or not)
						$newNode = $xmlDoc->addChild($nodeKey); // add child node
					} else {
						$newNode = $xmlDoc; // use curent node
					} 
					// build next level tree
					$this->arrayToXml($nodeValue, $nodeKey, $newNode, $xmlDoc, false);
				} else {                
					// TODO: mysql bit hack, FIX THIS!
					$isBitValue = false;
					if (is_string($nodeValue) 
						&& count($nodeValue) == 1 
						&& preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]+/', '', $nodeValue) != $nodeValue
					) {
						$nodeValue = ord($nodeValue); // bit to int
						$isBitValue = true;
					}                    
					$newNode = $xmlDoc->addChild($nodeKey, $nodeValue);
					if (defined('USE_DEBUG') && USE_DEBUG && $isBitValue) {
						$newNode->addAttribute('warning', 'type');
						$newNode->addAttribute('type', 'bit');
						$newNode->addAttribute('data', sprintf("%08b", $nodeValue));
					}                    
				}                

			}

			// return xml
			return ($isFirstLevel) ? $xmlDoc->asXML() : false;
		}

		/**
		* Parse xml 
		* 
		* @param string $xmlDoc
		* @returns DOMDocument|bool
		*/
		private function _parseXmlDoc($xmlDoc) {
			$domDoc = false;

			if (!empty($xmlDoc)) {
				// load xml 
				$domDoc = new DOMDocument();
				if (!$domDoc->loadXML($xmlDoc, LIBXML_NOERROR | LIBXML_NOWARNING | LIBXML_COMPACT )) {
					libxml_clear_errors();
					unset($domDoc);
					$domDoc = false;
				}
			}

			return $domDoc;
		}

		/**
		* Parse dom node
		* 
		* @param DOMNode $domNode
		* @returns array
		*/
		private function _parseDomNode($domNode) {
			$result = array();
			// build array
			switch ($domNode->nodeType) {
				case XML_CDATA_SECTION_NODE:
				case XML_TEXT_NODE: // is text node
					$result = trim($domNode->textContent);
					break;
				case XML_ELEMENT_NODE:
				case XML_DOCUMENT_NODE:
					// parse element nodes
					foreach($domNode->childNodes as $childNode) {
						$childData = $this->_parseDomNode($childNode);
						if(isset($childNode->tagName)) {
							$tagName = $childNode->tagName;
							if(!isset($result[$tagName])) {
								$result[$tagName] = array();
							}
							if (is_array($result)) {
								$result[$tagName][] = $childData;
							}
						}
						elseif($childData) {
							$result = (string) $childData;
						}
					}
					if(is_array($result)) {
						// parse attrs
						if(is_object($domNode->attributes) && $domNode->attributes->length) {
							$attrs = array();
							foreach($domNode->attributes as $attrName => $attrNode) {
								$attrs[$attrName] = (string) $attrNode->value;
							}
							$result['@attributes'] = $attrs;
						}
						// optimize tree
						foreach ($result as $tagName => $childData) {
							if(is_array($childData) && count($childData)==1 && $tagName != '@attributes') {
								$result[$tagName] = $childData[0];
							}
						}
					}
					break;
			}
			return $result;
		}        

		/**
		* Parse dom node
		* 
		* @param DOMNode $oDomNode
		* @returns array
		*/
		private function _parseDomDoc($domDoc) {
			$result = $this->_parseDomNode($domDoc);
			return $result;
		}        

		/**
		* On error event
		* 
		* @param int $errorCode
		* @param string $errorMessage
		* @param mixed $errorData
		*/
		public function onError($errorCode, $errorMessage, $errorData = NULL) {
			$error = array(                
				'code' => $errorCode,
				'message' => $errorMessage,
			);

			if (!empty($errorData)) {
				$error['data'] = $errorData;
			}

			$this->errorsList['error'][] = $error;
			return true;
		}

		/**
		* Log message
		* 
		* @param int $logType
		* @param string $logMessage
		* @param mixed $logData 
		*/
		public function log($logType = API_LOG_TYPE_NOTICE, $logMessage, $logData = array()) {
			$errorCode = API_ERROR_CODE_NOTICE;
			switch($logType) {
				case 2:
					$errorCode = API_ERROR_CODE_WARNING;
					break;
				case 3:
					$errorCode = API_ERROR_CODE_ERROR;
					break;                                    
			}
			clsCommon::Log($logMessage, $logType);
			$this->onError($errorCode, $logMessage, $logData);
		}

		/**
		* Read xml from input
		* 
		*/
		private function _readXmlDocFromInput() {

			$xmlDoc = '';
			if (!empty($_GET['xml']) && preg_match('/^[a-z_]+$/', $_GET['xml'])) {
				$xmlFileName = sprintf('xml/%s.xml', $_GET['xml']);
				if (file_exists($xmlFileName)) {
					$xmlDoc = @file_get_contents($xmlFileName);
				} else {
					$this->log(API_LOG_TYPE_ERROR, sprintf('Method: %s, Line: %s => Xml file "%s" not found!', __METHOD__, __LINE__, $xmlFileName));
				}                
			} else {
				$this->log(API_LOG_TYPE_ERROR, sprintf('Method: %s, Line: %s => Param "xml" not set or incorrect!', __METHOD__, __LINE__));
			}

			return $xmlDoc;
		}

		/**
		* Check xml file
		* 
		*/
		private function _checkXmlFromInput() {

			$xmlDoc = '';
			if (!empty($_GET['xml']) && preg_match('/^[a-z_]+$/', $_GET['xml'])) {
                $xmlFileName = sprintf('xml/%s.xml', $_GET['xml']);
				$i = 0;
                if (file_exists($xmlFileName)) {
                    $reader = new XMLReader();
                    $reader->open($xmlFileName);
                    $actions = array();
                    while($reader->read()) {
//                        echo $i++;
                        if($reader->nodeType == XMLReader::ELEMENT && $reader->name == 'actions') {
                            $doc = new DOMDocument('1.0', 'UTF-8');
                            $xml = $doc->importNode($reader->expand(),true);
                            $actions = $this->_parseDomNode($xml);
                            $this->actions = $this->getArrayNode($actions['action']);
                            unset($doc);
                            unset($xml);
                            $reader->next();
//                            echo $xml->action; //or whatever
                        }
                        if($reader->nodeType == XMLReader::ELEMENT && in_array($reader->name, $this->actions)) {
                            $doc = new DOMDocument('1.0', 'UTF-8');
                            $xml = $doc->importNode($reader->expand(),true);
                            $xxx = $this->_parseDomNode($xml);
                            $result = $this->callParser($xxx, array(), $reader->name);
                            unset($doc);
                            unset($xml);
                            $reader->next();
                        }
                    }
                     # Останавливаем профайлер
        $xhprof_data = xhprof_disable();    

        # Сохраняем отчет и генерируем ссылку для его просмотра
        
	
    	define("XHPROF_ROOT", CORE_3RDPARTY_PATH . '/xhprof');
        include_once XHPROF_ROOT . "/xhprof_lib/utils/xhprof_lib.php";
        include_once XHPROF_ROOT . "/xhprof_lib/utils/xhprof_runs.php";
        $xhprof_runs = new XHProfRuns_Default();
        $run_id = $xhprof_runs->save_run($xhprof_data, "xhprof_test");
        echo "Report: http://" . $_SERVER['HTTP_HOST'] . "/3rdparty/xhprof/xhprof_html/index.php?run=$run_id&source=xhprof_test"; # Хост, который Вы настроили ранее на GUI профайлера
        echo "\n";
                    
                    die();
					$xmlDoc = $xmlFileName;
				} else {
					$this->log(API_LOG_TYPE_ERROR, sprintf('Method: %s, Line: %s => Xml file "%s" not found!', __METHOD__, __LINE__, $xmlFileName));
				}                
			} else {
				$this->log(API_LOG_TYPE_ERROR, sprintf('Method: %s, Line: %s => Param "xml" not set or incorrect!', __METHOD__, __LINE__));
			}

			return $xmlDoc;
		}
        
        /**
		* Check xml file
		* 
		*/
		private function _openXml($reader, $xmlFileName) {

			$result = false;
			$reader->open($xmlFileName);
			$reader->setParserProperty(XMLReader::VALIDATE, true);
            die(var_dump($reader->isValid()));
            if (!empty($_GET['xml']) && preg_match('/^[a-z_]+$/', $_GET['xml'])) {
				$xmlFileName = sprintf('xml/%s.xml', $_GET['xml']);
				if (file_exists($xmlFileName)) {
					$xmlDoc = $xmlFileName;
				} else {
					$this->log(API_LOG_TYPE_ERROR, sprintf('Method: %s, Line: %s => Xml file "%s" not found!', __METHOD__, __LINE__, $xmlFileName));
				}                
			} else {
				$this->log(API_LOG_TYPE_ERROR, sprintf('Method: %s, Line: %s => Param "xml" not set or incorrect!', __METHOD__, __LINE__));
			}

			return $xmlDoc;
		}
        
        
		/**
		* Get correct array node
		* 
		* @param mixed $node
		*/
		public function getArrayNode($node) {
			$result = array();
			if (!empty($node)) {                    
				if(isset($node[0]) && !is_string($node)) {
					$result = $node;
				} else {
					$result = array($node);
				}
			}
			return $result;
		}

		/**
		* Parse node
		* 
		* @param array $data
		* @param array $args
		*/
		public function callParser($data, $args = array(), $name = ''){
			$result = array();

			if (empty($data) || !is_array($data)) {
				return array();
			}
            
            if(!empty($name)) {
                $actions = array($name);
                $data = array($name => $data);
            } else {
                $actions = array_keys($data);
            }

			foreach($actions as $action) {
				if ((isset($data[$action])) || !empty($name)) {
					if ((is_array($data[$action]) && count($data[$action]) > 0) || !empty($name)) {

						$actionNameParts = explode('_', $action);
						$actionNamePartsUcfirst = array_map('ucfirst', $actionNameParts);
						$className = sprintf('clsApi%s',  join($actionNamePartsUcfirst));

						if (class_exists($className)) {
							$object = $className::getInstance();
							$object->setApi($this);
							try {
								$tampResult = $object->parseItems($data[$action], $args);
                                var_dump(123);
								
								if (!empty($tampResult) && is_array($tampResult)) {
									if (!isset($result[$action])) {
										$result[$action] = array();
									}
									$result[$action] += $tampResult;
								}
							} catch (\Exception $exception) {
								$this->log(API_LOG_TYPE_ERROR, strip_tags($exception->getMessage()), array(
										'file' => $exception->getFile(), 
										'line' => $exception->getLine(),
										'backtrace' => array('trace' => explode("\n", $exception->getTraceAsString()))
									));
							}
						} else {
							$this->log(API_LOG_TYPE_WARNING, sprintf('Method: %s, Line: %s => Node parser "%s" not found', __METHOD__, __LINE__, $className));
						}
					}
				} else {
					$this->log(API_LOG_TYPE_WARNING, sprintf('Method: %s, Line: %s => Empty data, key: %s', __METHOD__, __LINE__, $action));
				}
			}

			return $result;
		}

		/**
		* Run application
		* 
		*/
		public function runApp() {
			$this->actions = array();
			$this->response = array('data' => array());
			$responseData = &$this->response['data'];

			$parseData = array();            
			$parseStatus = true;

			do {
//				$reader = new XMLReader();
//                $xmlDoc = $this->_readXmlDocFromInput();
                $xmlDoc = $this->_checkXmlFromInput();
				if ($xmlDoc === false) {
					//TODO: Read error
					$this->log(API_LOG_TYPE_ERROR, sprintf('Method: %s, Line: %s => Read xml error', __METHOD__, __LINE__));
					$parseStatus = false;
					break;
				}
                
                $domDoc = $this->_parseXmlDoc($xmlDoc);
//				$domDoc = $this->_openXml($reader, $xmlDoc);
				if ($domDoc === false) {
					//TODO: Xml error
					$this->log(API_LOG_TYPE_ERROR, sprintf('Method: %s, Line: %s => Parse xml error', __METHOD__, __LINE__));
					$parseStatus = false;
					break;
				}

				$parsedData = $this->_parseDomDoc($domDoc);
				if ($parsedData === false) {
					//TODO: Dom document error       
					$this->log(API_LOG_TYPE_ERROR, sprintf('Method: %s, Line: %s => Parse dom error', __METHOD__, __LINE__));
					$parseStatus = false;
					break;
				}

				if (empty($parsedData['request']) || !is_array($parsedData['request'])) {
					//TODO: Request empty error    
					$this->log(API_LOG_TYPE_WARNING, sprintf('Method: %s, Line: %s => Empty request', __METHOD__, __LINE__));
					$parseStatus = false;
					break;
				}

				$request = $parsedData['request'];

				if (!empty($request['actions']['action'])) {
					$this->actions = $this->getArrayNode($request['actions']['action']);
				}                

				$parseData = (empty($request['data']) || !is_array($request['data'])) ? array() : $request['data'];        
				$result = $this->callParser($parseData);
				$responseData = array_merge($responseData, $result);

			} while(0);

			$actions = array_merge($this->actions, array(
					'events'
				));

			if (!empty($actions)) {
				$actions = array_unique($actions);

				$result = array();
				foreach($actions as $action) {

					$actionNameParts = explode('_', $action);
					$actionNamePartsUcfirst = array_map('ucfirst', $actionNameParts);
					$className = sprintf('clsApiAction%s',  join($actionNamePartsUcfirst));

					if (class_exists($className)) {
						$object = $className::getInstance();
						$object->setApi($this);

						try {
							$tempResult = $object->action($parseData);

							foreach($tempResult as $resultItem) {
								if (is_array($resultItem) && count($resultItem) == 3) {
									list($actionGroup, $actionType, $actionData) = $resultItem;
									if (!empty($actionData) && is_array($actionData)) {
										if (!isset($result[$actionGroup][$actionType])) {
											$result[$actionGroup][$actionType] = array();
										}                                    
										$result[$actionGroup][$actionType][] = $actionData;
									}
								}
							}

						} catch (\Exception $exception) {
							$this->log(API_LOG_TYPE_ERROR, strip_tags($exception->getMessage()), array(
									'file' => $exception->getFile(), 
									'line' => $exception->getLine(),
									'backtrace' => array('trace' => explode("\n", $exception->getTraceAsString()))
								));
						}
					} else {
						$this->log(API_LOG_TYPE_WARNING, sprintf('Method: %s, Line: %s => Action handler "%s" not found', __METHOD__, __LINE__, $className));
					}
				}
				$responseData = array_merge($responseData, $result);
				//$aResponseData += ;
			}


			if (!empty($this->errorsList)) {
				$this->response['errors'] = $this->errorsList;
			}

			$this->response['status'] = $parseStatus ? '1' : '0';

			//$this->printXmlResponse();
			return true;
		}

		/**
		* Get actions list
		* 
		* @return array
		*/
		public function getActions() {
			return $this->actions;
		}

		/**
		* Check action
		* 
		* @param string $action
		* @return bool
		*/
		public function checkAction($action) {
			static $keysList = false; 
			if (empty($keysList) && !empty($this->actions) && is_array($this->actions)) {
				$keysList = array_flip($this->actions);
			}
			return isset($keysList[$action]);
		}

		/**
		* Shutdown handler
		* 
		*/
		public function shutdown() {
			$lastError = error_get_last();
			if (!empty($lastError) && $lastError['type'] == 1) {
				$this->onError(API_ERROR_CODE_FATAL, $lastError['message'], array(
						'file' => $lastError['file'], 
						'line' => $lastError['line'],
					));

				if (!empty($this->errorsList)) {
					$this->response['errors'] = $this->errorsList;
				}
				$this->response['status'] = '0';
			}            
			$this->printXmlResponse();            
		}

		/**
		* Print xml response
		* 
		*/
		public function printXmlResponse() {
			$outputData = ob_get_contents();
			ob_end_clean();

			if (!empty($outputData)) {
				$this->response['php_output'] = htmlentities($outputData, NULL, 'utf-8');
			}            

			$endTime = microtime(true);
			$this->response['timestamp'] = time();
			$this->response['exectime'] = round($endTime - $this->startTime, 4);

			header("Content-Type: text/xml; charset=utf-8", true);
			echo $this->arrayToXml($this->response, 'response');
		}

		/**
		* Print array response
		* 
		*/
		public function printArrayResponse() {
			$outputData = ob_get_contents();
			ob_end_clean();

			if (!empty($outputData)) {
				$this->response['php_output'] = htmlentities($outputData, NULL, 'utf-8');
			}            

			$endTime = microtime(true);
			$this->response['timestamp'] = time();
			$this->response['exectime'] = round($endTime - $this->startTime, 4);

			header("Content-Type: text/html; charset=utf-8", true);
			dbg($this->response);
		}
	}
