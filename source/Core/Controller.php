<?php

namespace Source\Core;

use Source\Core\View;

class Controller
{
    /** @var View $view */
    protected $view;

    /** @var  */
    protected $seo;

    /** @var */
    protected $message;
    public function __construct(string $pathToViews)
    {
        $this->view = new View($pathToViews);
    }
}