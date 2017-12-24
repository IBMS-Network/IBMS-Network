<?php
define('R_OPCODE', chr(0));
define ('T_PREF', 'T_PX_');
define ('T_DELIM', '::');
define ('INNER_JOINS', 'IJ');
define ('LEFT_JOINS', 'LJ');
define ('JOINS', 'NJ');
define ('INNER_JOIN', R_OPCODE . INNER_JOINS . R_OPCODE);
define ('LEFT_JOIN', R_OPCODE . LEFT_JOINS . R_OPCODE);
define ('JOIN', R_OPCODE . JOINS . R_OPCODE);

$rop_sign = array(  'EQ' => R_OPCODE . '= ' . R_OPCODE, 
					'GT' => R_OPCODE . '> ' . R_OPCODE,
					'LT' => R_OPCODE . '< ' . R_OPCODE,
					'GE' => R_OPCODE . '>=' . R_OPCODE,
					'LE' => R_OPCODE . '<=' . R_OPCODE,
					'NE' => R_OPCODE . '!=' . R_OPCODE,
					'LK' => R_OPCODE . ' LIKE ' . R_OPCODE,
);

foreach ($rop_sign as $code => $value) {
	define($code, $value);
}

class Record {

	var $__structure__      = array();
	var $__primary_key__    = array();
	var $__foreign_keys__   = array();
	var $__new_item__       = array();
	var $__autoincrements__ = array();
	var $__tablename__      = '__none_table__';
	var $statment_codes     = array();    
	var $native_key         = false;    // is key one integer value only
	var $dsp                = null;

	function __construct() {
	}
		
	function Init() {
		$class = get_class($this);
		$classes = array($class);
		while($class = get_parent_class($class)) { 
			$classes[] = $class; 
		}
		
		$this->__tablename__ = strtolower(get_class($this));

		if (PRODUCTION) {
			if (is_file(TABLE_DIR . $this->__tablename__ . '.def')) {
				$this->__structure__ = unserialize(file_get_contents(TABLE_DIR . $this->__tablename__ . '.def'));
			} else {
				$parent_tablename = get_parent_class($this);
				
				if (is_file(TABLE_DIR . $parent_tablename . '.def')) {
					$this->__structure__ = unserialize(file_get_contents(TABLE_DIR . $parent_tablename . '.def'));
				}   
			} // if
		} else {
			$this->__structure__ = $this->dsp->mysql2def->ParseTableFromSQL($this->__tablename__);
		}
		
		$this->_parseStruct();
	} // Constructor

	function __destruct() {
	} // Destructor

	function SetDSP(&$dsp) {
		$this->dsp = $dsp;
	}

	function _parseStruct(){
		foreach ($this->__structure__ as $field => $desc) {
			if (!empty($desc['primary'])) {
				$this->__primary_key__[$field] = $field;
			} //if
			
			/**
			 * @todo
			 *	сделать проверку на ключ - масив
			 *	 && !array($desc[$key])
			 */
			foreach (array('index', 'unique', 'key') as $key) {            
				if (!empty($desc[$key]) && !array($desc[$key])) {
					if (isset($this->__foreign_keys__[$desc[$key]])) {
						$this->__foreign_keys__[$desc[$key]][] = $field;
					} else {
						$this->__foreign_keys__[$desc[$key]] = array($field);
					}
				} // if
			} // foreach
			
			if (isset($desc['autoincrement'])) {
				$this->__new_item__[$field] = '';
				$this->__autoincrements__[$field] = $field; 
			} elseif (isset($desc['default'])) {
				$this->__new_item__[$field] = $desc['default'];
			} else {
				$this->__new_item__[$field] = $this->_empty_value_by_type($desc['type']);
			}
		} // foreach
		
		if (sizeof($this->__primary_key__) == 1) {
			$key = reset($this->__primary_key__);
			if (isset($key['type']) && $key['type'] == 'integer') {
				$this->native_key = true;
			}
		}
	} // _parseStruct()


