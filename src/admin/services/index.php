<?php
namespace wunderman\wunderforms;

class IndexController {
        
    public static function get() {
        // show main content view
        f::setView(__DIR__."/../views/adminView.php");
    }
}
