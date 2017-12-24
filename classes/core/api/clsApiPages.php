<?php
class clsApiPages {
	/**
	* Self instance 
	* 
	* @var clsApiPages
	*/
	static private $instance = NULL;

	/**
	* Api core
	* 
	* @var clsApiCore
	*/
	protected $api;
    
    /**
	* Page metas
	* 
	* @var array
	*/
	protected static $metas = array();


	/**
	* Constructor
	* 
	*/
	public function __construct() {
	}

	/**
	* Set Api
	* 
	* @param clsApiCore $api
	*/
	public function setApi($api) {
		$this->api = $api;
	}

	/**
	* Get instance
	* 
	* @var clsApiPages
	*/
	public static function getInstance() {
		if (self::$instance == NULL) {
			self::$instance = new clsApiPages();
		}
		return self::$instance;
	} 

	public function parseItems($items, $args) {
		$result = array();
		
		$pages = clsStatic::getInstance();
		
        if(isset($items['page'])) {
			$nodeKey = 'page';
			$nodeData = $this->api->getArrayNode($items[$nodeKey]);
			foreach ($nodeData as $nodeItem) {
				if (empty($nodeItem['id'])) {
					continue;
				}
				$outerId = (int)$nodeItem['id'];
				$resultNode = &$result[$nodeKey][];
				$resultNode['id'] = $outerId;
				
				$title = $nodeItem['title'];
				$description = isset($nodeItem['description']) ? $nodeItem['description'] : '';
				$content = $nodeItem['content'];
				$createDate = date('Y-m-d H:i:s', strtotime($nodeItem['create_date']));
				$status = $nodeItem['status'];
				
				$itemId = 0;
				do {
					$itemId = $pages->getPageIdByOuterId($outerId);

					if (!empty($itemId)) {
						break;
					}

					$itemId = $pages->createPageRaw($outerId, $title, $description,
						$content, $createDate, $status);

					if (!empty($itemId)) {
						break;
					}

					$this->api->log(API_LOG_TYPE_ERROR, sprintf('Method: %s, Line: %s => Pages: outer_id = %s, don\'t added!', __METHOD__, __LINE__, $outerId));
				} while(0);
                
                if (!empty($itemId)) {
                    $args = array('page_type_id' => clsApiParser::getPageTypeIdByName('static'),
                                  'item_id' => (int)$itemId);
                    $metas = clsApiMetas::getInstance();
                    $metas->setApi($this->api);
                    $metas->parseItems($this::$metas, $args);
                    $this::$metas = array();
                }
				
				$resultNode += $this->api->callParser($nodeItem, array(
					'item_id' => $itemId,
					'page_type_id' => clsApiParser::getPageTypeIdByName('page')
				));

				$resultNode['status'] = !empty($itemId) ? '1' : '0';
			}
        } else {
            $this::$metas = $items['metas'];
        }
//		if ($this->api->checkAction('pages')) {
//		}

		return $result;
	}
}