	function _empty_value_by_type($filed_type) {
		$result = '';
		switch ($filed_type) {
			case 'integer' : 
				$result = 0;
				break;
			case 'string' : 
				$result = '';
				break;
			case 'text' : 
				$result = '';
				break;
			case 'date' : 
				$result = '0000-00-00';
				break;
			case 'datetime' : 
				$result = '0000-00-00 00:00:00';
				break;
		} // switch
		
		return $result; 
	} // _empty_value_by_type()

	function NewItem() {
		return $this->__new_item__;
	} // NewItem()

	function GetItem($key) {
		$key = $this->_explode_key($key);
		if ($key === false) return false;
		
		if (is_callable(array($this, 'beforeGet'))) {
			$key = $this->beforeGet($key);
		}
		$where = $this->_prepare_where_statment($key);
		$item = $this->dsp->db->SelectRow("SELECT * FROM `{$this->__tablename__}` WHERE " . $where);
		if (is_callable(array($this, 'afterGet'))) {
			$item = $this->afterGet($item);
		}
		return $item;
	} // GetItem()

	function AddItem($item, $try = false) {
		if ($try) {
			$try_word = 'IGNORE';
		} else {
			$try_word = '';
		}
		
		$db = &$this->dsp->db;
		$empty_item = $this->NewItem();
		$item = array_template($item, $empty_item, true);
		if (is_callable(array($this, 'beforeAdd'))) {
			$item = $this->beforeAdd($item);
		}
		if (isset($this->__structure__['createdate'])) {
			$item['createdate'] = date('Y-m-d H:i:s');
		}
		
		$item = array_diff_key($item, $this->__autoincrements__);
		$p = $this->_prepare_params_statment($item);
		$db->Execute("INSERT {$try_word} INTO `{$this->__tablename__}` SET " . $p);
		if ($try && !$db->RowsAffected()) { // No rows was added - duplicated
			$item = $this->GetItem($item);
			return $item;
		}
		$id = $db->LastInsertId();
		$key = array_template($item, $this->__primary_key__);
		if (sizeof($this->__autoincrements__) == 1) {
			$key[reset($this->__autoincrements__)] = $id;
		} // if
		if (!empty($key)) {
			$item = $this->GetItem($key);
		}
		
		if (is_callable(array($this, 'afterAdd'))) {
			$item = $this->afterAdd($item);
		}    
		return $item;
	} // AddItem();

	function DelItem($key) {
		$key = $this->_explode_key($key);
		if ($key === false) return false;
		if (is_callable(array($this, 'beforeDel')) || is_callable(array($this, 'afterDel'))) {
			$item = $this->GetItem($key);
		}
		if (is_callable(array($this, 'beforeDel'))) {
			$this->beforeDel($item);
		}
		$where = $this->_prepare_where_statment($key);
		$this->dsp->db->Execute("DELETE FROM `{$this->__tablename__}` WHERE " . $where);
		if (is_callable(array($this, 'afterDel'))) {
			$this->afterDel($item);
		}
		return true;
	} // DelItem()

	function EditItem($key, $params = false, $skip_updatedate = false) {
		if ($params === false) {
			$params = $key;
			$key = array_template($params, $this->__primary_key__);
		}
		$key = $this->_explode_key($key);
		if ($key === false) return false;
		$params = array_template($params, $this->__structure__);
		$db = &$this->dsp->db;
		if (is_callable(array($this, 'beforeEdit'))) {
			$params = $this->beforeEdit($key, $params);
		}
		if (!$skip_updatedate && isset($this->__structure__['updatedate'])) {
			$params['updatedate'] = date('Y-m-d H:i:s');
		}
		$p = $this->_prepare_params_statment($params);
		$where = $this->_prepare_where_statment($key);
		$db->Execute("UPDATE `{$this->__tablename__}` SET " . $p . " WHERE " . $where);
		$item = $this->GetItem($key);
		if (is_callable(array($this, 'afterEdit'))) {
			$item = $this->afterEdit($item);
		}
		return $item;
	} // EditItem()

