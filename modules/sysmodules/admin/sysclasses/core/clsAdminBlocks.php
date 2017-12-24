<?php

namespace engine\modules\admin;

class clsAdminBlocks extends clsSysBlocks
{

    protected function clsSysBlocks()
    {
        parent::__construct();
    }

    /**
     * Function to get html of this block
     * This function declared in parent class clsBlock. Required for site engine.
     *
     * @return html of current page
     */
    public function showContent()
    {

        return $this->getContent();
    }

}