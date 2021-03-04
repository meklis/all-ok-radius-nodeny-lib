<?php


namespace Meklis\RadiusToNodeny;


class Settings
{
    protected $settings;
    function __construct($settings)
    {
        $this->settings = $settings;
    }
    function __get($name)
    {
        if(isset($this->settings[$name])) {
            return $this->settings[$name];
        }
        return  null;
    }
    function __set($key, $value)
    {
        $this->settings[$key] = $value;
    }

    function get($propertyName) {
        $elements = explode(".", $propertyName);
        $arrayKey = join('',array_map(function ($e) {
            return "['{$e}']";
        }, $elements));
        $return = null;
        $evalArrayBlock = "if(isset(\$this->settings{$arrayKey})) {\$return = \$this->settings{$arrayKey}; }";
        eval($evalArrayBlock);
        return $return;
    }
}