	function GetAll() {
		$items = $this->dsp->db->Select("SELECT * FROM `{$this->__tablename__}`");
		return $items;
	} // GetAll()

	function ReindexByKey($data) {
		$result = array();
		foreach ($data as $value) {
			$key = $this->MakeKey($value);
			$value = array_diff_key($value, $this->__primary_key__);
			$result[$key] = $value;
		}
		return $result;
	} // ReindexByKey()

	function GetItemsCount() {
		return $this->dsp->db->SelectValue('SELECT COUNT(*) FROM `{$this->__tablename__}`');
	} // GetItemsCount()

	function GetFieldsByKey($key, $fields) {
		$key = $this->_explode_key($key);
		if ($key === false) return false;
		$key = array_template($key, $this->__primary_key__);
		$result = $this->GetFieldsByCause($key, $fields);
		return reset($result);
	} // GetFieldsByKey
	
	function GetByKey($key) {
		return $this->GetFieldsByKey($key, array_keys($this->__structure__));
	} // GetByKey()
	
	function GetFieldByKey($key, $field, $page = 0, $pagesize = 10) {
		$result = $this->GetFieldsByKey($key, array($field));
		return $result[$field];
	} // GetFieldByKey()

	function GetFieldsByCause($cause, $fields, $orders = array(), $page = 0, $pagesize = 10) {
		$q_joins = array();
		foreach ($cause as $field => $value) {
			if ($field[0] == R_OPCODE) {
				list($t, $opcode, $field) = explode(R_OPCODE, $field, 3);
				$q_joins[$opcode][$field] = $value;
			}
		}
		if (empty($q_joins)) {
			$prefix = '';
			$full_prefix = '';
		} else {
			$prefix = T_PREF . "{$this->__tablename__}";
			$full_prefix = $prefix . '.';
		}
		$field_list = $this->_prepare_fields_list($fields, $prefix);
		$cause = array_template($cause, $this->__structure__);
		$where = $this->_prepare_where_statment($cause, $prefix);
		
		$_sql = '';
		
		if (!empty($prefix)) $_sql .= ' ' . $prefix;
		
		$_joins = array();		
		if (!empty($q_joins[INNER_JOINS])) {
			$_joins[INNER_JOINS] = $q_joins[INNER_JOINS];
		}
		if (!empty($q_joins[LEFT_JOINS])) {
			$_joins[LEFT_JOINS] = $q_joins[LEFT_JOINS];
		}
		if (!empty($q_joins[JOINS])) {
			$_joins[JOINS] = $q_joins[JOINS];
		}        
		
		$_w_check = '';
		if (!empty($_joins)) {
			foreach ($_joins as $join_type => $joins_list) {
				foreach ($joins_list as $table => $t_cause) {
					$sub_prefix = T_PREF . $table;
					
					$sub_sql = ' ';

					if ($join_type == INNER_JOINS) {
						$sub_sql = ' INNER ';
					} elseif ($join_type == LEFT_JOINS) {
						$sub_sql = ' LEFT ';
					} else {
						
					}
					
					$sub_sql .= "JOIN `{$table}` {$sub_prefix} ON (";
					
					if (!empty($t_cause['fields'])) {
						$_field_list = $this->_prepare_fields_list($t_cause['fields'], $sub_prefix);
						if (!empty($_field_list)) {
							if (empty($field_list)) {
								$field_list = $_field_list;
							} else {
								$field_list = sprintf('%s, %s', $field_list, $_field_list);
							}
						}
					}
					
					if (!empty($t_cause['links'])) {
						$sub_sql .= $this->_prepare_link_statment($t_cause['links'], $prefix, $sub_prefix);
					} // (!empty($t_cause['links'])) {
					
					$sub_sql .= ' ';
					if (!empty($t_cause['where'])) {
						$sub_sql .= ' AND ' . $this->_prepare_where_statment($t_cause['where'], $sub_prefix);
					} // (!empty($t_cause['where'])) {
					
					$sub_sql .= ' ';
					if (!empty($t_cause['root_where'])) {
						$sub_sql .= ' AND ' . $this->_prepare_where_statment($t_cause['root_where'], $prefix);
					} // (!empty($t_cause['where'])) {
					
					if (!empty($t_cause['check'])) {
						$_w_check .= 'NOT `' . $sub_prefix . '`.`' .($t_cause['check']). '` IS NULL OR ';
					}
					
					$_sql .= $sub_sql . ')';
				} // foreach ($i_joins)
			}
		} // if
		
		$sql = "SELECT {$field_list} FROM `{$this->__tablename__}` {$_sql}";
		
		if (!empty($where)) $sql .= " WHERE " . $where;
		
		if (!empty($_w_check)) {
			 $sql .= (empty($where) ? '' : ' AND ') . ' (' . $_w_check . ' 0) ';
		}
		
		if (!empty($orders)) {
			$order = array();
			foreach ($orders as $idx => $value) {
				if (is_integer($idx)) {
					$order[$value] = 'ASC';
				} else {
					if (in_array(trim(strtoupper($value)), array('ASC', 'DESC'))) {
						$order[$idx] = trim(strtoupper($value));
					} else {
						$order[$idx] = 'ASC';
					}
				} // if
			} // foreach 
			
//            $order = array_template($order, $this->__structure__); // not valid for more than one property

			$orderby = array();
			foreach ($order as $field => $direct) {
				$orderby[] = $full_prefix . '`' . $field . '` ' . $direct;
			} // foreach 
			$orderby = join(', ', $orderby);
			$sql .= ' ORDER BY ' . $orderby;
		}
		if ($page > 0) {
			$sql .= ' LIMIT ' . ($page - 1) * $pagesize . ', ' . $pagesize;
		}
		if(DEBUG) {
		  preshow_message($sql, ' FROM GetFieldsByCause');
		}

		//dbg($sql);
		
		$result = $this->dsp->db->Select($sql);
		return $result;
	} // GetFieldsByKey

