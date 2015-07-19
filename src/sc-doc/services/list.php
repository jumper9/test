<?php
namespace sancor\prizes;

header('Content-Type: application/json; charset=utf-8');

class ListController {
        
    public static function get() {
        $host=$_SERVER["REQUEST_SCHEME"]."://".$_SERVER["HTTP_HOST"]; 
            echo '
{
    "swagger": "2.0",
    "info": {
        "description": "API Sancor Prizes",
        "version": "1.0.0",
        "title": "Sancor Prizes",
        "termsOfService": ""
    },
    "host": "",
    "basePath": "/api",
    "schemes": [
        "http"
    ],
    "paths": {
        ';
    
include(__DIR__."/../documentation/Front.json");
echo ',';
include(__DIR__."/../documentation/Back.json");

echo '
}

}            ';

    }    
    
    
}
