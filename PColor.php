<?php

namespace Poundation;

class PColor extends PObject{

    static function Hex2RGB($color){
        $color = str_replace('#', '', $color);
        if (strlen($color) != 6){ return array(0,0,0); }
        $rgb = array();
        for ($x=0;$x<3;$x++){
            $rgb[$x] = hexdec(substr($color,(2*$x),2));
        }
        return $rgb;
    }

    static function cssRgbaFromHex($hexColor, $alpha, $withPrefix = false) {
        $hex = self::Hex2RGB($hexColor);
        $cssString = $withPrefix ? 'rgba(':'';
        $cssString.= $hex[0] . ', ' . $hex[1] . ', ' . $hex[2] . ', ' . number_format($alpha, 1, '.', ',');
        $cssString.= $withPrefix?');':'';
        return $cssString;
    }

}