	function GetFieldByCause($cause, $field, $orders = array(), $page = 0, $pagesize = 10) {
		$fields = $this->GetFieldsByCause($cause, array($field), $orders, $page, $pagesize);
		$result = array();
		foreach ($fields as $field) {
			$result[] = reset($field);
		}
		return $result;
	} // GetFieldByKey
	
	function GetByCause($cause, $orders = array(), $page = 0, $pagesize = 10) {
		return $this->GetFieldsByCause($cause, array_keys($this->__structure__), $orders, $page, $pagesize);
	} // GetByCause()
	 
	function CountByCause($cause) {
		$q_joins = array();
		foreach ($cause as $field => $value) {
			if ($field[0] == R_OPCODE) {
				list($t, $opcode, $field) = explode(R_OPCODE, $field, 3);
				$q_joins[$opcode][$field] = $value;
			}
		}
		if (empty($q_joins)) {
			$prefix = '';
			$full_prefix = '';
		} else {
			$prefix = T_PREF . "{$this->__tablename__}";
			$full_prefix = $prefix . '.';
		}
		$cause = array_template($cause, $this->__structure__);
		$where = $this->_prepare_where_statment($cause, $prefix);
		$sql = "SELECT COUNT(*) FROM `{$this->__tablename__}`";
		if (!empty($prefix)) $sql .= ' ' . $prefix;

		$_joins = array();        
		if (!empty($q_joins[INNER_JOINS])) {
			$_joins[INNER_JOINS] = $q_joins[INNER_JOINS];
		}
		if (!empty($q_joins[LEFT_JOINS])) {
			$_joins[LEFT_JOINS] = $q_joins[LEFT_JOINS];
		}
		if (!empty($q_joins[JOINS])) {
			$_joins[JOINS] = $q_joins[JOINS];
		}     
				
		$_w_check = '';
		if (!empty($_joins)) {
			foreach ($_joins as $join_type => $joins_list) {
				foreach ($joins_list as $table => $t_cause) {
					$sub_prefix = T_PREF . $table;
					
					$sub_sql = ' ';

					if ($join_type == INNER_JOINS) {
						$sub_sql = ' INNER ';
					} elseif ($join_type == LEFT_JOINS) {
						$sub_sql = ' LEFT ';
					} else {
						
					}
					
					$sub_sql .= " JOIN `{$table}` {$sub_prefix} ON (";
					if (!empty($t_cause['links'])) {
						$sub_sql .= $this->_prepare_link_statment($t_cause['links'], $prefix, $sub_prefix);
					} // (!empty($t_cause['links'])) {
						
					$sub_sql .= ' ';
					if (!empty($t_cause['where'])) {
						$sub_sql .= ' AND ' . $this->_prepare_where_statment($t_cause['where'], $sub_prefix);
					} // (!empty($t_cause['where'])) {
						
					$sub_sql .= ' ';
					if (!empty($t_cause['root_where'])) {
						$sub_sql .= ' AND ' . $this->_prepare_where_statment($t_cause['root_where'], $prefix);
					} // (!empty($t_cause['where'])) {
					
					if (!empty($t_cause['check'])) {
						$_w_check .= 'NOT `' . $sub_prefix . '`.`' .($t_cause['check']). '` IS NULL OR ';
					}
										
					$sql .= $sub_sql . ')';
				} // foreach ($i_joins)
			}
		} // if
		if (!empty($where)) $sql .= " WHERE " . $where;
		if (!empty($_w_check)) {
			 $sql .= (empty($where) ? '' : ' AND ') . ' (' . $_w_check . ' 0) ';
		}		
		$result = $this->dsp->db->SelectValue($sql);
		return $result;
	} // CountByCause()

