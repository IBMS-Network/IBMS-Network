<?php

class clsSiteMap {

	private $artists = array();
	private $xml = "";
	private $inc = 0;
	private $counter = 0;
	private $template = '<?xml version="1.0" encoding="UTF-8"?>
		<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
		{TABLE}
		</urlset>';
	private $xml_set = array();
	private $num_per = 40000;
	private $filename = "content/{SERVER_SIMPLE_NAME}/sitemap{NUM}.xml";
	private $server_simple_name = SERVER_NAME;
	private $server_name;
	private $db = "";

	/**
	* domain config array
	* @var array $config - domain config array
	*/
	private $config = array();


	public function clsSiteMap(){
		$this->db = DB::getInstance();
		$this->server_name = 'www.'.$this->server_simple_name;
		$this->filename = str_replace("{SERVER_SIMPLE_NAME}", $this->server_simple_name, $this->filename);
		$this->config = clsCommon::getDomainConfig();
	}

	private function _headerXML(){
		//main
		$this->xml_set[] = $this->getElement(SERVER_URL_NAME."/",1,'daily');
		//news
		$this->xml_set[] = $this->getElement(
			clsCommon::compileDefaultItemHref('News', array()),
			0.9,'daily');
	}

	private function _bodyXML(){
		$this->_newsContent();
		$this->_categoryContent();
		$this->_productsContent();
	}

	private function getElement($url,$priority,$changefreq = 'monthly'){
		$this->counter++;
		$last_mod = date("Y-m-d")."T".date("H:i:sP");
		return '<url>
				  <loc>'.$url.'</loc>
				  <lastmod>'.$last_mod.'</lastmod>
				  <changefreq>'.$changefreq.'</changefreq>
				  <priority>'.$priority.'</priority>
				</url>';
	}

	public function Show(){
		echo "<pre>";print_r($this->xml);echo "</pre>";
	}

	public function StartCreateSiteMap(){
		$this->_headerXML();
		$this->_bodyXML();
		$this->_createSiteMap();
	}

	private function _newsContent(){
		$sql = "
			SELECT id
			FROM news
			WHERE news.status = 1";
		$res = $this->db->GetAll($sql, array());
		foreach ($res as $result) {
//			if($this->isBadEntity($result['title'])){
				$link = clsCommon::compileDefaultItemHref('Article', array('news_id' => $result['id']));
				$this->xml_set[] = $this->getElement($link, 0.8);
//			}
		}
	}
	
	private function _categoryContent(){
		$sql = "
			SELECT id
			FROM categories
			WHERE categories.status = 1";
		$res = $this->db->GetAll($sql, array());
		foreach ($res as $result) {
//			if($this->isBadEntity($result['title'])){
				$link = clsCommon::compileDefaultItemHref('Category', array('cat_id' => $result['id']));
				$this->xml_set[] = $this->getElement($link, 0.8);
//			}
		}
	}
	
	private function _productsContent(){
		$sql = "
			SELECT id
			FROM products
			WHERE products.status = 1";
		$res = $this->db->GetAll($sql, array());
		foreach ($res as $result) {
				$link = clsCommon::compileDefaultItemHref('Products', array('product_id' => $result['id']));
				$this->xml_set[] = $this->getElement($link, 0.8);
		}
	}

	private function _createSiteMap(){
		$check = 0;
		$inc = 1;
		$flname = "";
		$output_files = array();
		foreach ($this->xml_set as $value){
			$check ++;
			$this->xml .= $value;
			if($check >= $this->num_per){
				$content = str_replace("{TABLE}",$this->xml,$this->template);
				$this->xml = "";
				$flname = $inc;
				$file_name = str_replace("{NUM}",$flname,$this->filename);
				file_put_contents(SERVER_ROOT.$file_name,$content);
				$output_files[] = SERVER_ROOT.$file_name;
				$inc ++;
				$check = $check-$this->num_per;
			}
		}
		if(!empty($this->xml)){
			$content = str_replace("{TABLE}",$this->xml,$this->template);
			$this->xml = "";
			$flname = $inc;
			$file_name = str_replace("{NUM}",$flname,$this->filename);
			file_put_contents(SERVER_ROOT.$file_name,$content);
			$output_files[] = SERVER_ROOT.$file_name;
			$inc ++;
		}
		
		foreach ($output_files as $output_name){
			if (file_exists($output_name)){
				if (file_exists($output_name.".gz"))
					unlink($output_name.".gz");
				$string_command = "gzip -c ".$output_name." > ".$output_name.".gz";
				exec($string_command);
				unlink($output_name);
			}
		}
	}

	private function isBadEntity($artist){
	    return preg_match("/^([0-9a-zA-Z_.,-~ '\"\(\)\[\]@#\$%^&*!+?]+)$/",$artist);
	}

}