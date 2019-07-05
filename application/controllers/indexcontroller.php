<?php


namespace Framework\controllers;

use Framework\lib\AbstractController;

/**
 * Class IndexController
 *
 * @package   Framework\controllers
 *
 * @author    Shrief Mohamed
 *
 * Description
 */
class IndexController extends AbstractController
{
    public function DefaultAction()
    {
        $data = null;
        $this->_template->SetData(['data' => $data])
            ->SetViews(['head', 'header', 'view', 'footer'])
            ->Render();
    }
}