	function CountByCausePeriod($cause, $timefrom, $timeto) {
		$cause = array_template($cause, $this->__structure__);
		$where = $this->_prepare_where_statment($cause);
		
		$datefrom = date('Y-m-d 00:00:00', $timefrom);
		$dateto   = date('Y-m-d 23:59:59', $timeto);
		
		$sql = "SELECT COUNT(*) FROM `{$this->__tablename__}` WHERE " . $where . " AND `createdate` BETWEEN ? AND ?";
		
		$result = $this->dsp->db->SelectValue($sql, $datefrom, $dateto);
		
		return $result;
	} // CountByCausePeriod()


	function CountPageByCause($cause, $pagesize) {
		return ceil($this->CountByCause($cause) / $pagesize);
	} // CountPageByCause()


	function GetPositionByCause($cause, $order_less, $order_grate = array()) {
		$cause = array_template($cause, $this->__structure__);
		$where = $this->_prepare_where_statment($cause);
		
		$sql = "SELECT COUNT(*) FROM `{$this->__tablename__}` WHERE " . $where;
		
		$order = '';
		if (!empty($order_grate)) {
			foreach ($order_grate as $field => $value) {
				$order .= ' AND `' . $field . '` > ' . $this->_prepare_field_value($value, $field);
			} // foreach 
		}
					
		if (!empty($order_less)) {
			foreach ($order_less as $field => $value) {
				$order .= ' AND `' . $field . '` < ' . $this->_prepare_field_value($value, $field);
			} // foreach 
		}
					
		$sql .= $order;
		
		$result = $this->dsp->db->SelectValue($sql);
		
		return $result;
	} // GetFieldsByKey


	function GetKeyPage($cause, $key, $pagesize, $order = array()) {
		$key = $this->_explode_key($key);
		if ($key === false) return false;
		
		$cause = array_template($cause, $this->__structure__);
		$where = $this->_prepare_where_statment($cause);
		
		$keys = $this->GetFieldsByCause($cause, $this->__primary_key__, $order);

		$index = 0;
		foreach ($keys as $idx => $record) {
			if (array_diff_assoc($record, $key) == array()) {
				$index = $idx;
			}
		}
		$page = ceil(($index + 1) / $pagesize);
		return $page;
	}
	

