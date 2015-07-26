<!DOCTYPE html>
<html lang="en">
<head>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <title>Wunderforms</title>
    <link rel="shortcut icon" href="http://s3-sa-east-1.amazonaws.com/movistare2/img/favicon.ico" type="image/x-icon">
    <s cript type="text/javascript" src="<?php echo S3_PREFIX; ?>/js/jquery/jquery-1.11.2.js"></s cript>
    <script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script type="text/javascript" src="<?php echo S3_PREFIX; ?>/js/jquery.fancybox/jquery.fancybox.min.js?v=2.1.5"></script>
    <script type="text/javascript" src="<?php echo S3_PREFIX; ?>/js/jquery.blockui/jquery.blockui.js"></script>
    <script type="text/javascript" src="<?php echo S3_PREFIX; ?>/js/jquery.te/jquery.te.js"></script>
    <script type="text/javascript" src="<?php echo S3_PREFIX; ?>/js/jquery.colorpicker/jquery.colorpicker.js"></script>
    <script type="text/javascript" src="<?php echo S3_PREFIX; ?>/js/jquery.datetimepicker/jquery.datetimepicker.js"></script>
    <script type="text/javascript" src="<?php echo S3_PREFIX; ?>/js/jquery.fileupload/jquery.fileupload.js"></script>
    <script type="text/javascript" src="<?php echo S3_PREFIX; ?>/js/jquery/jquery-ui.js"></script>

    <link type="text/css" rel="stylesheet" href="<?php echo S3_PREFIX; ?>/js/jquery/jquery-ui.css">
    <link type="text/css" rel="stylesheet" href="<?php echo S3_PREFIX; ?>/js/jquery/theme.css">
    <link type="text/css" rel="stylesheet" href="<?php echo S3_PREFIX; ?>/js/jquery.fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
    <link type="text/css" rel="stylesheet" href="<?php echo S3_PREFIX; ?>/js/jquery.te/jquery.te.css" media="screen" />
    <link type="text/css" rel="stylesheet" href="<?php echo S3_PREFIX; ?>/js/jquery.colorpicker/jquery.colorpicker.css" media="screen" />
    <link type="text/css" rel="stylesheet" href="<?php echo S3_PREFIX; ?>/js/jquery.datetimepicker/jquery.datetimepicker.css" media="screen" />
    <link type="text/css" rel="stylesheet" href="<?php echo S3_PREFIX; ?>/js/jquery.fileupload/jquery.fileupload.css" media="screen" />
    <link type="text/css" rel="stylesheet" href="<?php echo HTML_PREFIX; ?>/css/cms.css">
    <link type="text/css" rel="stylesheet" href="<?php echo HTML_PREFIX; ?>/css/buttons.css">
    <link type="text/css" rel="stylesheet" href="<?php echo HTML_PREFIX; ?>/css/cards.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <script type="text/javascript" src="<?php echo HTML_PREFIX; ?>/js/app/app.js"></script>
    <script type="text/javascript" src="<?php echo HTML_PREFIX; ?>/js/app/security.js"></script>
    <script type="text/javascript" src="<?php echo HTML_PREFIX; ?>/js/app/api.js"></script>
    <script type="text/javascript" src="<?php echo HTML_PREFIX; ?>/js/app/users.js"></script>
    <script type="text/javascript" src="<?php echo HTML_PREFIX; ?>/js/app/clients.js"></script>
    <script type="text/javascript" src="<?php echo HTML_PREFIX; ?>/js/app/forms.js"></script>
    <script type="text/javascript" src="<?php echo HTML_PREFIX; ?>/js/app/dashboard.js"></script>
    <style class="cp-pen-styles">@import url(http://fonts.googleapis.com/css?family=Roboto:400,700,300);</style>
    
    <!-- Preview Styles -->
    <style>
    html,body{height:100%}
    body{margin:0;background:#efefeb}
    #menu-wrapper{margin:0 auto;width:100%;display:none}
</style>
<script>$(function() { 
    w.load();
    //$(".nav").on("mouseover",function() { $(".nav").height(400);} ); 
    $(".navMenu    li a ").click(function() { $(".navMenu").hide();} ); 
    $(".nav > li > a").on("mouseover",function() { $(".navMenu").show();} ); 

});
</script>
<meta name="robots" content="noindex,follow" />
    </head>

<body>
<div id='dimScreen'></div>
<div class="top"></div>
<div id="menu-wrapper">
    <ul class="nav env_<?PHP echo ENV; ?>" style='width:100%;height:50px'>
        <li><a href="javascript:w.gotoPage('index')">
                <div style='float:left;margin:5px 10px 0 0'><img src='<?php echo HTML_PREFIX; ?>/css/images/wun2.png' height=30></div>
                <div style='font-size:20px;float:left;margin-top:0px;'>Wunderforms</div>
                </a>
        </li>
        <li id='topmenu_dashboard'><a href="javascript:w.gotoPage('index')" >Dashboard</a></li>
        <li id='topmenu_clients'><a href="javascript:w.gotoPage('index?admin=clients')">Clients</a></li>
        <li id='topmenu_forms'><a href="javascript:w.gotoPage('index?admin=forms')">Form Designer</a></li>
        <li id='topmenu_users'><a href="javascript:w.gotoPage('index?admin=users')">Users</a></li>
        <li id='topmenu_api'><a href="javascript:w.gotoPage('index?admin=api')">The Cool Form API</a></li>
        

        <li id='topmenu_username' style='float:right'><a href="#" id='top_username'></a>
            <div class='navMenu'>
                <div class="nav-column" style='width:140px'>
                    <h3 class="orange"></h3>
                    <ul>
                        <li><a href="javascript:w.users.changePwd()">Change Password</a></li>
                        <li><a href="javascript:w.security.doLogout()">Close Session</a></li>
                    </ul>

                </div>
            </div>
        </li>
    </ul>

<div id='header' style='height:56px;width:100%;z-index:100;background-color:white;box-shadow:0 2px 4px rgba(0, 0, 0, 0.15);display:none'></div>

</div>
    <div id='preloadImages'></div>
    <div id='topAlert' style='display:none'>Datos grabados</div>
    <div id='main'>
        <div id='content'></div>
        <div id='modal'></div>
        <div id='addContentPopup' style='position:absolute;bottom:10px;right:45px;width:400px;height:180px;background-color:white;border:1px solid #cccccc;display:none'></div>
    </div>
    <div id='version' style='position:fixed;right:30px;bottom:4px;'><?PHP echo VERSION;?></div>
	
    
</body>

</html>