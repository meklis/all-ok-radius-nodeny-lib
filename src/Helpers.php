<?php


namespace Meklis\RadiusToNodeny;


class Helpers
{
        public static function prepareMac($mac) {
            return strtoupper(str_replace(['-', ":", " ", "."], '', $mac));
        }
        public static function replaceEnv($string) {
            foreach ($_ENV as $k=>$v) {
                $string = str_replace(["\${{$k}}", "\${$k}"], $v,$string);
            }
            return $string;
        }
}