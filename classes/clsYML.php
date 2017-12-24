<?php

class clsYML {

	private $artists = array();
	private $xml = "";
	private $inc = 0;
	private $counter = 0;
	private $template = '<?xml version="1.0" encoding="utf-8"?>
                         <!DOCTYPE yml_catalog SYSTEM "shops.dtd">
                         <yml_catalog date="{DATE}">
                         <shop>
                         {TABLE}
                         </shop>
                         </yml_catalog>';
	private $xml_set = array();
	private $num_per = 40000;
	private $filename = "content/{SERVER_SIMPLE_NAME}/YML_{NUM}.xml";
	private $server_simple_name = SERVER_NAME;
	private $server_name;
	private $db = "";

	/**
	* domain config array
	* @var array $config - domain config array
	*/
	private $config = array();


	public function clsYML(){
		$this->db = DB::getInstance();
		$this->server_name = 'www.'.$this->server_simple_name;
        
		$this->filename = str_replace("{SERVER_SIMPLE_NAME}", $this->server_simple_name, $this->filename);
		$this->config = clsCommon::getDomainConfig();
	}

	private function _shopContent(){
        
        $content = '<name>' . YML_SHOP_NAME . '</name>
                    <company>' . YML_SHOP_COMPANY . '</company>
                    <url>' . YML_SHOP_URL . '</url>';
        $this->xml_set[] = $content;
	}
    
	private function _currenciesContent(){
        
        $content = '<currency id="RUR" rate="1"/>';
        $this->xml_set[] = $content;
	}

	private function _bodyXML(){
        $this->_shopContent();
        $this->_currenciesContent();
		$this->_categoryContent();
		$this->_productsContent();
	}

	private function getElement($data){
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

	public function StartCreateYML(){
		$this->_bodyXML();
		$this->_createYML();
	}

	private function _categoryContent(){
		$content = '<categories>';
        
        $sql = "
			SELECT id, parent_id, name
			FROM categories
			WHERE categories.status = 1";
		$res = $this->db->GetAll($sql, array());
        if($res) {
            foreach ($res as $result) {
                $id = $result['id'];
                $parentId = empty($result['parent_id']) ? '' : $result['parent_id'];
                $name = $result['name'];
                $content .= '<category id="' . $id . '"'
                         . (empty($parentId) ? '' : (' parentId="' . $parentId . '"')) . '>'
                        . $name . '</category>';
            }            
        }
        
        $content .= '</categories>';
        $this->xml_set[] = $content;

	}
	
	private function _productsContent(){
        $content = '<offers>';
        
		$sql = "
			SELECT p.id, p.name, pp.price, gp.category_id
			FROM products p
            JOIN productprices pp ON pp.product_id = p.id
            JOIN productsgroups pg ON pg.product_id = p.id
            JOIN group_products gp ON gp.id = pg.group_product_id
			WHERE p.status = 1 AND pp.column_number = 1";
		$res = $this->db->GetAll($sql, array());
		foreach ($res as $result) {
            $url = clsCommon::compileDefaultItemHref('Products', array('product_id' => $result['id']));
            $price = $result['price'];
            $currencyId = 'RUR';
            $categoryId = $result['category_id'];
            $name = $result['name'];
            $content .= '<offer>
                    <url>' . $url . '</url>
                    <price>' . $price . '</price>
                    <currencyId>' . $currencyId . '</currencyId>
                    <categoryId>' . $categoryId . '</categoryId>
                    <name>' . $name . '</name>
                    </offer>';
		}
        
        $content .= '</offers>';
        $this->xml_set[] = $content;
	}

	private function _createYML(){
		$check = 0;
		$inc = 1;
		$flname = "";
		$output_files = array();
		foreach ($this->xml_set as $value){
			$check ++;
			$this->xml .= $value;
			if($check >= $this->num_per){
				$content = str_replace("{DATE}",date('Y-m-d H:i'),$this->template);
				$content = str_replace("{TABLE}",$this->xml,$content);
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
            $content = str_replace("{DATE}",date('Y-m-d H:i'),$this->template);
			$content = str_replace("{TABLE}",$this->xml,$content);
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
//				unlink($output_name);
			}
		}
	}

	private function isBadEntity($artist){
	    return preg_match("/^([0-9a-zA-Z_.,-~ '\"\(\)\[\]@#\$%^&*!+?]+)$/",$artist);
	}

}