	function GetPageByCause($cause, $pagesize, $order_less, $order_grate = array()) {
		$position = $this->GetPositionByCause($cause, $order_less, $order_grate);
		$page = ceil($position / $pagesize) + 1;
		return $page;
	}
	
	
	function UpdateByCause($cause, $fields) {
		$cause = array_template($cause, $this->__structure__);
		$where = $this->_prepare_where_statment($cause);
		$fields = array_template($fields, $this->__structure__);
		$p = $this->_prepare_params_statment($fields);
		$sql = "UPDATE `{$this->__tablename__}` SET " . $p . " WHERE " . $where;
		$result = $this->dsp->db->SimpleExecute($sql);
		return $result;
	} // UpdateByCause()
	
	
	function UpdateByKey($key, $fields, $internal = true) {
		$key = $this->_explode_key($key);
		if ($key === false) return false;
		
		if ($internal) {
			return $this->UpdateByCause($key, $fields);
		} else {
			$item = $this->GetItem($key);
			foreach ($fields as $field => $value) {
				$item[$field] = $value;
			}
			$item = $this->EditItem($key, $item);
			return $item;
		} // if
	} // UpdateByKey()
	
	
	function DeleteByCause($cause) {
		$cause = array_template($cause, $this->__structure__);
		$where = $this->_prepare_where_statment($cause);
		$this->dsp->db->Execute("DELETE FROM `{$this->__tablename__}` WHERE " . $where);
	} // DeleteByCause()
	
	function MakeKey($item) {
		$key = array_template($item, $this->__primary_key__);
		$key = array_values($key);
		if (count($key) == 1) {
			$key = reset($key);
		} else {
			$key = join('|', $key);
		}
		return $key;
	} // MakeKey()

	function MakeHttpKey($item) {
		$key = array_template($item, $this->__primary_key__);
		$key = array_values($key);
		if ($this->native_key) {
			$key = reset($key);
		} else {
			$key = base64_encode(join('|', $key));
		}
		return $key;
	} // MakeHttpKey()

	function ExplodeKey($key) {
		return array_combine(array_keys($this->__primary_key__), explode('|', $key));
	} // ExplodeKey()

	function ExplodeHttpKey($key) {
		if (!$this->native_key) {
			$key = base64_decode($key);
		}
		return $this->ExplodeKey($key);
	} // ExplodeHttpKey()
	
	function QuoteRow($row) {
		foreach ($row as $field => $value) {
			if ($this->_need_quote($this->__structure__[$field]['type'])) {
				$row[$field] = '<![CDATA[' . $value . ']]>';
			} // if
		} // foreach
		return $row;
	} // QuoteRow()
	
	function QuoteList($list) {
		foreach ($list as $idx => $row) {
			$list[$idx] = $this->QuoteRow($row);
		} // foreach
		return $list;
	} // QuoteRow()
	
	function _prepare_fields_list($fields, $prefix = '') {
		$prefix = trim(trim($prefix, ' '), '.');
		if (!empty($prefix)) $prefix = $prefix . '.';
		
		$field_list = array();
		foreach ($fields as $field) {
			$field_list[] = $prefix . '`' . $field . '`';
		}
		$field_list = join(', ', $field_list);
		return $field_list;
	} // _prepare_fields_list()
	
