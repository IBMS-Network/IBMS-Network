<?php

namespace engine;

/**
 * Class to manage styles files
 *
 * @author Sergey.Khaletsky
 */
class clsSysStyles
{
    /**
     * Const with default link type
     */

    const DEFAULT_LINK_TYPE = 'text/css';

    /**
     * Const with default rel attribute value
     * @see link types here https://developer.mozilla.org/en-US/docs/Web/HTML/Link_types
     */
    const DEFAULT_REL = 'stylesheet';

    /**
     * Current styles files queue
     * @var array 
     */
    protected $styles = array();

    /**
     * Method to register style files to include them into template
     * @param string|array $handle Alias of style file 
     * @param string $src Style file URL
     * @param int $offset (optional) Offset for files queue
     * @param array $attributes (optional) <link> tag attributes
     * @param bool $inFooter (optional) Whether to enqueue the script before </head> or before </body>
     * @return bool
     */
    public function registerFile($handle, $src = null, $offset = null, $attributes = array())
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
                $this->styles[$key] = array('href' => $params['src'], 'offset' => $offset, 'attributes' => $attributes);
            }
        } else {
            if ($src) {
                $this->styles[$handle] = array('href' => $src, 'offset' => $offset, 'attributes' => $attributes);
            }
        }
    }

    /**
     * Method to delete file form styles files queue
     * @param string $handle
     * @return void
     */
    public function unregisterFile($handle)
    {
        if (is_set($this->styles[$handle])) {
            unset($this->styles[$handle]);
        }
    }

    /**
     * Method to generate HTML code with scripts
     * @return string
     */
    public function getHTML()
    {
        return $this->prepare();
    }

    /**
     * Method to prepare (sort by offset) js files queue 
     * @return string HTML code with sorted script files by offset field
     */
    private function prepare()
    {

        $stylesForSort = array();

        foreach ($this->styles as $style) {
            $attrs = '';
            if (!empty($style['attributes'])) {
                //set default script type
                if (isset($style['attributes']['type'])) {
                    $attrs .= 'type="' . $script['attributes']['type'] . '" ';
                    unset($style['attributes']['type']);
                } else {
                    $attrs .= 'type="' . self::DEFAULT_LINK_TYPE . '" ';
                }
                //set default value rel attribute value
                if (isset($style['attributes']['rel'])) {
                    $attrs .= 'type="' . $style['attributes']['rel'] . '" ';
                    unset($style['attributes']['rel']);
                } else {
                    $attrs .= 'type="' . self::DEFAULT_REL . '" ';
                }

                foreach ($style['attributes'] as $attr => $value) {
                    $attrs .= $attr . '="' . $value . '" ';
                }
            } else {
                $attrs = 'type="' . self::DEFAULT_LINK_TYPE . '" rel="' . self::DEFAULT_REL . '"';
            }
            $linkHTML = '<link href="' . $style['href'] . '" ' . trim($attrs) . ' />';
            if (!empty($style['offset'])) {
                $stylesForSort[$style['offset']] = $linkHTML;
            } else {
                $stylesForSort[] = $linkHTML;
            }
        }

        // sort styles queue by offset
        ksort($stylesForSort);
        return implode(PHP_EOL, $stylesForSort);
    }

}
