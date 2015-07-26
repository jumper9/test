<?php
namespace wunderman\wunderforms;

class IndexController {
        
    public static function get() {
        // show main content view
        f::setView(dirname(__FILE__)."/../views/apiView.php");
    }
}