	function _prepare_where_statment($keys, $prefix = '') {
		$prefix = trim(trim($prefix, ' '), '.');
		if (!empty($prefix)) $prefix = $prefix . '.';
		
		$result = array();
		foreach ($keys as $field => $value) {
			if (!is_array($value)) {
				$opcode = '=';
				if (($value[0] == R_OPCODE) && ($value[3] == R_OPCODE)) {
					list($t, $opcode, $value) = explode(R_OPCODE, $value, 3);
				}
				if(stripos($opcode, 'like')!=false) {
				  $result[] = $prefix . '`' . $field . '` LIKE ' . $this->dsp->db->QuoteValue($value, true);
				} else {
				  $result[] = $prefix . '`' . $field . '` ' . $opcode . $this->_prepare_field_value($value, $field);
				}
			} else {
				//TODO:empty string, null 
				if (empty($value)) {
					return '1=0'; // Empty anyway
				}
				$vals = array();
				foreach ($value as $unic) {
					$vals[] = $this->_prepare_field_value($unic, $field);
				} // foreach
				$vals = join(', ', $vals);
				$result[] = $prefix . '`' . $field . '` IN (' . $vals . ')'; 
			} 
		} // foreach 
		return join(' AND ', $result);
	} // 
	
	function _prepare_link_statment($keys, $main_prefix = '', $slave_prefix = '') {
		$main_prefix = trim(trim($main_prefix, ' '), '.');
		if (!empty($main_prefix)) $main_prefix = $main_prefix . '.';
		
		$slave_prefix = trim(trim($slave_prefix, ' '), '.');
		if (!empty($slave_prefix)) $slave_prefix = $slave_prefix . '.';

		$result = array();
		foreach ($keys as $field => $value) {
			$opcode = '=';
			if (($value[0] == R_OPCODE) && ($value[3] == R_OPCODE)) {
				list($t, $opcode, $value) = explode(R_OPCODE, $value, 3);
			}
			$result[] = $main_prefix . '`' . $field . '` ' . $opcode . $slave_prefix . '`' . $value . '` ';
		} // foreach 
		return join(' AND ', $result);
	} // _prepare_link_statment()
	
	function _prepare_params_statment($params) {
		
		$result = array();
		foreach ($params as $field => $value) {
			if (!is_array($value)) {
				$result[] = '`' . $field . '` = ' . $this->_prepare_field_value($value, $field);
			} else {
				$vals = array();
				foreach ($value as $unic) {
					$vals[] = $this->_prepare_field_value($unic, $field);
				} // foreach
				$vals = join(', ', $vals);
				$result[] = '`' . $field . '` IN (' . $vals . ')'; 
			} 
		} // foreach 
		return join(', ', $result);
	} // _prepare_params_statment()
		
	function _prepare_field_value($value, $field = '') {
		if (strpos($value, T_DELIM) !== false) {
			list($table, $field) = explode(T_DELIM, $value, 2);
			return T_PREF . $table . '`' . $field . '`';
		}      
		if ($field == '' || $this->_need_quote($this->__structure__[$field]['type'])) {
			return $this->dsp->db->QuoteValue($value);
		} else {
			return $value;
		} // if
	} // _prepare_field_value()
	
	function _need_quote($field_type) {
		return (in_array($field_type, array('string', 'text', 'date', 'datetime', 'char')));
	} // need_quote())
  
	function _explode_key($key) {
		if (!is_array($key) && (sizeof($this->__primary_key__) == 1)) {
			$primary = reset(array_keys($this->__primary_key__));
			$key = array($primary => $key);
		} // if
		$key = array_template($key, $this->__primary_key__);
		if (sizeof($key) != sizeof($this->__primary_key__)) {
			return false;
		}
		return $key; 
	} // _explode_key()
	
	function _internal_row_convert($row) {
		foreach ($row as $field => $value) {
			$row[$field] = $this->_internal_type_convert($value, $this->__structure__[$field]);
		}
		return $row;
	} // _internal_row_convert()
	
	function _internal_type_convert($value, $type) {
		switch ($type) {
			case 'integer' : 
				$value = (integer)$value;
				break;
			case 'float' : 
				$value = (float)$value;
				break;
		} // swtich
		return $value;
	} // _internal_type_convert()
  
	function __get($name) {
		global $dsp;
		if ($name == "dsp") {
			return $dsp;
		} else {
			return $dsp->$name;
		} 
	}  

	function __set($name, $value) {
	}  

} // class Record
