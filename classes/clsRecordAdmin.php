<?php

    /**
    * Version 1.01
    */

    class Record_admin {
        /*
        * this property contains object of related entity from Record class
        */
        var $class = null;

        /*
        * this property contains related class name from Record class
        */
        var $class_name = '';

        var $pattern = null;

        /*
        * array of fields for this entity by actions from  related *.ptrn file
        */
        var $record = array();

        /*
        * inner array of all 'lookup' objects from .ptrn file
        */
        var $list_calc_fields = array();

        /*
        * array of key=>value with params thats can be overwritting from action
        */
        var $defaults = array();

        /*
        * inner array of all 'checkbox' objects from .ptrn file
        */
        var $list_checkbox_fields = array();

        /*
        * inner array of all 'images' objects from .ptrn file
        */
        var $list_images_fields = array();

        var $descriptions = array();

        /*
        * curent entity contains tag fields
        */
        var $is_tag = false;

        function __construct() {
        }


        /*
        * Init Admin Records class
        *
        * Available types :
        *   checkbox(integer, tinyint)
        *   lookup(integer)
        *   password(string,32)
        *   image(integer)
        */
        function Init($params = array()) {
            $class_name = explode('_', strtolower(get_class($this)));
            array_pop($class_name);
            $this->class_name = join('_', $class_name);

            $ini = $this->class_name;
            if (!empty($params['ini'])) {
                $ini = $params['ini'];
            }

            $this->pattern = simplexml_load_file(CLASS_DIR . '/admin_tables/' . $ini . '.ptrn');

            if (!empty($this->pattern->table)) {
                $this->class_name = (string)$this->pattern->table;
            }

            $class_params = array();
            $this->dsp->Init($this->class_name, $class_params);

            if (!empty($this->dsp->installed[$this->class_name])) {
                $this->class = &$this->dsp->installed[$this->class_name];
            }

            foreach ($this->pattern->actions->children() as $action => $def) {
                if ($action == 'defult') continue;

                if (!empty($def->description)) {
                    $this->descriptions[$action] = $def->description;
                }

                foreach ($def->field as $i => $field) {

                    $idx = (string)$field->attributes()->name;
                    if (empty($field->attributes()->type)) {
                        if (!empty($this->class->__structure__[$idx])) {
                            $this->pattern->actions->{$action}->addAttribute('type', $this->class->__structure__[$idx]['type']);
                        } else {
                            $this->pattern->actions->{$action}->addAttribute('type', 'ordinary');
                        }
                    } // if

                    $requiredNode = $field->xpath('required'); 
                    $field->addChild('field_required', (int)!empty($requiredNode[0]));
                    $field->addChild('field_type', $field->attributes()->type);
                    $field->addChild('field_size', (int)$field->attributes()->size);

                    if (empty($field->showtype)) {
                        $field->addChild('showtype', (string)$field->attributes()->type);
                    }

                    switch ((string)$field->showtype) {
                        case 'checkbox' :
                            // prepare checkboxes fields for checking
                            $this->list_checkbox_fields[$action][$idx] = $field;
                            break;
                        case 'image' :
                            // prepare images fields for checking
                            $this->list_images_fields[$action][$idx] = $field;
                            break;
                        case 'tag' :
                            // prepare images fields for checking
                            $this->is_tag = true;
                            break;
                        case 'lookup' :
                            $this->list_calc_fields[$action][$idx] = $field; //$this->record_list[$idx];

                            break;
                    } // switch

                    $this->record[$action][$idx] = $field;
                } // foreach

            } // foreach

        } // Init()


        function SetDefaults($params) {
            $this->defaults = array_template($params, $this->class->__structure__);
        } // SetDefaults()


        function ClearDefaults() {
            $this->defaults = array();
        } // ClearDefaults()


        function GetDefaults() {
            return $this->defaults;
        } // GetDefaults()


        function GetItem($key) {
            $key = $this->beforeGet($key);
            $item = $this->class->GetItem($key);
            $item = $this->afterGet($item);

            return $item;
        }


        function beforeAdd($item) {
            return $item;
        } // beforeAdd()


        function afterAdd($key, $item) {
            return $item;
        } // afterAdd()


        function beforeEdit($key, $item) {
            return $item;
        } // beforeEdit()


        function afterEdit($key, $item) {
            return $item;
        } // afterEdit()


        function beforeGet($key, $item) {
            return $key;
        } // beforeAdd()


        function beforeDel($key) {
            return $key;
        } // beforeDel()


        function afterDel($key) {
            return $key;
        } // afterDel()


        function afterGet($item) {
            foreach ($this->edit_calc_fields as $field => $rec) {
                if (is_callable(array($this, 'afterGet_' . $rec['type']))) {
                    $item[$field] = call_user_func(array($this, 'afterGet_' . $rec['type']), $field, $item);
                }
            }

            if (is_callable(array('parent', 'afterGet'))) {
                $item = parent::afterGet($item);
            }

            //die();

            return $item;
        } // afterAdd()


        function afterGetList($item) {
            //var_dump($this->list_calc_fields);
            foreach ($this->list_calc_fields['list'] as $field => $rec) {
                if (is_callable(array($this, 'afterGetList_' . (string)$rec->showtype))) {
                    $item[$field] = call_user_func(array($this, 'afterGetList_' . (string)$rec->showtype), $field, $item);
                }
            }

            if (is_callable(array('parent', 'afterGetList'))) {
                $item = parent::afterGetList($item);
            }

            return $item;
        }


        function afterGetList_lookup($field, $item) {
            $rec = $this->list_calc_fields['list'][$field];
            $table = (string)$rec->lookup->table;
            $lookup = $this->dsp->$table->GetItem($item[$field]);

            if (!empty($rec->lookup->showfield)) {
                $value = $lookup[(string)$rec->lookup->showfield];
            } elseif (!empty($rec->lookup->showfields)) {
                $fields = array();
                foreach ($rec->lookup->showfields->field as $lfield) {
                    $fields[] = $lookup[(string)$lfield];
                } // foreach

                $value = join((string)$rec->lookup->showfields->join, $fields);
            } else {
                $value = '';
            } // if

            return $value;
        }

        /*
        function afterGet_lookup($field, $item) {
        $rec = $this->edit_calc_fields[$field];
        $lookup = $this->dsp->{$rec['table']}->GetItem($item[$field]);
        $this->_prepare_lookup($rec);
        $item;
        //var_dump($this->dsp->{$rec['table']});
        if (!empty($lookup[$rec['showfield']])) {
        $value = $lookup[$rec['showfield']];
        } else {
        $value = '';
        }

        return $value;
        }
        */

        // ======================== Block Records functions ========================================

        /*
        * Create admin listing control
        *
        * @param string $block_id
        *   identificator name for block in XML structure
        * @param array $list
        *   array of data for inner table records
        * @param $table
        *   unknown data
        * @param string $title
        *   Name for label for table listing control
        * @param array $pager_income
        *   assoc array of paging information
        *   - page : current page number
        *   - last : last page number
        *   - pre_url : first part of url for paging link
        *   - past_url : last part of url for paging link
        * @param array $row_panel
        *   addition - optional fields for listing control if need it. By default it's
        *   array('edit' , 'delete')
        * @param array $mass_panel
        *   unknown data
        */
        function MakeListBlock($block_id, $list, $table, $title, $pager_income, $row_panel = array(), $mass_panel = array()) {
            //$order = array($order_field => $order_direct);

            if (empty($block_id)) $block_id = 'record_list';

            $this->dsp->blocks->AddChild($block_id, 'title', $title);

            // Pager
            if (is_array($pager_income)) {
                $pager = $this->dsp->blocks->GetPagerNode(1, $pager_income['last'], $pager_income['current'], $pager_income['pre_url'], $pager_income['post_url']);
            }
            $this->dsp->blocks->AddNode($block_id, $pager);

            // Header
            $sess_field = strtolower(get_class($this)) . '_order' ;
            $this->dsp->blocks->CreateAsCopySXMLNode($block_id . '_header', $this->pattern->actions->list, 'header');
            $this->dsp->blocks->AddChild($block_id . '_header', 'pre_url', $pager_income['pre_url']);
            $this->dsp->blocks->AddChild($block_id . '_header', 'o_direct', $_SESSION[$sess_field]['order']);
            $this->dsp->blocks->AddChild($block_id . '_header', 'o_order', $_SESSION[$sess_field]['order_field']);
            // Records
            $this->dsp->blocks->Create($block_id . '_records', '', 'records');
            foreach ($list as $idx => $item) {
                $key = $this->class->MakeHttpKey($item, $this->defaults);
                $orig_item = $item;
                $item = $this->afterGetList($item);
                $this->beforeShowConvertFields($item);

                // set indicator for records with 'status' 0
                $record_attributes = array('key' => $key);
                if(array_key_exists('status', $item) && $item['status'] != 1) {
                    $record_attributes['status'] = 0;
                }
                if(array_key_exists('deleted', $item) && $item['deleted'] == 1) {
                    $record_attributes['deleted'] = 1;
                }
                if(array_key_exists('locked', $item) && $item['locked'] == 1) {
                    $record_attributes['locked'] = 1;
                }
                unset($item['status']);
                unset($item['deleted']);
                unset($item['locked']);
                $this->dsp->blocks->Create($block_id . '_records_' . $key, '', 'item', $record_attributes);
                foreach ($item as
                    $field => $value) {

                    $rec = null;
                    $rec = $this->pattern->actions->list->xpath("field[@name='{$field}']"); //$this->record_list[$field];
                    $rec = reset($rec);
                    $type = (string)$rec->attributes()->type;

                    $this->dsp->blocks->CreateAsCopySXMLNode($block_id . '_records_' . $key . '_' . $field, $rec, 'item', array('type' => $type, 'field' => $field));
                    $this->dsp->blocks->AddChild($block_id . '_records_' . $key . '_' . $field, 'value', htmlspecialchars($value));
                    $this->dsp->blocks->AddChild($block_id . '_records_' . $key . '_' . $field, 'orig_value', $orig_item[$field]);
                    if (!empty($rec->link)) {
                        $t = (string)$rec->link->t;

                        $urls = array(
                            't' => $t,
                            'op' => (string)$rec->link->op,
                        );
                        if(!empty($rec->link->params) && !empty($rec->link->params->key) && !empty($rec->link->params->value)) {
                            $urls[(string)$rec->link->params->key] = $list[$idx][(string)$rec->link->params->value];
                        }
                        else {
                            $link_key = array_combine($this->dsp->$t->__primary_key__, array($orig_item[$field]));
                            $link_key = $this->dsp->$t->MakeHttpKey($link_key);
                            $urls['key'] = $link_key;
                        }
                        $inner_xml = MakeXML($urls);
                        $this->dsp->blocks->AddXML($block_id . '_records_' . $key . '_' . $field,$inner_xml,'url');
                    }

                    $this->dsp->blocks->AddChildBlock($block_id . '_records_' . $key, $block_id . '_records_' . $key . '_' . $field);
                }

                $this->dsp->blocks->AddChildBlock($block_id . '_records', $block_id . '_records_' . $key);
            } // foreach

            // Row Panel
            $this->dsp->blocks->Create($block_id . '_row_panel', '', 'row_panel');
            if (!empty($row_panel['common'])) {
                $this->dsp->blocks->AddList($block_id . '_row_panel', $row_panel['common'], 'common');
                unset($row_panel['common']);
            }
            $this->dsp->blocks->AddItemsList($block_id . '_row_panel', $row_panel);

            // Mass operations
            if (count($list) > 0) {
                $mass_panel = array(array('title' => 'Удалить', 'opcode' => 'massdel'));
                $this->dsp->blocks->Create($block_id . '_mass_panel', '', 'mass_panel');
                $this->dsp->blocks->AddItemsList($block_id . '_mass_panel', $mass_panel);
            }

            // Common
            $this->dsp->blocks->AddChildBlock($block_id, $block_id . '_header');
            $this->dsp->blocks->AddChildBlock($block_id, $block_id . '_records');
            $this->dsp->blocks->AddChildBlock($block_id, $block_id . '_row_panel');
            $this->dsp->blocks->AddChildBlock($block_id, $block_id . '_mass_panel');

        } // MakeListBlock()


        function MakeFilterBlock($block_id, $filters, $filter = array()) {
            $items = array();

            foreach ($filters as $field => $desc) {
                if (isset($filter[$field])) {
                    $desc['value'] = $filter[$field];
                }
                $desc['field'] = $field;

                $this->dsp->blocks->Create($block_id . '_' . $field, '', 'item', array('field' => $field, 'type' => $desc['type']));
                switch ($desc['type']) {
                    case 'lookup' :
                        $fields = $this->dsp->$desc['table']->__primary_key__;
                        if (!empty($desc['showfields'])) {
                            $fields = $desc['showfields'];
                        } else {
                            $fields[] = $desc['showfield'];
                        }

                        $order = array();
                        if (!empty($desc['order'])) {
                            $order = $desc['order'];
                        }
                        $cause = array();
                        if (!empty($desc['cause'])) {
                            $cause = $desc['cause'];
                        }
                        $lookup = $this->dsp->$desc['table']->GetFieldsByCause($cause, $fields, $order);
                        $lookup = $this->dsp->$desc['table']->ReindexByKey($lookup);

                        foreach ($lookup as &$row) {
                            $row = $row[$desc['showfield']];
                            //MakeCData($row);
                        }

                        $this->dsp->blocks->AddItemsList($block_id . '_' . $field, $lookup, 'values', array());

                        break;

                    case 'enum' :
                        $this->dsp->blocks->AddItemsList($block_id . '_' . $field, $desc['values'], 'values', array());
                        //print_r($this->dsp->blocks->GetAsXML($block_id . '_' . $field));

                        break;

                    case 'field' :
                        $cause = array();
                        if (!empty($desc['cause'])) {
                            $cause = $desc['cause'];
                        }
                        $values = $this->dsp->$desc['table']->GetFieldByCause($cause, $desc['field']);
                        $values = array_unique($values);
                        $values = array_combine($values, $values);
                        $this->dsp->blocks->AddItemsList($block_id . '_' . $field, $values, 'values', array());
                        $desc['type'] = 'enum';

                        break;


                }
                $this->dsp->blocks->Create($block_id . '_' . $field . '_title', $desc['name'], 'title');
                $this->dsp->blocks->AddChildBlock($block_id . '_' . $field, $block_id . '_' . $field . '_title');

                if (isset($filter[$field])) {
                    $this->dsp->blocks->Create($block_id . '_' . $field . '_value', $filter[$field], 'value');
                    $this->dsp->blocks->AddChildBlock($block_id . '_' . $field, $block_id . '_' . $field . '_value');
                }

                $this->dsp->blocks->AddChildBlock($block_id, $block_id . '_' . $field);

            }

        } // MakeFilterBlock()


        /*
        * Create edit entity admin control
        *
        * @param string $block_id
        *   identificator name for block in XML structure
        * @param integer $key
        *   identificator entity
        * @param array $item
        *   assoc data of selected entity
        * @param string $table
        *   unknown data
        * @param string $title
        *   Name for label for edit control
        * @param array $row_panel
        *   addition - optional button-controls for edit control if need it
        * @param array $data
        *   assoc array from validation functionality. Keys :
        *     - data   : array of data from post
        *     - fields : array of error fields
        */
        function MakeEditBlock($block_id, $key, $item, $table, $title, $row_panel = array(), $data=array()) {
            if (empty($block_id)) $block_id = 'record_edit';

            $this->dsp->blocks->AddChild($block_id, 'table', $table);
            $this->dsp->blocks->AddChild($block_id, 'title', $title);

            $this->dsp->blocks->Create($block_id . '_row_edit', '', 'row', array('key' => $this->class->MakeHttpKey($key)));
            $this->MakeEditRowBlock($block_id . '_row_edit', $key, $item, $data);
            $this->dsp->blocks->AddChildBlock($block_id, $block_id . '_row_edit');

            // Row panel
            $this->dsp->blocks->Create($block_id . '_row_panel', '', 'row_panel');
            if (!empty($row_panel['common'])) {
                $this->dsp->blocks->AddList($block_id . '_row_panel', $row_panel['common'], 'common', $row_panel['common']);
                unset($row_panel['common']);
            }
            $this->dsp->blocks->AddItemsList($block_id . '_row_panel', $row_panel);
            $this->dsp->blocks->AddChildBlock($block_id, $block_id . '_row_panel');

        } // MakeEditBlock()



        /*
        * Common Add form control for entity
        *
        * @param string $block_id
        *   identificator name for block in XML structure
        * @param array $item
        *   assoc data of selected entity
        * @param string $table
        *   unknown data
        * @param string $title
        *   Name for label for edit control
        * @param array $row_panel
        *   addition - optional button-controls for edit control if need it
        * @param array $data
        *   assoc array from validation functionality. Keys :
        *     - data   : array of data from post
        *     - fields : array of error fields
        */
        function MakeAddBlock($block_id, $item, $table, $title, $row_panel = array(), $data = array()) {
            if (empty($block_id)) $block_id = 'record_edit';

            $this->dsp->blocks->AddChild($block_id, 'table', $table);
            $this->dsp->blocks->AddChild($block_id, 'title', $title);

            $this->dsp->blocks->Create($block_id . '_row_edit', '', 'row');
            $this->MakeAddRowBlock($block_id . '_row_edit', $item, $data);
            $this->dsp->blocks->AddChildBlock($block_id, $block_id . '_row_edit');

            $this->dsp->blocks->Create($block_id . '_row_panel', '', 'row_panel');
            if (!empty($row_panel['common'])) {
                $this->dsp->blocks->AddList($block_id . '_row_panel', $row_panel['common'], 'common', $row_panel['common']);
                unset($row_panel['common']);
            }
            $this->dsp->blocks->AddItemsList($block_id . '_row_panel', $row_panel);
            $this->dsp->blocks->AddChildBlock($block_id, $block_id . '_row_panel');
        } // MakeAddBlock()




        // ======================== Basic Records functions ========================================

        function GetRecordList($cause, $order = array(), &$page, $pagesize = 30, &$lastpage) {
            $cause = array_merge($cause, $this->defaults);

            $lastpage = ceil($this->class->CountByCause($cause) / $pagesize);

            if ($page > $lastpage) $page = $lastpage;

            $posts = $this->class->GetByCause($cause, $order, $page, $pagesize);
            $_post = array();
            if (!empty($posts)) {
                foreach ($posts as $idx => $post) {
                    $_post = $post;
                    $post = array_template($post, $this->record['list'], true);

                    if(!array_key_exists('status', $post) && array_key_exists('status', $_post)) {
                        $post['status'] = $_post['status'];
                    }
                    $posts[$idx] = $post;
                }
            }
            return $posts;
        } // GetRecordList()


        /**
        * Create row in Add form control of entity
        *
        * @param string $id
        *   identificator name for parent block in XML structure
        * @param array $row
        *   assoc data of selected entity
        * @param array $data
        *   assoc array from validation functionality. Keys :
        *     - data   : array of data from post
        *     - fields : array of error fields
        */
        function MakeAddRowBlock($id, $row, $data = array()) {
            $this->dsp->blocks->CheckBlock($id);

            if (!empty($this->descriptions['add'])) {
                $this->dsp->blocks->AddXML($id, $this->descriptions['add'], 'description');
            }

            $extra_fields = array_diff_key($row, $this->record['add']);
            $row_original = $row;
            $this->beforeAddConvertFields($row);

            foreach ($this->record['add'] as $field => $item) {
                $type = (string)$item->attributes()->type;
                $showtype = (string)$item->showtype;

                // check for validate array data
                if (isset($row[$field]) && !empty($row[$field]) && $row[$field]!='NULL') {
                    $value = !empty($data['data'][$field]) ? $data['data'][$field] : $row[$field];
                } else {
                    $value = !empty($data['data'][$field]) ? $data['data'][$field] : '';
                }
                $item->addChild('value', $value);
                $item->addChild('field', $field);

                // set error atribute for response
                if (in_array($field, $data['fields']) ){
                    $item->addChild('error', 1);
                }

                if (is_callable(array($this, '_prepare_' . $showtype))) {
                    call_user_func(array($this, '_prepare_' . $showtype), &$item, $field, $row_original, $data);
                }

                $this->dsp->blocks->CreateAsCopySXMLNode($id . '_' . $field, $item, 'item');
                $this->dsp->blocks->AddChildBlock($id, $id . '_' . $field);
            }
            return true;
        } // MakeAddRowBlock()


        function _prepare_lookup(&$item) {
            $table = (string)$item->lookup->table;
            $fields = $this->dsp->$table->__primary_key__;

            $template = array();
            if (isset($item->lookup->showfield)) {
                $template[] = (string)$item->lookup->showfield;
            } elseif (isset($item->lookup->showfields)) {
                foreach ($item->lookup->showfields->field as $field) {
                    $template[] = (string)$field;
                }
            }
            
            // set order by param id
            $order = array();
            if (isset($item->lookup->order)) {
                foreach ($item->lookup->order->field as $field) {
                    if ((string)$field != '') {
                        $order[(string)$field->attributes()->name] = (string)$field;
                    } else {
                        $order[(string)$field->attributes()->name] = 'ASC';
                    }
                }
            }
			
            // set query by param id
        	$queryCause = array();
			if ((int)$item->value > 0){
	            $primary = reset($this->dsp->$table->__primary_key__);
				$queryCause = array($primary => (int)$item->value);
			}
            
            $fields = array_merge($fields, $template);
            $lookup = $this->dsp->$table->GetFieldsByCause($queryCause, $fields, $order);
            $lookup = $this->dsp->$table->ReindexByKey($lookup, false);
            
            $template = array_flip($template);

            $values = $item->lookup->addChild('values');
            foreach ($lookup as $idx => $row) {
                $row = array_template($row, $template);
                if (isset($item->lookup->showfields)) {
                    $row = join((string)$item->lookup->join, $row);
                } else {
                    $row = reset($row);
                }

                $value = $values->addChild('item', htmlspecialchars($row));
                $value->addAttribute('id', $idx);
            }
        }


        /*
        * Fill data for edit control
        *
        * @param string $id
        *   identificator name for parent block in XML structure
        * @param integer $key
        *   identificator entity
        * @param array $row
        *   assoc data of selected entity
        * @param array $data
        *   assoc array from validation functionality. Keys :
        *     - data   : array of data from post
        *     - fields : array of error fields
        */
        function MakeEditRowBlock($id, $key, $row, $data=array()) {
            $this->dsp->blocks->CheckBlock($id);

            if (!empty($this->descriptions['add'])) {
                $this->dsp->blocks->AddXML($id, $this->descriptions['edit'], 'description');
            }

            $extra_fields = array_diff_key($row, $this->record['edit']);
            $row_original = $row;
            $this->beforeEditConvertFields($row);

            $items = array();

            foreach ($this->record['edit'] as $field => $item) {
                $type = (string)$item->attributes()->type;
                $showtype = (string)$item->showtype;

                if (isset($row[$field]) && !empty($row[$field]) && $row[$field]!='NULL') {
                    $value = !empty($data['data'][$field]) ? $data['data'][$field] : $row[$field];
                } else {
                    $value = !empty($data['data'][$field]) ? $data['data'][$field] : '';
                }
                $item->addChild('value', $value);
                $item->addChild('field', $field);

                // set error atribute for response
                if (in_array($field, $data['fields']) ){
                    $item->addChild('error', 1);
                }

                if (is_callable(array($this, '_prepare_' . $showtype))) {
                    call_user_func(array($this, '_prepare_' . $showtype), &$item, $field, $row_original, $data);
                }

                $this->dsp->blocks->CreateAsCopySXMLNode($id . '_' . $field, $item, 'item');
                $this->dsp->blocks->AddChildBlock($id, $id . '_' . $field);
            }
            return true;
        }


        /*
        * Process of Adding entity
        *
        * @param array $params
        *   data from Add form control
        */
        function AddRecord($params) {
            $this->afterAddConvertFields($params);

            if (!empty($_FILES)) {
                foreach ($_FILES as $idx => $file) {
                    if(!empty($_FILES[$idx]['name'])) {
                        if ((string)$this->record['add'][$idx]->showtype == 'image') {
                            list($fid, $fpath)= $this->dsp->i->putToPlace($_FILES[$idx]);
                            $params[$idx] = $fid;
                        }
                    }
                    else {
                        $params[$idx] = 0;
                    }
                }
            }

            $params = array_merge($params, $this->defaults);
            $params = $this->beforeAdd($params);
            $params = $this->class->AddItem($params);
            $key = $this->class->_explode_key($params);
            $params = $this->afterAdd($key, $params);
            return $params;
        } // AddRecord()

        function EditRecord($key, $params) {
            $this->afterEditConvertFields($params);

            $key_is = $key;
            if (!empty($_FILES)) {
                foreach ($_FILES as $idx => $file) {
                    if(!empty($_FILES[$idx]['name'])) {
                        if ((string)$this->record['edit'][$idx]->showtype == 'image') {
                            if(IsInt($params[$idx . '_old'])>0 ) {
                                // replace existed file
                                list($fid, $fpath) = $this->dsp->i->replaceByIDX((int) $params[$idx . '_old'], $_FILES[$idx]);

                            }
                            else {
                                // add new image
                                list($fid, $fpath) = $this->dsp->i->putToPlace($_FILES[$idx]);

                            }
                            $params[$idx] = $fid;
                        }
                    }
                    else {
                        $params[$idx] = IsInt($params[$idx . '_old']);
                    }
                }
            }
            $params = array_merge($params, $this->defaults);
            $params = $this->beforeEdit($key, $params);
            
            $params = $this->class->EditItem($key, $params);

            //$key = array_template($params, $key, true);
            $params = $this->afterEdit($key, $params);
            return $params;

        } // EditRecord()


        function NewRecord() {
            $item = $this->class->NewItem();
            $item = array_merge($item, $this->defaults);

            //$item = $this->afterNew($item);
            $this->beforeShowRowConvertFields($item);

            return $item;
        } // NewRecord()


        function beforeShowConvertFields(&$item) {
            foreach ($item as $field => $value) {
                if (is_callable(array($this, '_convert_list_' . (string)$this->record['list'][$field]->showtype))) {
                    call_user_func(array($this, '_convert_list_' . (string)$this->record['list'][$field]->showtype), &$item[$field], &$item, &$field);
                }
            }
        }


        function beforeShowRowConvertFields(&$item) {
            foreach ($item as $field => $value) {
                if (is_callable(array($this, '_convert_' . (string)$this->record['list'][$field]->showtype))) {
                    call_user_func(array($this, '_convert_' . (string)$this->record['list'][$field]->showtype), &$item, $field);
                }
                if (is_callable(array($this, '_convert_row_' . (string)$this->record['list'][$field]->showtype))) {
                    call_user_func(array($this, '_convert_row_' . (string)$this->record['list'][$field]->showtype), &$item[$field]);
                }
            }
        }


        function beforeAddConvertFields(&$item) {
            foreach ($item as $field => $value) {
                if (isset($this->record['add'][$field]) && is_callable(array($this, '_convert_add_' . (string)$this->record['add'][$field]->showtype))) {
                    call_user_func(array($this, '_convert_add_' . (string)$this->record['add'][$field]->showtype), &$item[$field]);
                }
            }
        }


        function afterAddConvertFields(&$item) {
            foreach ($item as $field => $value) {
                if (isset($this->record['add'][$field]) && is_callable(array($this, '_convert_after_add_' . (string)$this->record['add'][$field]->showtype))) {
                    call_user_func(array($this, '_convert_after_add_' . (string)$this->record['add'][$field]->showtype), &$item[$field]);
                }
            }
            if(!empty($this->list_checkbox_fields['add']) && count($this->list_checkbox_fields['add']) >0) {
                foreach ($this->list_checkbox_fields['add'] as $idx=>$chfield) {
                    if(!isset($item[(string)$chfield->attributes()->name])){
                        $item[(string)$chfield->attributes()->name] = 0;
                    }
                }
            }
        }


        function beforeEditConvertFields(&$item) {
            foreach ($item as $field => $value) {
                if (isset($this->record['edit'][$field]) && is_callable(array($this, '_convert_edit_' . (string)$this->record['edit'][$field]->showtype))) {
                    call_user_func(array($this, '_convert_edit_' . (string)$this->record['edit'][$field]->showtype), &$item[$field]);
                }
            }
        }


        function afterEditConvertFields(&$item) {
            foreach ($item as $field => $value) {
                if (isset($this->record['edit'][$field]) && is_callable(array($this, '_convert_after_edit_' . (string)$this->record['edit'][$field]->showtype))) {
                    call_user_func(array($this, '_convert_after_edit_' . (string)$this->record['edit'][$field]->showtype), &$item[$field], &$item, &$field);
                }
            }

            if(!empty($this->list_checkbox_fields['edit']) && count($this->list_checkbox_fields['edit']) >0) {
                foreach ($this->list_checkbox_fields['edit'] as $idx=>$chfield) {
                    if(!isset($item[(string)$chfield->attributes()->name])){
                        $item[(string)$chfield->attributes()->name] = 0;
                    }
                }
            }
        }


        function beforeInsertConvertFields($item) {
        }





        function _convert_ordinary(&$field) {
        }

        function _convert_enum(&$field) {
        }

        function _convert_enum_edit(&$field) {
        }

        function _convert_string(&$field) {
        }

        function _convert_text(&$field) {
        }

        function _convert_integer(&$field) {
        }

        function _convert_list_datetime(&$field) {
            $parts = explode(' ', $field);
            $date_parts = explode('-', $parts[0]);
            $time_parts = explode(':', $parts[1]);

            $parts[0] = implode('.', array_reverse($date_parts));
            unset($time_parts[2]);
            $parts[1] = implode(':', $time_parts);
            $field = implode(' ', $parts);
        }


        function _convert_list_date(&$field) {
            $date_parts = explode('-', $field);

            $field = implode('.', array_reverse($date_parts));
        }


        function _convert_list(&$field) {
        }


        function _convert_edit_datetime(&$field) {
            $parts = explode(' ', $field);
            $date_parts = explode('-', $parts[0]);
            $time_parts = explode(':', $parts[1]);

            $parts[0] = implode('.', array_reverse($date_parts));
            unset($time_parts[2]);
            $parts[1] = implode(':', $time_parts);
            $field = implode(' ', $parts);
        }


        function _convert_edit_date(&$field) {
            if(!empty($field) && $field!='0000-00-00' && $field!='NULL') {
                $date_parts = explode('-', $field);
                $field = implode('.', array_reverse($date_parts));
            }
            else {
                $field = date("d.m.Y");
            }
        }

        function _convert_add_datetime(&$field) {
        	if(!empty($field) && $field != '0000-00-00 00:00:00' && $field != 'NULL') {
	            $parts = explode(' ', $field);
	            $date_parts = explode('-', $parts[0]);
	            $time_parts = explode(':', $parts[1]);
	
	            $parts[0] = implode('.', array_reverse($date_parts));
	            unset($time_parts[2]);
	            $parts[1] = implode(':', $time_parts);
	            $field = implode(' ', $parts);
        	}
            else {
                $field = date("d.m.Y H:i");
            }
        }


        function _convert_add_date(&$field) {
            if(!empty($field) && $field!='0000-00-00' && $field!='NULL') {
                $date_parts = explode('-', $field);
                $field = implode('.', array_reverse($date_parts));
            } else {
                $field = date("d.m.Y");
            }
        }


        function _convert_after_edit_datetime(&$field) {
            $parts = explode(' ', $field);
            $date_parts = explode('.', $parts[0]);
            $time_parts = explode(':', $parts[1]);

            $parts[0] = implode('-', array_reverse($date_parts));
            unset($time_parts[2]);
            $parts[1] = implode(':', $time_parts);
            $field = implode(' ', $parts);
        }


        function _convert_after_edit_date(&$field) {
            $date_parts = explode('.', $field);
            $field = implode('-', array_reverse($date_parts));
        }

        function _convert_after_edit_lookup(&$field) {
            $field = empty($field) ? 0 : (int)$field;
        }

        function _convert_after_add_lookup(&$field) {
            $field = empty($field) ? 0 : (int)$field;
        }

        function _convert_after_add_datetime(&$field) {
            if(empty($field) || $field == 'NULL') {
                $field = date("d.m.Y H:i:s");
            }
            $parts = explode(' ', $field);
            $date_parts = explode('.', $parts[0]);
            $time_parts = explode(':', $parts[1]);

            $parts[0] = implode('-', array_reverse($date_parts));
            unset($time_parts[2]);
            $parts[1] = implode(':', $time_parts);
            $field = implode(' ', $parts);
        }


        function _convert_after_add_date(&$field) {
            if(empty($field)) {
                $field = date("d.m.Y");
            }
            $date_parts = explode('.', $field);
            $field = implode('-', array_reverse($date_parts));
        }

        function _prepare_checkbox(&$item){
        }

        function _convert_list_lookup(&$item){
        }

        function _convert_add_image(&$field) {
            $field = '';
        }

        function _convert_edit_image(&$field) {
            $field = $this->dsp->i->resize($field, empty($this->image_size) ? 100 : $this->image_size);
        }

        function _prepare_image(&$item, $field, $row_original) {
            $item->addChild('old_value', $row_original[$field]);
            $item->addChild('image_original', $this->dsp->i->resize($row_original[$field], 0));
        }

        function _convert_edit_password(&$field) {
            $field = '';
        }

        function _convert_after_add_password(&$field){
            $field = md5($field);
        }

        function _convert_after_add_integer(&$field) {
            $field = IsInt($field) < 1 ? 0 : (int)$field;
        }

        function _convert_after_edit_integer(&$field) {
            $field = IsInt($field) < 1 ? 0 : (int)$field;
        }

        function _convert_after_edit_password(&$field, &$item, &$key) {
            if(empty($field)) {
                unset($item[$key]);
            }
            else {
                $field = md5($field);
            }
        }

        /**
        * Common validate function
        *
        * @param array $data
        *   incoming data to validate
        * @param string $op
        *   action name
        * @param array $key
        *   array of primary key
        *
        * @return array $result
        *   return assoc array:
        *    "is_valid" - true or false
        *    "errors" - errors if exists
        *    "data" - validated data
        */
        public function Validate($data, $op = "add", $key = 0) {

            $result = array(
                "is_valid" => true ,
                "errors" => array(),
                "data" => $data,
                "fields" => array(),
            );
            $errors = array();
            $fields = array();
            $structure = $this->record[$op];
            if(empty($structure)) {
                return $result;
            }

            // start call validate function by showtype for every field
            foreach ($structure as $field=>$item) {
                $_result = array();
                $_result = $this->validateItem($item, $data[$field], $field, $op, $data, $key);
                if(!$_result["is_valid"]) {
                    $result["is_valid"] = false;
                    foreach ($_result["errors"] as $error) {
                        $errors[] = $this->_validate_output_error($error, $_result['replace'], $item, $data[$field]);
                    }
                    $fields[] = $field;

                }
            }
            $result["errors"] = $errors;
            $result["fields"] = $fields;
            return $result;
        }

        /**
        * handle data for one field. Call needed function to validate field.
        *
        * @param SimpleXML $item
        *   data from *.ptrn file about validating field
        * @param mixed $value
        *   post data value for validating field
        * @param string $field
        *   field name
        * @param string $op
        *   action for validating
        * @param array $all_data
        *   all post data from form
        * @param array $key
        *   array of primary key
        */
        function validateItem($item, $value, $field, $op, $all_data, $key = 0) {
            // prepare default response
            $result = array(
                "is_valid" => true ,
                "errors" => array(),
                "replace" => array(),
            );

            if(!empty($item->required)) {
                $errors = array();
                // if field is required
                $_result = false;

                if(!empty($item->required->function)) {
                    // special function to validate
                    $function_name1 = '_validate_' . $op . '_' . (string)$item->required->function;
                    $function_name2 = '_validate_' . (string)$item->required->function;
                    if (is_callable(array($this, $function_name1))) {
                        // we try to find validate function related by action and by name
                        $_result = call_user_func(array($this, $function_name1), &$value, $field, $all_data, $op, $key);
                    }
                    else if (is_callable(array($this, $function_name2))) {
                        // then we try to find validate function by name
                        $_result = call_user_func(array($this, $function_name2), &$value, $field, $all_data, $op, $key);
                    }
                    else {
                        // _validate_default - must exists
                        $_result = $this->_validate_default($value);
                    }
                }
                else {
                    // standard function
                    $function1 = '_validate_' . $op . '_' . (string) $item->showtype;
                    $function2 = '_validate_' . (string) $item->showtype;
                    if (is_callable(array($this, $function1))) {
                        $_result = call_user_func(array($this, $function1), &$value, $field, $all_data, $op, $key);
                    }
                    else if (is_callable(array($this, $function2))) {
                        $_result = call_user_func(array($this, $function2), &$value, $field, $all_data, $op, $key);
                    }
                    else {
                        // _validate_default - must exists
                        $_result = $this->_validate_default($value);
                    }
                }

                // try to handle result from function
                if(!$_result["is_valide"]){
                    $result = $_result;
                }
            }
            return $result;
        }

        function _validate_default ($value) {
            global $error_text;
            $result = array(
                "is_valid" => true,
                "errors" => array(),
                "replace" => array(),
            );
            if(empty($value)) {
                $result["is_valid"] = false;
                $result["errors"][] = $error_text['admin_validate_common']['empty'];
            }
            return $result;
        }

        function _validate_output_error($text, $replacement = array(), $item = '', $value = '') {
            $replace = array();
            $replace["%field"] = !empty($item) ? (string) $item->title : '';
            $replace["%value"] = !empty($value) ? $value : '';
            $replace["%service"] = ucfirst($this->class_name);
            $replace = array_merge($replace, $replacement);
            return str_replace(array_keys($replace), array_values($replace), $text);
        }

        function _validate_add_unique($value, $field, $all_data, $op, $key) {
            global $error_text;
            $result = array(
                "is_valid" => true,
                "errors" => array(),
                "replace" => array(),
            );
            $_result = $this->_validate_default($value);
            if(!$_result["is_valid"]) {
                $result["is_valid"] = false;
                $result["errors"][] = $error_text['admin_validate_common']['empty'];
            }
            $cause = array($field=>$value);
            $_result = $this->class->CountByCause($cause);
            if($_result>0) {
                $result["is_valid"] = false;
                $result["errors"][] = $error_text['admin_validate_common']['not_unique'];
            }
            return $result;
        }

        function _validate_edit_unique($value, $field, $all_data, $op, $key) {
            global $error_text;
            $result = array(
                "is_valid" => true,
                "errors" => array(),
                "replace" => array(),
            );
            $_result = $this->_validate_default($value);
            if(!$_result["is_valid"]) {
                $result["is_valid"] = false;
                $result["errors"][] = $error_text['admin_validate_common']['empty'];
            }
            $cause = array($field=>$value, $this->primary_name => NE. $key[$this->primary_name]);
            $_result = $this->class->CountByCause($cause);
            if($_result>0) {
                $result["is_valid"] = false;
                $result["errors"][] = $error_text['admin_validate_common']['not_unique'];
            }
            return $result;
        }

        function _validate_integer($value, $field, $all_data, $op, $key) {
            global $error_text;
            $result = array(
                "is_valid" => true,
                "errors" => array(),
                "replace" => array(),
            );

            $_result = $this->_validate_default($value);
            if(!$_result["is_valid"]) {
                $result["is_valid"] = false;
                $result["errors"][] = $error_text['admin_validate_common']['empty'];
            }

            if(IsInt($value) < 1) {
                $result["is_valid"] = false;
                $result["errors"][] = $error_text['admin_validate_common']['not_integer'];
            }
            return $result;
        }

        function _validate_string($value, $field, $all_data, $op, $key) {
            global $error_text;
            $result = array(
                "is_valid" => true,
                "errors" => array(),
                "replace" => array(
                    "%max_length" => (int) $this->record[$op][$field]->attributes()->size,
                ),
            );

            $_result = $this->_validate_default($value);
            if(!$_result["is_valid"]) {
                $result["is_valid"] = false;
                $result["errors"][] = $error_text['admin_validate_common']['empty'];
            }

            if(mb_strlen($value, 'utf-8') > (int) $this->record[$op][$field]->attributes()->size) {
                $result["is_valid"] = false;
                $result["errors"][] = $error_text['admin_validate_common']['to_long'];
            }
            return $result;
        }

        function _validate_password($value, $field, $all_data, $op, $key){
            global $error_text;
            $result = array(
                "is_valid" => true,
                "errors" => array(),
                "replace" => array(
                    "%max_length" => MAX_LENGTH_PASS,
                    "%min_length" => MIN_LENGTH_PASS,
                ),
            );

            $_result = $this->_validate_default($value);
            if(!$_result["is_valid"]) {
                $result["is_valid"] = false;
                $result["errors"][] = $error_text['admin_validate_common']['empty'];
            }

            if(strlen($value) > MAX_LENGTH_PASS) {
                $result["is_valid"] = false;
                $result["errors"][] = $error_text['admin_validate_common']['to_long'];
            }

            if(strlen($value) < MIN_LENGTH_PASS) {
                $result["is_valid"] = false;
                $result["errors"][] = $error_text['admin_validate_common']['to_short'];
            }
            return $result;
        }
    } // class Record_admin

