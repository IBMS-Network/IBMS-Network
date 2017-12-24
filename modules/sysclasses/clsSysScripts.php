<?php

namespace engine;

/**
 * Class to manage script files
 *
 * @author Sergey.Khaletsky
 */
class clsSysScripts
{
    /**
     * Const with default script type
     */

    const DEFAULT_SCRIPT_TYPE = 'text/javascript';

    /**
     * Current script files queue
     * @var array 
     */
    protected $scripts = array();

    /**
     * Method to register script files to include them into template
     * @param string|array $handle Alias of script file 
     * @param string $src Script file URL
     * @param int $offset (optional) Offset for files queue
     * @param array $attributes (optional) <script> tag attributes
     * @param bool $inFooter (optional) Whether to enqueue the script before </head> or before </body>
     * @return bool
     */
    public function registerFile($handle, $src = null, $offset = null, $attributes = array(), $inFooter = false, $dataMain = '')
    {
        if (!$handle) {
            return false;
        }

        if (is_array($handle)) {
            foreach ($handle as $key => $params) {
                if (empty($params['src']))
                    continue;
                $offset = (!empty($params['offset'])) ? $params['offset'] : null;
                $attributes = (!empty($params['attributes'])) ? $params['attributes'] : null;
                $inFooter = (!empty($params['inFooter'])) ? $params['inFooter'] : false;
                if(!empty($params['dataMain'])) {
                    $attributes['data-main'] = $params['dataMain'];
                }
                $this->scripts[$key] = array('src' => $params['src'], 'offset' => $offset, 'attributes' => $attributes, 'inFooter' => $inFooter);
            }
        } else {
            if ($src) {
                $this->scripts[$handle] = array('src' => $src, 'offset' => $offset, 'attributes' => $attributes, 'inFooter' => $inFooter);
            }
        }
    }

    /**
     * Method to delete file form scripts files queue
     * @param string $handle
     * @return void
     */
    public function unregisterFile($handle)
    {
        if (is_set($this->scripts[$handle])) {
            unset($this->scripts[$handle]);
        }
    }

    /**
     * Method to generate HTML code with scripts
     * @return string
     */
    public function getHTML()
    {
        return $this->prepareFiles();
    }

    /**
     * Method to reset scripts queue
     */
    public function clear()
    {
        $this->scripts = array();
    }

    /**
     * Method to prepare (sort by offset) js files queue 
     * @return string HTML code with sorted script files by offset field
     */
    private function prepareFiles()
    {

        $scriptsForSort = array();

        foreach ($this->scripts as $script) {
            $attrs = '';
            if (!empty($script['attributes'])) {
                //set default script type
                if (!empty($script['attributes']['type'])) {
                    $attrs .= 'type="' . $script['attributes']['type'] . '" ';
                    unset($script['attributes']['type']);
                } else {
                    $attrs .= 'type="' . self::DEFAULT_SCRIPT_TYPE . '" ';
                }
                foreach ($script['attributes'] as $attr => $value) {
                    $attrs .= $attr . '="' . $value . '" ';
                }
            } else {
                $attrs = 'type="' . self::DEFAULT_SCRIPT_TYPE . '" ';
            }
            $jsHTML = '<script src="' . $script['src'] . '" ' . trim($attrs) . '></script>';
            if (!empty($script['offset'])) {
                $scriptsForSort[$script['offset']] = $jsHTML;
            } else {
                $scriptsForSort[] = $jsHTML;
            }
        }

        //sort scripts queue by offset
        ksort($scriptsForSort);
        return implode(PHP_EOL, $scriptsForSort);
    }

}

?>
