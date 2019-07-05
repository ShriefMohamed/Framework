<?php

namespace Framework\lib;


class Template
{
    private $controller;
    private $action;
    private $language;
    private $title;
    private $description;
    private $view;
    private $views = array();
    private $data = array();

    public function __construct($controller, $action)
    {
        if (($action == FrontController::NOT_FOUND_ACTION)) {
            $this->view = VIEWS_DIR . 'notfound' . DS . 'notfound';
            $action = 'notfound';
        } else {
            $this->view = VIEWS_DIR . $controller . DS . $action;
            $action = $action;
        }
        $this->controller = $controller;
        $this->action = $action;
    }

    public function SetLanguage($language)
    {
        if (!empty($language) && null !== $language) {
            $this->language = $language;
        }
        return $this;
    }

//    public function SetTitle($title)
//    {
//        if (!empty($title) && null !== $title) {
//            $this->title = $title;
//        }
//        return $this;
//    }

//    public function SetDescription($description)
//    {
//        if (!empty($description) && null !== $description) {
//            $this->description = $description;
//        }
//        return $this;
//    }

    public function SetData($data)
    {
        $this->data = $data;
        return $this;
    }

    public function SetViews($views)
    {
        $this->views = $views;
        return $this;
    }

    public function Render()
    {
        if (!empty($this->views)) {
            if ($this->data) { extract($this->data); }
            foreach ($this->views as $value) {
                if ($value !== 'view') {
                    if (file_exists(TEMPLATES_DIR . $value . '.php')) {
                        require_once TEMPLATES_DIR . $value . '.php';
                    }
                } else {
                    if (file_exists($this->view . '.php')) {
                        require_once $this->view . '.php';
                    } else {
                        require_once VIEWS_DIR . 'notfound' . DS . 'notfound.php';
                    }
                }
            }
        }
    }

    public function Highlight($menu)
    {
        if ($this->action == $menu) echo "active";
    }
}