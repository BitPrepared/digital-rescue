<?php

namespace Rescue;

use \stdClass;

class RescueMenuItem
{

    public static function createMenuItem($title,$path,$controller,$templateUrl)
    {
        $menu = new stdClass;
        $menu->Path = $path;
        $menu->Controller = $controller;
        $menu->TemplateUrl = $templateUrl;
        $menu->Title = $title;

        return $menu;
    }

}
