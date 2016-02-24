<?php
    /**
    * FileManager Class
    *
    * @category  Files & Folder
    * @package   FileManager
    * @author    Nghia Duong <nghiadh.job@gmail.com>
    * @copyright Copyright (c) 2016
    * @license   http://opensource.org/licenses/gpl-3.0.html GNU Public License
    * @version   1.0
    **/
?>
<?php
    define("AUTHENTICATE_USER","");
    define("AUTHENTICATE_PASSWORD","");
    define("RESTRICT_ACCESS_CHILD_FOLDER_ONLY",true);
?>
<?php 
   
    //include_once "function.php";
    $FM = new FileManager();
    if( defined('AUTHENTICATE_USER') && AUTHENTICATE_USER !="")
    {
        $FM->requireLogin();
    }
    if($FM->isAjaxCall())
    {
        echo $FM->processAjax();
        exit;
    }
    else
    {
        
        if(isset($_GET['download']) && !empty($_GET['download']))
        {
            $FM->download($_GET['download']);
            exit;
        }
        if(isset($_GET['ac']) && $_GET['ac'] == "upload")
        {
            echo json_encode($FM->upload());
            exit;
        }
    }
    $sPath = isset($_GET['path'])?$_GET['path']:dirname(__FILE__);
    $aFiles = $FM->scanDir($sPath);
    if(!count($aFiles))
    {
        exit("There are some problems. Script cannot init and get the list folders and files in where it was installed");
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>File Manager 1.0</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
       
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <!--<link rel="stylesheet" href="main.css">-->
        <style>
            html {
                position: relative;
                min-height: 100%;
                height:100%;
               
                margin: 0;
                padding: 0;
            }
            body {
            
                height: 87%;
                margin: 0;
                min-height: 87%;
                padding: 0;
            }
            .footer {
                position: absolute;
                bottom: 0;
                width: 100%;
            
                height: 30px;
                background-color: #f5f5f5;
            }
            
            .clear{
                clear: both;
            }
            
            body > .container {
                padding: 60px 15px 0;
            }
            .container .text-muted {
                margin: 20px 0;
            }
            
            .footer > .container {
                padding-right: 15px;
                padding-left: 15px;
            }
            
            code {
                font-size: 80%;
            }
            .navbar.navbar-fixed-top{
                margin-bottom:0 !important;
            }
            #file-manager-main-view{
                margin-bottom: 0px;
                margin-top: 52px;
                height: 100%;
                position:relative;
                z-index:8888;
                padding-left:0;
            }
            #file-treeview{
                border-right:5px solid #dfdfdf;
                height: 100%;
                overflow-y: auto;
                padding: 15px 4px 0 4px !important;
            }
            #file-list{
                height: 100%;
                overflow-y: auto;
                padding: 0 4px 0 4px !important;
            }
            
            .current-path{
                width:100%;
            }
            .file-item{
                display:inline-block;
            }
            .li-folder,
            .folder{
                background: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAADqElEQVRIiZ2UTWhcVRTHf/fNm06SSei0aa1F0ahJraCkuOgiiMaNoFiqriQgdKsgdKFLUZeC0FUJ7lwUBXGRjVA3Wr/Q6CIBwUajITZYM+1kZpJ0vt679xwX983LG5tJqhcu7/LgnN/5/8+9x6gqqwtv1cRGJduyRI0IcQpwBb++Sr6LQB1YnDxzsc4+S1UBMKrK9aX39dj4FFvlZTbLyxSGSuQLhxkYOUpjYx2A1lYVZ2PaW1VcHAGsJtB3J89cXOwHCAGQOoH7ndLdY5SOn2S78ifV679QKf/FkXtOMHqoCOOP9SSIWo2xrfLa2LWf508BD/RT4gEKGq9DvA7BQYZL9zF8+BnqN//mh8/meG7q+E7E0BEA8qWHGL1/gl9/+m5sL6vCHknq1WDraO4uDo6OE1vpjWhWAAiaFdz48zTb8V75uwBFRbtH/KkFOeftiB1hLiAITE+wiqPdsfsDVEElAWkaDuoBzimB0V0BuhOwlwJQUV95GiAgCUCU3C6JVOW2f7sDFEQ0safrkRIkCaxT8uHtwZoUsC9AuwqSxChgJE1gneAk+P8AVHcUpFYoJlEQW8GJIgrGQLcTd25RVoH6r8n0wFrBOV9EYECNwfwXBar4+ZNVYDStMLKCdV6BKhijGHNnCoLEDVQUEUFEEee3SgKIxdvkFCeKE3BCeo3nZmfemZudOdUXoGiaWJ0iThARajdupAAnYNPkiksKePzJKR585MTbwJd9LfJvSr09Jk84dIxvL3+DinKwGBKGOQYKoe+DyTS6do3S0DClkxOsXP2t1N8i2FFASJAvYq0wWPD3ZXAgjxOInWIz28gwI2OvdcfArivsJvc9UAInqfedSMgFSmyV2CmBQNBVYMAk82uvZocAzkrSVEWNpuOiHTlygSGKhQPWX9EsIHBdQP95FAK0WzHFwbx/bJK5npEQBIbIehU+uWKMwRjYbGywsv4jD+8oCIAeOQFAvda6svLHBjZ2ydj2FW3e6tBsx0RWiGIhsprCIquUb21waelSZkByCBgh09sA4ImXPnj6Znn7w6WrZQ9JKlpZq16u1JrrlWqTcrVBfbtDq+OIrBDHwtFYed0UU4uefWpiFGhkVRhVLxlgbnbmQnF46PyjpyeZ/+J7Xnj1o9PAwtzszL3ANHAWmD6Qz5UGCiGDhTy1rRZR7NhuRAuvvPHpFNDO9qUHkEDOARfaHTv/8vlPXgRa/25c8mqngbPLqxtrb773+cfA10n1ZAH/AGczgzxfHZhwAAAAAElFTkSuQmCC');
               
            }
            .li-folder{
                background-repeat: no-repeat;
                background-position: left top;
                padding-left:24px !important;
            }
            .tr-file-holder.wait .file-item,
            .li-folder.wait,
            #message-holder.wait
            {
                background-image:  url('data:image/gif;base64,R0lGODlhEAAQAAAAACH/C05FVFNDQVBFMi4wAwH//wAh+QQLBgAPACwAAAAAEAAQAIMvLy92dnaUlJSlpaWtra21tbW9vb3GxsbOzs7W1tbe3t7n5+fv7+/39/f///////8EifDJ52QJdeq3QnoC4DSFsnFAAAIPMizaAheAEbrI0yyOEwSGRkCAKAwIO2PjoQikGktK4zAowCauzCTxkRgGYNNmajBQBWixhlEoEAyTBUIrUVwlUwFy8z0wKEYIUwkNDApBCUdLdhwDCUULjg8MCVokBQ4HBA9UfxsLBDCaIwRdGiMSCAYVDVoRACH5BAsGAA8ALAAAAAAQABAAgz09PXNzc4uLi6Wlpa2trbW1tb29vcbGxs7OztbW1t7e3ufn5+/v7/f39////////wSL8Mnn5Ah16reCegHgNICxPUwQgMAjAIe2MM8BhwmwNovjCAJEIzBAGAYAxqIwaHAugobz0WAwCwtNwpCZJBITYwF7ahwMhjOBQN5YCwSThKHoShTZSQMxIEwnRwc0DkxCBwlVCgYNCX1OeE8JCFgDYAwJXQ1jDgcENQM0GwsEWZ0jBGAbIxJGFQ1dEQAh+QQLBgAPACwAAAAAEAAQAIM5OTl/f3+UlJSlpaWtra21tbW9vb3GxsbOzs7W1tbe3t7n5+fv7+/39/f///////8Ei/DJ5yQRdeq3xHpB4DCBsT3k8AjBChzawjxIkLAJ0DaK4xSDRENgUCAIAEYuiTIMCo2GxNEIAACFDAehlQQEE4TBcJhtOuODgUAofDaMQoFgkjB6G8Vb0kAMCFIaTmUUQAgNB0J3Bg0Jf1J6HEEIbkEoCVoNcg4HBA8HA2YxBB+dVAQJJ1QSYhUNWhEAIfkECwYADwAsAAAAABAAEACDOjo6fX19mJiYpaWlra2ttbW1vb29xsbGzs7O1tbW3t7e5+fn7+/v9/f3////////BI3wyeekGXXqx8p6Q+A0AbI9DVGAAQiY08I8ypAQggIIqOI4hYGiYTgsEoYA4wAANDiXQiMzCjQNmccCkZUIBhOEoTjbLAICAcFAUH02uiZgwvBt1poGYkB4ai4HM0ADCA0HCQ11Bg0JfE8KHws2CB42HAlZDQUFDgcEDwcDZRoLBB+eIwQJJyMSYhVTExEAIfkECwYADwAsAAAAABAAEACDQUFBb29vi4uLpaWlra2ttbW1vb29xsbGzs7O1tbW3t7e5+fn7+/v9/f3////////BIvwyeckMnXqxwp7B+E0Q7I9DWGAw1MEirZ8yqAcwxIUqOI4BVvDgGAoEAIGIhBocAyDQiMzEgACiMxjkd0QeBaD4fDZLARMgYHwXZwUAcB1YtRKCgdNAzEgOCcOcgMfQAMIDQcJDQwJAWdyTgpuCyUIBZQrCQNaDQUFDiEsZTIEbiEjBCYbI2EVUxMRACH5BAsGAA8ALAAAAAAQABAAgzw8PH9/f5iYmKWlpa2trbW1tb29vcbGxs7OztbW1t7e3ufn5+/v7/f39////////wSM8MnnJDJ16scKewfhNIWyPQ1hgMSDDIu2fMqghMyAoIvjFLaGAcFYKAiMhCDQ4BgGhUZmRAgIEjJEZnI4TC6Gw2fDWFoNBEIhtlkE3oEJQ7GVFLyTxovQnDgCAAEmPzoNBwkNCwQACwMAAE0KMY0JCAIFAAUPCQVbJJ0hD49sMgQxIQ4LmScjFhgcWxEAIfkECwYADwAsAAAAABAAEACDQEBAa2tri4uLpaWlra2ttbW1vb29xsbGzs7O1tbW3t7e5+fn7+/v9/f3////////BIzwyeckMnXqxwp7B+E0hbI9DWGAxIMMi7Z8yqCEzICgi+MUtoYBwVgoCI0FsMExDAqNzOgwIJgmC0RmcthZDIbDZ8MgDJ4GAqEQIw8EgsGEodhKuprGC6lxCAACMT86DVQKCQUBSgEBTAoxC1WMBwByN1skBQ4BAA+cCScLBDEAAA4LACsbIxKMFVETEQAh+QQLBgAPACwAAAAAEAAQAIM1NTVnZ2eBgYGlpaWtra21tbW9vb3GxsbOzs7W1tbe3t7n5+fv7+/39/f///////8EjfDJ5yQyderHCnsH4TSFsj0NYYDEgwyLtnzKoITMgKCL4xS2hgHBWCgIjQWwwTEMCo3M6PCMTRaIzCSRmFwMh8+mcTCADQRCwaphpFUTRkJr6U4arwBz4hgIBh8/TwoBMDcCDAcCAkwKMQYABQIBCAEtWFoMAAIOAQAPkyYbBwAmng4MAQcnIxKTFVETEQAh+QQLBgAPACwAAAAAEAAQAIMlJSVkZGSQkJClpaWtra21tbW9vb3GxsbOzs7W1tbe3t7n5+fv7+/39/f///////8Ei/DJ5yQyderHCnsH4TSFsj0NYYDEgwyLtnzKoITMgKCL4xS2hgHBWCgIjQWwwTEMCo3M6PCMTRaIzCSRmFwMh8+mcTCADQTCoLvpFFTegVaisEoYAQCAqXGGKXoCCwGCCwkCDAkDSA8lIAAEAgEJAQccCVp4AQ6SD5F2WwArkg4MAjsbIxIDAhVRExEAIfkECwYADwAsAAAAABAAEACDPj4+b29vkJCQpaWlra2ttbW1vb29xsbGzs7O1tbW3t7e5+fn7+/v9/f3////////BIrwyeckMnXqxwp7B+E0hbI9DWGAxIMMi7Z8yqCEzICgi+MUtoYBwVgoCI0FsMExDAqNzOjwjE0OgswkkZgEAIDAbtM4GAzYgPpw6hRU24JWorBKGAIwU+M8fBxfAgsCBAwMCQYNCQNIIGwJAG8CNV2HWg1qDgMCD04fGwtiD5sODARdGyMSThVRExEAIfkECwYADwAsAAAAABAAEACDPz8/c3NzkJCQpaWlra2ttbW1vb29xsbGzs7O1tbW3t7e5+fn7+/v9/f3////////BIfwyeckMnXqxwp7B+E0hbI9DWGAxIMMi3aYrxIyA4IujhMAA0bgwFgoCI1FYdB4LAQAAKNJaRwGhdjkIMhMEomJIEAOb6wGw+EXPZw6BdVEgdkotJLGABCgTgwDRBQ/QSoMDHQNCQNILjoKQwZZA2EMCV4NAl0GLVcfG09hnCMEZhojFnUNXhEAIfkECwYADwAsAAAAABAAEACDLi4ub29vjY2NpaWlra2ttbW1vb29xsbGzs7O1tbW3t7e5+fn7+/v9/f3////////BI3wyeckMnXqpwB6B+E0hbI9DACAxIMMi5aYB0DczPA1i+MEtkYgkFgoCLzCoPFIAAMNJqVxGBRik4MgM0kkJgOB2LShGgzaIfHEKBQIhskCwZUosJKGQSydGAYHDBQDAQUMBwkNDAoGDQkDSC5fCwMJCFeVKAlcJAUOISADghsLBDEhIwRfGyMWGA8NXBEAOw==');
                background-repeat: no-repeat;
                background-position: left center;
            }
            .message-icon,
            .folder{
                width:24px;
                height:24px;
            }
            .file{
                background:  url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH4AEVCQwhbzpb4gAAAN9JREFUSMft1r1KQ0EQBeAvN2oQfJ9UQtJYpdbeZ7AVrOzyJKZJpSkCIZUiFj6Djb2dkGjhCEFyk9016e6BLRbO/B52Z1p+0MUZ2rbjHk8SUYXzMY4T+D1McSoD17hN5N7gAe+pQapoy2dGQo84x11KkEoZ5rjAaFuQkgC9aFUfryF6t47cDgOYJTj/QGfl/oYjHNbZH2Rm/xLnr/A710DOO9gr1rXoCicJWgxLK/hKsFv+p4Jho0GjQaPB7jVYxpdbig4WmyqYxNCXOTp/nV9iUEdoFawtq1jEEvBcR/gGW/ArD+Xu+KoAAAAASUVORK5CYII=');
                height:24px;
                width:24px;
                background-position: left center;
            }
            .left-list ul{
                list-style: none;
                padding-left:2px;
            }
            ul.tree-items-holder li{
                padding:3px;
            }
            .file-context-menu ul li a:hover,
            #table-list-detail tbody tr:hover,
            .active-context-menu,
            ul.tree-items-holder li .file-name:hover
            {
                background-color: #bdf;
                cursor: pointer;
            }
            
            ul.tree-items-holder li .file-name {
                color: #333;
                display: block;
                padding: 0 2px 0 5px;
                text-decoration: none;
                width:200%;
            }
            ul.tree-items-holder li::before {
                content: "—";
                margin-left: -40px;
                position: absolute;
                color:#dfdfdf;
            }
            ul.tree-items-holder li ul{
                border-left:1px solid #dfdfdf;
                padding-left: 16px;
            }
            .file-name{
                height:24px;
                line-height: 24px;
            }
            input.check_all_item{
                margin-top:0;
            }
            #table-list-detail th, #table-list-detail td{
                vertical-align: middle;
                  padding: 4px;
            }
            .file-context-menu{
                background: #fff none repeat scroll 0 0;
                border: 1px solid #bebebe;
                display: inline-block;
                font-family: inherit;
                font-size: inherit;
                list-style-type: none;
                margin: 5px;
                max-width: 360px;
                min-width: 180px;
                padding: 4px 0;
                position: fixed;
                z-index: 9501;
            }
            .file-context-menu ul{
                list-style: none;
                padding-left:0;
                margin:0;
            }
            .file-context-menu ul li a{
                display:block;
                padding:3px 5px;
            }
            .file-context-menu ul li a:hover{
                text-decoration: none;
            }
            #message-holder{
                height: 30px;
                line-height: 30px;
                text-align: left;
                color:green;
                padding:0 25px;
            }
            .modal.fade{
                z-index: 9999;
            }
            .modal-backdrop{
                z-index:8888;
            }
            .body-files{
                max-height: 250px;
                overflow-y: auto;
            }
            .body-files ul{
                list-style: decimal-leading-zero;
            }
            #compress_file_path{
                width:100%;
            }
            .nav.navbar-nav li > a {
                text-align: center;
                padding:7px;
                font-size:11px;
            }
            .nav.navbar-nav li > a > i{
                font-size:14px;
            }
            .navbar{
                min-height: 40px;
            }
            .navbar-brand{
                height:44px;
                line-height: 14px;
            }
            .li-devision{
                line-height: 14px;
                height: 44px;
                border-left: 1px solid #dfdfdf;
                width:1px;
                margin:0 3px;
            }
            .nav.navbar-nav li > a > i.fa{
                display:block;
            }
            .example{
                font-style: italic;
                font-size:11px;
            }
            #manager-file-editor{
                
                height: 100%;
                margin-bottom: 0;
                margin-top: 52px;
                padding-left: 0;
                position: relative;
                z-index: 8888;
            }
            #simple-editor{
                width: 100%;
                height:100%;
            }
            .modal-dialog{
                width:444px;
            }
        </style>
    </head>

    <body>
        <?php if(isset($_GET['editor']) && !empty($_GET['editor'])):?>
            <nav class="navbar navbar-default navbar-fixed-top">
            <div class="container-fluid ">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="">File Manager</a>
                </div>
                <div id="navbar" class="collapse navbar-collapse">
                    <ul class="nav navbar-nav">
                        <li><a href="javascript:void(0);" class="ajax_request" onclick="FILE_MANAGER.saveFile(this);"><i class="fa fa-save"></i> Save File</a></li>
                        <li><a href="javascript:void(0);" class="ajax_request" onclick="FILE_MANAGER.reloadFile(this);"><i class="fa fa-refresh"></i> Reload File</a></li>
                        
                    </ul>
                </div><!--/.nav-collapse -->
            </div>
        </nav>
          <div class="container-fluid " id="manager-file-editor" style="padding-right:0;">
            <textarea id="simple-editor" file="<?php echo $_GET['editor'];?>"><?php echo htmlentities($FM->readFileContent($_GET['editor']));?></textarea>
          </div>
        <?php else:?>
        <!-- Fixed navbar -->
        <nav class="navbar navbar-default navbar-fixed-top">
            <div class="container-fluid ">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#">File Manager</a>
                </div>
                <div id="navbar" class="collapse navbar-collapse">
                    <ul class="nav navbar-nav">
                       <li><a href="?a"> <i class="fa fa-files-o"></i>  Explorer</a></li>
                        <li><a href="javascript:void(0);" class="ajax_request" onclick="FILE_MANAGER.goTo(this);"><i class="fa fa-paper-plane-o"></i> Go To</a></li>
                        <li><a href="javascript:void(0);" class="ajax_request" onclick="FILE_MANAGER.newFile(this);"><i class="fa fa-file"></i> New File</a></li>
                        <li><a href="javascript:void(0);" class="ajax_request" onclick="FILE_MANAGER.newFolder(this);"><i class="fa fa-folder"></i> New Folder</a></li>
                        <li><a href="javascript:void(0);" class="ajax_request" onclick="FILE_MANAGER.copy();"><i class="fa fa-copy"></i> Copy</a></li>
                        <li><a href="javascript:void(0);" class="ajax_request" onclick="FILE_MANAGER.move(this);"><i class="fa fa-crosshairs"></i> Move File</a></li>
                        <li><a href="javascript:void(0);" class="ajax_request" onclick="FILE_MANAGER.remove(this);"><i class="fa fa-remove"></i> Remove</a></li>
                        <li class="li-devision"></li>
                        <li><a href="javascript:void(0);" class="ajax_request" onclick="FILE_MANAGER.upload(this);" max="<?php echo $FM->getMaximumFileUploadSize();?>"><i class="fa fa-cloud-upload"></i> Upload</a></li>
                        <li><a href="javascript:void(0);" class="ajax_request" onclick="FILE_MANAGER.download(this);"><i class="fa fa-cloud-download"></i> Download</a></li>
                        <li><a href="javascript:void(0);" class="ajax_request" onclick="FILE_MANAGER.reload(this);"><i class="fa fa-refresh"></i> Reload</a></li>
                        
                        <!--<li><a href="?ac=ftp" class="ajax_request"><i class="fa fa-unlock"></i> FTP Login</a></li>-->
                        <li class="li-devision">&nbsp;</li>
                        <li><a href="javascript:void(0);" class="ajax_request" onclick="FILE_MANAGER.compress(this);"><i class="fa fa-compress"></i> Compress</a></li>
                        <li><a href="javascript:void(0);" class="ajax_request" onclick="FILE_MANAGER.uncompress(this);" ><i class="fa fa-expand"></i> Extract</a></li>
                        <li class="li-devision">&nbsp;</li>
                        <li><a href="javascript:void(0);" class="ajax_request" onclick="FILE_MANAGER.permission(this);" ><i class="fa fa-key"></i> Permissions</a></li>
                        <li><a href="javascript:void(0);" class="ajax_request" onclick="FILE_MANAGER.rename(this);" ><i class="fa fa-exchange"></i> Rename</a></li>
                        <li><a href="javascript:void(0);" class="ajax_request" onclick="FILE_MANAGER.edit(this);" ><i class="fa fa-edit"></i> Edit</a></li>
                    </ul>
                </div><!--/.nav-collapse -->
            </div>
        </nav>

        <!-- Begin page content -->
        <div class="container-fluid " id="file-manager-main-view">
             <div class="col-xs-2" id="file-treeview">
                
                <div class="left-list">
                       <?php 
                         $sHTML =  $FM->renderFolderView($aFiles,false);
						 
                         if(empty($sHTML))
                         {
                             $sHTML = $FM->renderFolderView($FM->scanDir(dirname($FM->getPath()),false),false);
                         }
echo $sHTML;
                       ?>
                </div>
            </div>
            <div class="col-xs-10" id="file-list">
                <div class="table-respsonsive">
                    
                   
                    <table class="table table-striped" id="table-list-detail">
                       <thead class="fixed-header">
                            <tr class="header">
                                <th style="width: 26px;text-align: center">
                                   <span><input type="checkbox" name="" class="check_all" autocomplete="off"></span>
                                </th>
                                <th style="width: 26px;text-align: center"></th>
                                <th><span>Name</span></th>
                                <th <span>Size</span></th>
                                <th ><span>Last Modified</span></th>
                                <th ><span>Permission</span></th>
                            </tr>
                        </thead>
                        <tbody>
                             <?php foreach($aFiles as $iKey => $aFile):?>
                                  <tr class="tr-file-holder" path="<?php echo $aFile['full_path']?>" type="<?php echo $aFile['type']?>">
                                    <td style="width: 26px;text-align: center">
                                        <?php if($aFile['title'] != ".."):?>
                                            <input type="checkbox" class="check_all_item" value="<?php echo $aFile['full_path']?>" name="check_all" autocomplete="off">
                                        <?php endif;?>
                                    </td>
                                    <td  style="width: 26px;text-align: center">
                                       <i class="file-item <?php echo $aFile['type']?>"></i>
                                    </td>
                                    <td class="file-title">
                                       <?php echo $aFile['title'];?>
                                    </td>
                                    <td class="file-size">
                                       <?php echo $aFile['file_size_view'];?>
                                    </td>
                                    <td class="file-time" >
                                       <?php echo $aFile['time_view'];?>
                                    </td>
                                    <td class="file-perm">
                                        <?php echo $aFile['perm'];?>
                                    </td>
                                   
                                  </tr>
                             <?php endforeach;?>
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
        <?php endif;?>
        <footer class="footer">
            <div class="" id="message-holder">
                <span class="current-path">Current Path: <span class="path" id="path_value"><?php echo $FM->getPath();?></span></span>
                 <i class="message-icon"></i><span class="return-message"></span>
            </div>
        </footer>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
         <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
         <script src="main.js"></script>
         
         <script>
            FILE_MANAGER = {
                currentPath: "",
                context: null,
                init: function() {
                    $("input.check_all").change(function() {
                        $("input.check_all_item").prop('checked', $(this).prop("checked"));
                    });
                    $('#file-treeview').on('click', '.tree-item.li-folder > a', function() {
                        var _parent = $(this).parent();
                        var _path = _parent.attr('path');
                        if (_parent.hasClass('collapsed')) {
                            _parent.find('ul').remove();
                            FILE_MANAGER.buildLeftTreeView(_parent, _path);
                            $('#path_value').html(_path);
                            _parent.removeClass('collapsed').addClass('expanded');
                        } else {
                            _parent.find('ul').slideUp({
                                duration: 500,
                                easing: null
                            });
                            _parent.removeClass('expanded').addClass('collapsed');
                        }
                        FILE_MANAGER.buildRightList('#table-list-detail tbody', _path);
                    });
                    $('#file-list .tr-file-holder').each(function(i,e){
                        var _l = $(e).attr('path');
                        $(e).attr('id','right_' + _l.hashCode());
                    });
                    $('.tree-items-holder li.tree-item').each(function(i,e){
                         var _l = $(e).attr('path');
                        $(e).attr('id','left_' + _l.hashCode());
                    });
                    $('#table-list-detail').on('dblclick', 'tr.tr-file-holder', function() {
                        var _self = $(this);
                        var _path = _self.attr('path');
                    
                        if (typeof(_path) != "undefined") {
                            if (_self.attr('type') == "folder") {
                                _self.addClass('wait');
                                $('#path_value').html(_path);
                                FILE_MANAGER.buildRightList('#table-list-detail tbody', _path);
                                if($('.tree-items-holder li').length <=0){
                                    FILE_MANAGER.buildLeftTreeView($('.tree-items-holder'), _path);    
                                }else{
                                    
                                }
                                
                                $("input.check_all").prop("checked", false);
                            } else {
                                //console.log('not implemented for file viewer');
                                _self.find('input[type=checkbox]').prop("checked","checked");
                                FILE_MANAGER.edit();
                            }
                        }
                    });
                    $('#table-list-detail').on('click', 'tr.tr-file-holder', function(e) {
                        if (!$(this).hasClass('active-context-menu')) {
                            $('.active-context-menu').removeClass('active-context-menu');
                            FILE_MANAGER.ContextMenu.remove();
                        }
                    });
                    $('#table-list-detail').on('contextmenu', 'tr.tr-file-holder', function(e) {
                        $('.active-context-menu input.check_all_item').prop('checked', false);
                        FILE_MANAGER.ContextMenu.init(this, e);
                        $('.active-context-menu').removeClass('active-context-menu');
                        $(this).addClass('active-context-menu');
                        e.preventDefault();
                        $(this).find("input.check_all_item").trigger('click');
                        return false;
                    });
            
                },
                newFolder: function(){
                    var _htmlBody = '<div><p>New Folder Name <span class="example">(ex: abc, folder1, folder2)</span></p><p><input type="text" name="file_name" value="" class="form-control" id="form_file_name"/></p></div>';
                    _htmlBody += '<div><p>Permission <span class="example">(ex: 0755, 0644)</span></p><p><input type="text" name="file_perm" value="0755" class="form-control" id="form_file_perms"/></p></div>';
                    _htmlBody += "<div style='margin-top:10px;'><p>New file will be created in: </p><p><input type='text' name=\"\" id=\"form_file_path\" value=\""+FILE_MANAGER.currentPath+"/\" class=\"form-control\"/></p></div>";
                    EBoostrapModal.show({
                        title: 'New Folder',
                        body: _htmlBody,
                        buttons: {
                            'process':{
                                title: 'Create New Folder',
                                class: 'btn-success',
                                action: function(e){
                                    var _file = $('#form_file_name').val();
                                    var _path = $('#form_file_path').val();
                                    var _perm = $('#form_file_perms').val();
                                    $.post('?ac=newFolder&ajax=1', { file: _file,path: _path,perm:_perm }, function( data ) {
                                        data = $.parseJSON(data);
                                        alert(data.message);
                                        if (data.status == "success") {
                                            //code
                                            EBoostrapModal.remove();
                                            FILE_MANAGER.reload();
                                        }
                                        $(e).html("Create New Folder");
                                    });
                                    $(e).html("Processing...");
                                }
                            },
                            'cancel':{
                                title:'Cancel',
                                class: 'btn-danger',
                                action: function(){
                                    EBoostrapModal.remove();
                                },
                                
                            }
                        }
                    });
                },
                newFile: function(){
                    var _htmlBody = '<div><p>New File Name <span class="example">(ex: file.txt, file.html, file.php)</span></p><p><input type="text" name="file_name" value="" class="form-control" id="form_file_name"/></p></div>';
                    _htmlBody += '<div><p>Permission <span class="example">(ex: 0755, 0644)</span></p><p><input type="text" name="file_perm" value="0755" class="form-control" id="form_file_perms"/></p></div>';
                    _htmlBody += "<div style='margin-top:10px;'><p>New file will be created in: </p><p><input type='text' name=\"\" id=\"form_file_path\" value=\""+FILE_MANAGER.currentPath+"/\" class=\"form-control\"/></p></div>";
                    EBoostrapModal.show({
                        title: 'New File',
                        body: _htmlBody,
                        buttons: {
                            'process':{
                                title: 'Create New File',
                                class: 'btn-success',
                                action: function(e){
                                    var _file = $('#form_file_name').val();
                                    var _path = $('#form_file_path').val();
                                    var _perm = $('#form_file_perms').val();
                                    $.post('?ac=newFile&ajax=1', { file: _file,path: _path,perm: _perm }, function( data ) {
                                        data = $.parseJSON(data);
                                        alert(data.message);
                                        if (data.status == "success") {
                                            //code
                                            EBoostrapModal.remove();
                                            FILE_MANAGER.reload();
                                        }
                                        $(e).html("Create New File");
                                    });
                                    $(e).html("Processing...");
                                }
                            },
                            'cancel':{
                                title:'Cancel',
                                class: 'btn-danger',
                                action: function(){
                                    EBoostrapModal.remove();
                                },
                                
                            }
                        }
                    });
                },
                upload: function(e){
                    var _htmlBody = '';
                    var size = $(e).attr('max');
                    _htmlBody += "<div style='margin-top:10px;'>You can upload file has maximum size " + size + "</div>";
                    _htmlBody += "<div style='margin-top:10px;' class='field_upload' ><iframe name=\"frame-upload\" style=\"display:none;\" id=\"frame-upload\"></iframe><form target=\"frame-upload\" action=\"?ac=upload\" method=\"post\" enctype=\"multipart/form-data\" id=\"form-upload\"><input type='file' name='file'/><p style=\"margin-top:10px;\">New file will be created in: </p><p><input type='text' name=\"path\" id=\"form_file_path\" value=\""+FILE_MANAGER.currentPath+"/\" class=\"form-control\"/></p></div>";
                    
                    EBoostrapModal.show({
                        title: 'Upload File',
                        //hasCloseButton: false,
                        body: _htmlBody,
                        buttons: {
                            'process':{
                                title: 'Process',
                                class: 'btn-success',
                                action: function(e){
                                     $("#frame-upload").load(
                                        function () {
                                            iframeContents = $("iframe")[0].contentDocument.body.innerHTML;
                                            iframeContents = $.parseJSON(iframeContents);
                                            if (iframeContents.status == 'success') {
                                                //code
                                                EBoostrapModal.remove();
                                                FILE_MANAGER.reload();
                                            }
                                        }
                                    );
                                    $('#form-upload').submit();
                                    $(e).html("Processing...");
                                }
                            },
                            'cancel':{
                                title:'Cancel',
                                class: 'btn-danger',
                                action: function(){
                                    EBoostrapModal.remove();
                                },
                                
                            }
                        }
                    });
                    FILE_MANAGER.ContextMenu.remove();
                },
                download: function(){
                    var _items = this.getSelectedItems(true);
                    if (_items.length <= 0) {
                        alert("No selected item to do this action");
                        return;
                    }
                    if (_items.length > 1) {
                        //code
                        alert("You can only choose 1 item to download. You should compress them before downloading.");
                        return;
                    }
                    var win = window.open("?download=" + _items[0], '_blank');
                        win.focus();
                },
                goTo: function(){
                    var _htmlBody = '';
                    
                    _htmlBody += "<div style='margin-top:10px;'><p>Folder Path</p><p><input type='text' name=\"\" id=\"compress_file_path\" value=\""+FILE_MANAGER.currentPath+"/\"/></p></div>";
                    EBoostrapModal.show({
                        title: 'Go To Folder',
                        //hasCloseButton: false,
                        body: _htmlBody,
                        buttons: {
                            'process':{
                                title: 'Process',
                                class: 'btn-success',
                                action: function(e){
                                    var _path = $('#compress_file_path').val();
                                    FILE_MANAGER.buildRightList('#table-list-detail tbody', _path);
                                    EBoostrapModal.remove();
                                }
                            },
                            'cancel':{
                                title:'Cancel',
                                class: 'btn-danger',
                                action: function(){
                                    EBoostrapModal.remove();
                                },
                                
                            }
                        }
                    });
                    
                    
                    FILE_MANAGER.ContextMenu.remove();
                },
                copy: function(moving) {
                    //copy
                    if (typeof(moving) == "undefined") {
                        //code
                        moving = 0;
                    }else{
                        moving = 1;
                    }
                    
                    var _items = this.getSelectedItems(true);
                    if (_items.length <= 0) {
                        alert("No selected item to do this action");
                        return;
                    }
                    var _htmlBody = '';
                    for(var i = 0; i < _items.length; i++)
                    {
                        _htmlBody+= "<li>" +_items[i]+"</li>";
                    }
                    _htmlBody = "<ul>" +_htmlBody+ "</ul>";
                    _htmlBody = "<div class='body-files'>" +_htmlBody+"</div>";
                    _htmlBody += "<div style='margin-top:10px;'><p>To folder</p><p><input type='text' name=\"\" id=\"compress_file_path\" value=\""+FILE_MANAGER.currentPath+"/\"/></p></div>";
                    EBoostrapModal.show({
                        title: ((moving == 1)?'Move':'Copy') + ' Files & Folders',
                        //hasCloseButton: false,
                        body: _htmlBody,
                        buttons: {
                            'process':{
                                title: 'Process',
                                class: 'btn-success',
                                action: function(e){
                                    var _file = $('#compress_file_path').val();
                                    $.post('?ac=copy&ajax=1&moving='+moving, { file: _file,path: FILE_MANAGER.currentPath, data:_items }, function( data ) {
                                        data = $.parseJSON(data);
                                        alert(data.message);
                                        if (data.status == "success") {
                                            //code
                                            EBoostrapModal.remove();
                                            FILE_MANAGER.reload();
                                        }
                                    });
                                    $(e).html("Processing...");
                                }
                            },
                            'cancel':{
                                title:'Cancel',
                                class: 'btn-danger',
                                action: function(){
                                    EBoostrapModal.remove();
                                },
                                
                            }
                        }
                    });
                    
                    
                    FILE_MANAGER.ContextMenu.remove();
                },
                paste: function() {
                    console.log('paste');
                    FILE_MANAGER.ContextMenu.remove();
                },
                remove: function(){
                    var _items = this.getSelectedItems(true);
                    if (_items.length <= 0) {
                        alert("No selected items to do this action");
                        return;
                    }
                    if (confirm("Are you sure to do this action?")) {
                        //code
                        var _htmlBody = '<div class="progress"><div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%"></div></div>';
                        EBoostrapModal.show({
                            title: 'Removing...',
                            body: _htmlBody,
                            buttons: {
                                
                            },
                            onDisplay: function(){
                                $.post('?ac=remove&ajax=1', {path: FILE_MANAGER.currentPath, data:_items }, function( data ) {
                                    data = $.parseJSON(data);
                                    alert(data.message);
                                    EBoostrapModal.remove();
                                    FILE_MANAGER.reload();
                                });
                            }
                        });
                    }
                    
                    FILE_MANAGER.ContextMenu.remove();
                },
                edit: function(){
                    var _items = this.getSelectedItems(true);
                    if (_items.length <= 0) {
                        alert("No selected item to do this action");
                        return;
                    }
                    if (_items.length > 1) {
                        //code
                        alert("You can only choose 1 item to edit");
                        return;
                    }
                    var link = _items[0];
                    var _hashLink = link.hashCode();
                    if ( $('tr#right_'+_hashLink).length > 0 && $('tr#right_'+_hashLink).attr('type') == "file") {
                        //code
                        var win = window.open("?editor=" + link, '_blank');
                        win.focus();
                    }
                    
                    
                    FILE_MANAGER.ContextMenu.remove();
                },
                show: function() {
                    FILE_MANAGER.ContextMenu.remove();
                },
                hide: function() {
                    FILE_MANAGER.ContextMenu.remove();
                },
                move: function() {
                    FILE_MANAGER.copy(1);
                    FILE_MANAGER.ContextMenu.remove();
                },
                reload: function(){
                    FILE_MANAGER.buildRightList('#table-list-detail tbody', FILE_MANAGER.currentPath);
                                    $("input.check_all").prop("checked", false);
                },
                rename: function(element){
                    var _items = this.getSelectedItems(true);
                    if (_items.length <= 0) {
                        alert("No selected item to do this action");
                        return;
                    }
                    if (_items.length > 1) {
                        //code
                        alert("You can only choose 1 item to rename");
                        return;
                    }
                    var _htmlBody = '';
                    for(var i = 0; i < _items.length; i++)
                    {
                        _htmlBody+= "<li>" +_items[i]+"</li>";
                    }
                    _htmlBody = "<ul>" +_htmlBody+ "</ul>";
                    _htmlBody = "<div class='body-files'>" +_htmlBody+"</div>";
                    _htmlBody += '<div><p>Rename to <span class="example">(Hello, nice, lorem)</span></p><p><input type="text" name="file_new_name" value="" class="form-control" id="file_new_name"/></p></div>';
                    EBoostrapModal.show({
                        title: 'Rename File or Folder',
                        //hasCloseButton: false,
                        body: _htmlBody,
                        buttons: {
                            'process':{
                                title: 'Process',
                                class: 'btn-success',
                                action: function(e){
                                    var _new_file_name = $('#file_new_name').val();
                                    $.post('?ac=rename&ajax=1', { name: _new_file_name,path: FILE_MANAGER.currentPath, data:_items }, function( data ) {
                                        data = $.parseJSON(data);
                                        alert(data.message);
                                        if (data.status == "success") {
                                            //code
                                            EBoostrapModal.remove();
                                            FILE_MANAGER.reload();
                                        }
                                        $(e).html("Process");
                                    });
                                    $(e).html("Processing...");
                                }
                            },
                            'cancel':{
                                title:'Cancel',
                                class: 'btn-danger',
                                action: function(){
                                    EBoostrapModal.remove();
                                },
                                
                            }
                        }
                    });
                    FILE_MANAGER.ContextMenu.remove();
                },
                permission: function(element){
                    var _items = this.getSelectedItems(true);
                    if (_items.length <= 0) {
                        alert("No selected item to do this action");
                        return;
                    }
                    var _htmlBody = '';
                    for(var i = 0; i < _items.length; i++)
                    {
                        _htmlBody+= "<li>" +_items[i]+"</li>";
                    }
                    _htmlBody = "<ul>" +_htmlBody+ "</ul>";
                    _htmlBody = "<div class='body-files'>" +_htmlBody+"</div>";
                    _htmlBody += '<div><p>Permission <span class="example">(ex: 0755, 0644)</span></p><p><input type="text" name="file_perm" value="0755" class="form-control" id="form_file_perms"/></p></div>';
                    EBoostrapModal.show({
                        title: 'Permissions Files & Folders',
                        //hasCloseButton: false,
                        body: _htmlBody,
                        buttons: {
                            'process':{
                                title: 'Process',
                                class: 'btn-success',
                                action: function(e){
                                    var _perm = $('#compress_file_path').val();
                                    $.post('?ac=permission&ajax=1', { perm: _perm,path: FILE_MANAGER.currentPath, data:_items }, function( data ) {
                                        data = $.parseJSON(data);
                                        alert(data.message);
                                        if (data.status == "success") {
                                            //code
                                            EBoostrapModal.remove();
                                            FILE_MANAGER.reload();
                                        }
                                    });
                                    $(e).html("Processing...");
                                }
                            },
                            'cancel':{
                                title:'Cancel',
                                class: 'btn-danger',
                                action: function(){
                                    EBoostrapModal.remove();
                                },
                                
                            }
                        }
                    });
                    FILE_MANAGER.ContextMenu.remove();
                },
                uncompress: function(element){
                    var _items = this.getSelectedItems(true);
                    if (_items.length <= 0) {
                        alert("No selected item to do this action");
                        return;
                    }
                    if (_items.length > 1) {
                        //code
                        alert("You can only choose 1 item to uncompress");
                        return;
                    }
                    var _htmlBody = '<div style="margin-bottom:15px;">File: <strong>'+_items+'</strong></div> <div class="progress"><div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%"></div></div>';
                    EBoostrapModal.show({
                        title: 'Uncompressing...',
                        body: _htmlBody,
                        buttons: {
                            
                        },
                        onDisplay: function(){
                            $.post('?ac=uncompress&ajax=1', {path: FILE_MANAGER.currentPath, data:_items }, function( data ) {
                                data = $.parseJSON(data);
                                alert(data.message);
                                EBoostrapModal.remove();
                                FILE_MANAGER.reload();
                            });
                        }
                    });
                    
                    FILE_MANAGER.ContextMenu.remove();
                },
                compress: function(element) {
                    var _items = this.getSelectedItems(true);
                    if (_items.length <= 0) {
                        alert("No selected items to do this action");
                        return;
                    }
                    var _htmlBody = '';
                    for(var i = 0; i < _items.length; i++)
                    {
                        _htmlBody+= "<li>" +_items[i]+"</li>";
                    }
                    _htmlBody = "<ul>" +_htmlBody+ "</ul>";
                    _htmlBody = "<div class='body-files'>" +_htmlBody+"</div>";
                    _htmlBody += "<div style='margin-top:10px;'><p>Enter the name of the compressed archive zip file</p><p><input type='text' name=\"\" id=\"compress_file_path\" value=\""+FILE_MANAGER.currentPath+"/\"/></p></div>";
                    EBoostrapModal.show({
                        title: 'Compress Files & Folders',
                        //hasCloseButton: false,
                        body: _htmlBody,
                        buttons: {
                            'process':{
                                title: 'Process',
                                class: 'btn-success',
                                action: function(e){
                                    var _file = $('#compress_file_path').val();
                                    $.post('?ac=compress&ajax=1', { file: _file,path: FILE_MANAGER.currentPath, data:_items }, function( data ) {
                                        data = $.parseJSON(data);
                                        alert(data.message);
                                        if (data.status == "success") {
                                            //code
                                            EBoostrapModal.remove();
                                            FILE_MANAGER.reload();
                                        }
                                    });
                                    $(e).html("Processing...");
                                }
                            },
                            'cancel':{
                                title:'Cancel',
                                class: 'btn-danger',
                                action: function(){
                                    EBoostrapModal.remove();
                                },
                                
                            }
                        }
                    });
                    
                    FILE_MANAGER.ContextMenu.remove();
                },
                saveFile: function(v){
                    var _vl = $('#simple-editor').val();
                    var _file = $('#simple-editor').attr('file');
                    var _htmlBody = '<div style="margin-bottom:15px;">Saving File : <strong>'+_file+'</strong></div> <div class="progress"><div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%"></div></div>';
                    EBoostrapModal.show({
                        title: 'Saving...',
                        body: _htmlBody,
                        buttons: {
                            
                        },
                        onDisplay: function(){
                            $.post('?ac=saveFile&ajax=1', {content: _vl, file:_file }, function( data ) {
                                data = $.parseJSON(data);
                                alert(data.message);
                                EBoostrapModal.remove();
                                
                            });
                        }
                    });
                },
                reloadFile:function(v){
                    if (confirm("Are you sure to reload this file? All of your changes will be lost.")) {
                        //code
                        window.location.href = window.location.href;
                    }
                },
                processing: function(v) {
                    if (v != false) {
                        $('#message-holder .return-message').html(v);
                        $('#message-holder').addClass('wait');
                    } else {
                        $('#message-holder .return-message').html(v);
                        $('#message-holder').removeClass('wait');
                    }
                },
               
                getSelectedItems: function(v) {
                    if (typeof(v) == "undefined") {
                        v = false;
                    }
                    var _items = $('input.check_all_item:checked');
                    if (v == true) {
                        var _values = [];
                        _items.each(function(i, e) {
                            _values.push($(e).val());
                        });
                        return _values;
                    }
                    return _items;
                },
                buildRightList: function(root, path) {
                    FILE_MANAGER.currentPath = path;
                    $.post('?ac=scan&ajax=1', {
                        path: path
                    }, function(data) {
                        data = $.parseJSON(data);
                        if (typeof(data) != "undefined") {
                            if (data.status == "error") {
                                //code
                                //alert(data.message);
                            }else{
                                var _html = FILE_MANAGER._buildRightTreeViewHTML(data.files);
                                $(root).html(_html);
                            }
                           
                        }
                        
                        $(root).removeClass('wait');
                        $(root).find('tr').removeClass('wait');
                    });
                },
                buildLeftTreeView: function(rootElement, path) {
            
                    $(rootElement).addClass('wait');
                    $.post('?ac=scan&ajax=1', {
                        path: path
                    }, function(data) {
                        data = $.parseJSON(data);
                        if (typeof(data) != "undefined") {
                             if (data.status == "error") {
                                //code
                                //alert(data.message);
                            }else{
                                var _html = FILE_MANAGER._buildLeftTreeViewHTML(data.files);
                                if (_html != "") {
                                    $(rootElement).append(_html);
                                } else {
                                    var _rightHTML = FILE_MANAGER._buildRightTreeViewHTML(data.files);
                                    $('#table-list-detail tbody').html(_rightHTML);
                                }
                
                                $(rootElement).find('ul:hidden').slideDown({
                                    duration: 500,
                                    easing: null
                                });
                            }
                            
                        }
                        $(rootElement).removeClass('wait');
                    });
                },
                _buildRightTreeViewHTML: function(data) {
                    var _html = [];
                    for (i = 0; i < data.length; i++) {
                        var _item = data[i];
                        var _tds = [];
                        if (_item.title == "..") {
                            _tds.push('<td style="width: 26px;text-align: center"></td>');
                        } else {
                            _tds.push('<td style="width: 26px;text-align: center"><input type="checkbox" class="check_all_item" value="' + _item.full_path + '" name="check_all" autocomplete="off"></td>');
                        }
            
                        _tds.push('<td  style="width: 26px;text-align: center"><i class="file-item ' + _item.type + '"></i></td>');
                        _tds.push('<td class="file-title">' + _item.title + '</td>');
                        _tds.push('<td class="file-size">' + _item.file_size_view + '</td>');
                        _tds.push('<td class="file-view">' + _item.time_view + '</td>');
                        _tds.push('<td class="file-perm">' + _item.perm + '</td>');
                        _html.push('<tr class="tr-file-holder" path="' + _item.full_path + '" type="' + _item.type + '" id="right_'+FILE_MANAGER.hash(_item.full_path)+'">' + _tds.join(' ') + '</tr>');
                    }
                    return _html.join(' ');
                },
                _buildLeftTreeViewHTML: function(data) {
                    var _html = [];
                    for (i = 0; i < data.length; i++) {
                        var _item = data[i];
                        if (_item.title == "." || _item.title == ".." || _item.type != "folder") {
                            continue;
                        }
                        _html.push('<li path="' + _item.full_path + '" class="tree-item li-' + _item.type + ' collapsed" id="letf_'+FILE_MANAGER.hash(_item.full_path)+'"><a href="javascript:void(0)" class="file-name">' + _item.title + '</a></li>');
                    }
                    if (_html.length > 0) {
                        return '<ul class="tree-items-holder">' + _html.join(' ') + '</ul>';
                    }
                    return "";
                },
                hash: function(url){
                    return url.hashCode();
                }
            };
            String.prototype.hashCode = function() {
                  var hash = 0, i, chr, len;
                  if (this.length === 0) return hash;
                  for (i = 0, len = this.length; i < len; i++) {
                    chr   = this.charCodeAt(i);
                    hash  = ((hash << 5) + hash) + chr;
                    hash |= 0; // Convert to 32bit integer
                  }
                  return hash;
            };
            
            FILE_MANAGER.ContextMenu = {
                context: null,
                menus: {
                    'edit': {
                        'name': 'Edit',
                        'icon': 'fa-edit',
                        'function': 'FILE_MANAGER.edit'
            
                    },
                    'copy': {
                        'name': 'Copy',
                        'icon': 'fa-copy',
                        'function': 'FILE_MANAGER.copy'
                    },
                    'paste': {
                        'name': 'Paste',
                        'icon': 'fa-paste',
                        'function': 'FILE_MANAGER.paste'
                    },
                    'move': {
                        'name': 'Move',
                        'icon': 'fa-crosshairs',
                        'function': 'FILE_MANAGER.move'
                    },
                    'compress': {
                        'name': 'Compress',
                        'icon': 'fa-compress',
                        'function': 'FILE_MANAGER.compress'
                    },
                    'uncompress': {
                        'name': 'Extract',
                        'icon': 'fa-expand',
                        'function': 'FILE_MANAGER.uncompress'
                    },
                    'delete':{
                        'name': 'Remove',
                        'icon': 'fa-remove',
                        'function': 'FILE_MANAGER.remove'
                    }
                },
                init: function(element, event) {
                    $('.file-context-menu').remove();
                    this.context = element;
                    $('body').append(this.renderHTML());
                    var _pos = $(element).offset();
                    var _x = event.pageX;
                    var _y = event.pageY;
                    var _h = $('.file-context-menu').height();
                    if (_y + _h > $(window).height()) {
                        _y = _y - _h - 10;
                    }
                    $('.file-context-menu').offset({
                        top: _y,
                        left: _x
                    }).show();
            
                },
                renderHTML: function() {
                    var _html = [];
                    for (var i in FILE_MANAGER.ContextMenu.menus) {
                        var _item = FILE_MANAGER.ContextMenu.menus[i];
                        _html.push('<li><a href="javascript:void(0)" onclick="' + _item.function+'()"><i class="fa ' + _item.icon + '"></i>  ' + _item.name + '</a></li>');
                    }
                    if (_html.length > 0) {
                        return '<div class="file-context-menu" style="display:none"><ul>' + _html.join(' ') + '</ul></div>';
                    }
                    return "";
                },
                remove: function() {
                    $('.file-context-menu').remove();
                }
            }
            
            EBoostrapModal = {
               defaultOpts:{
                  title: 'Notice',
                  body: '',
                  buttons: {},
                  callback: null,
                  onDisplay:null,
                  onClose: null,
                  ID: "EbootstrapModal",
                  hasCloseButton:true
               },
               show: function(opts){
                  this._init(opts);
                  this.remove();
                  var _html = [
                     this._header(),
                    this. _body(),
                    this. _footer(),
                  ];
                  $('body').append('<div id="'+this.defaultOpts.ID+'" class="modal fade" role="dialog"><div class="modal-dialog"></div></div>');
                  $('body').append('<div class="modal-backdrop fade in"></div>');
                  $('#' + this.defaultOpts.ID  + ' .modal-dialog').html('<div class="modal-content">'+_html.join('')+'</div>');
                  $('#'  + this.defaultOpts.ID).show(function(){
                     $(this).addClass('in');
                  });
                  
                  if (typeof(this.defaultOpts.onDisplay) == "function") {
                    //code
                    setTimeout(function(){
                        EBoostrapModal.defaultOpts.onDisplay();
                    },500);
                  }
                  //bind buttons
                  $('#EbootstrapModal .modal-footer .btn').each(function(i,e){
                     $(e).click(function(){
                        var _name = $(this).attr('name');
                        EBoostrapModal.defaultOpts.buttons[_name].action(e);
                     });
                  });
               },
               _init: function(opts){
                   this.defaultOpts = $.extend(this.defaultOpts, opts);
                   return this.defaultOpts;
               },
               _header: function()
               {
                
                  var _close_button =  '<button data-dismiss="modal" class="x close" type="button" onclick="EBoostrapModal.remove();"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>';
                  if (this.defaultOpts.hasCloseButton == false) {
                    //code
                    _close_button = "";
                  }
                  return '<div class="modal-header">' + 
                    _close_button +
                     '<h4 class="modal-title">' +this.defaultOpts.title + ' <small></small></h4>' +
                                        '</div>';
                                       
               },
               setContent: function(content){
                     $('#' + this.defaultOpts.ID + ' .modal-body').html(content);
               },
               _body: function(){
                   return '<div class="modal-body">'+this.defaultOpts.body+'</div>';
               },
               _footer: function(){
                    if (this.defaultOpts.buttons.length <=0) {
                        //code
                        return "";
                    }
                    var _buttons = "";
                    for( var i in this.defaultOpts.buttons){
                        var _temp = this.defaultOpts.buttons[i];
                        _buttons += "<a class='btn "+_temp.class+"' name='"+i+"'>" + _temp.title+"</a>";
                    }
                    
                   return '<div class="modal-footer">'+_buttons+'</div>';
               },
               remove: function(){
                     $('#' + this.defaultOpts.ID).remove();
                  $('.modal-backdrop').remove();
               }
            }
            $(document).ready(function(){
                FILE_MANAGER.init();
                var _tmp = '<?php echo str_replace("\\\",\\",$FM->getPath());?>';
                console.log(_tmp);
                FILE_MANAGER.currentPath = '<?php echo htmlentities($FM->getPath());?>';
            });
         </script>
    </body>
</html>

<?php
class FileManager
{
	private $_sCurrentPath;
	private $_mErrors;
	private $_bMoving = false;
    private $_sFileLocation = "";
	public function __construct()
	{
		$this->_sCurrentPath = dirname(__FILE__);
        if(defined('RESTRICT_ACCESS_CHILD_FOLDER_ONLY') && RESTRICT_ACCESS_CHILD_FOLDER_ONLY)
        {
            $this->_sFileLocation = dirname(__FILE__);
        }
        $sPath = isset($_POST['path']) ? $_POST['path'] : false;
        if(defined('RESTRICT_ACCESS_CHILD_FOLDER_ONLY') && RESTRICT_ACCESS_CHILD_FOLDER_ONLY && $sPath)
        {
            $sTmpPath = str_replace($this->_sFileLocation,'',$sPath);
            if($sTmpPath  == $sPath)
            {
                if($this->isAjaxCall())
                {
                    echo json_encode(array(
                        'status' => "error",
                        'message' => 'You cannot access'
                    ));exit;
                }
                else
                {
                    exit("INVALID PATH");
                }
            }
        }
	}
    public function makeURL()
    {
        
    }
	public function requireLogin()
	{
		$aAccount = array(
			'user' => isset($_SERVER['PHP_AUTH_USER'])?$_SERVER['PHP_AUTH_USER']:"",
			'password' => isset($_SERVER['PHP_AUTH_PW'])?$_SERVER['PHP_AUTH_PW']:"",
		);
		if($aAccount['user'] == AUTHENTICATE_USER && $aAccount['password'] == AUTHENTICATE_PASSWORD)
		{
			
		}
		else
		{
			header("WWW-Authenticate: Basic realm=\"AdminCP\"");
			header("HTTP/1.0 401 Unauthorized");
			exit("RESTRICTEA AREA!");
		}
	}
	public function processAjax()
	{
		//
		$sAction = isset($_GET['ac']) ? $_GET['ac'] : "404";
		$sAction = $sAction . "Action";
		if (!method_exists($this, $sAction)) {
			return "NOT FOUND";
		}
		return $this->$sAction();
	}
	public function upload()
	{
		$sPath = isset($_POST['path'])?$_POST['path'] .'/':"" ;
		$aReturn = array(
			'status' => 'error',
			'message' => 'Cannot upload file to this path '
		);
		if($sPath && isset($_FILES['file']))
		{
			if($_FILES["file"]['error'] ==  UPLOAD_ERR_OK)
			{
				if($_FILES["file"]["size"] > $this->getMaximumFileUploadSize(true))
				{
					$aReturn['message'] = 'Sorry, your file is too large';
				}
				$sNewUploadPath = $sPath. $_FILES['file']['name'];
				if(@move_uploaded_file($_FILES['file']['tmp_name'],$sNewUploadPath))
				{
					$aReturn = array(
						'status' => 'success',
						'message' => 'Uploaded file successfully '
					);
				}
			}
			
		}
		return $aReturn;
	}
	public function download($sFileName)
	{
		if(ini_get('zlib.output_compression')) 
		{
			ini_set('zlib.output_compression', 'Off'); 
		} 
		ob_clean();   
		ob_end_flush();
		$content_type = "application/force-download";
		header('Content-Description: File Transfer');
		header("Pragma: public"); // required
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private", false); // required for certain browsers
		header("Content-Transfer-Encoding: binary");
		header("Content-Type: " . $content_type);
		header("Content-Length: " . filesize($sFileName));
		header("Content-Disposition: attachment; filename=\"" . basename($sFileName) . "\";" );
		$fd = fopen ($file, "rb");
		if($fd)
		{
			while(!feof($fd)) {
				$buffer = fread($fd, 1024);
				echo $buffer;
			}
		}
		@fclose($fd);
		die();
	}
	public function scanAction()
	{
		$sPath = isset($_POST['path']) ? $_POST['path'] : false;
		$aFiles = array();
		if ($sPath) {
			$aFiles = $this->scanDir($sPath);
		}
		echo json_encode(array(
			'total' => count($aFiles) ,
			'files' => $aFiles,
		));
		exit;
	}
	public function renameAction()
	{
		$sNewName = isset($_POST['name']) ? $_POST['name'] : "";
		$aData = isset($_POST['data']) ? $_POST['data'] : array();
		$aReturn = array(
			'status' => 'success',
			'message' => 'Rename successfully',
		);
		if(!empty($sNewName) && count($aData))
		{
			$sOldFile = $aData[0];
			$sOldFileName = basename($sOldFile);
			$sNewFile = str_replace($sOldFileName,$sNewName,$sOldFile);
			//echo $sNewFile;
			@rename($sOldFile,$sNewFile);
			if(!file_exists($sNewFile))
			{
				$aReturn['status'] = 'error';
				$aReturn['message'] = 'Cannot rename file ' .$sOldFile;
			}
		}
		echo json_encode($aReturn);exit;
	}
	public function newFolderAction()
	{
		$sPath = isset($_POST['path']) ? $_POST['path'] : "";
		$sFileName = isset($_POST['file']) ? $_POST['file'] : "";
		$sPerm = isset($_POST['perm']) ? $_POST['perm'] : "0755";
		$aReturn = array(
			'status' => 'error',
			'message' => 'Cannot create new file '. $sFileName,
		);
		if (!empty($sFileName) && !empty($sPath))
		{
			$sFullPathFile = $sPath. $sFileName;
			if(is_dir($sFullPathFile))
			{
				$aReturn['message'] = 'The folder has already existed in ' . $sPath;
			}
			else
			{
				
				@mkdir($sFullPathFile,intval($sPerm, 8));
				if(is_dir($sFullPathFile))
				{
					$aReturn['message'] = 'The folder was created successfully';
					$aReturn['status'] = 'success';
				}
				else
				{
					$aReturn['message'] = 'Cannot create folder in '. $sPath;
				
				}
				
			}
		}
		echo json_encode($aReturn);
		exit;
	}
	public function permissionAction()
	{
		$sPerm = isset($_POST['perm']) ? $_POST['perm'] : "0755";
		$aData = isset($_POST['data']) ? $_POST['data'] : array();
		$aReturn = array(
			'status' => 'success',
			'message' => 'Changed permissions successfully',
		);
		if(count($aData))
		{
			foreach($aData as $mValue)
			{
				@chmod($mValue,intval($sPerm,8));
			}
		}
		echo json_encode($aReturn);exit;
	}
	public function newFileAction()
	{
		$sPath = isset($_POST['path']) ? $_POST['path'] : "";
		$sFileName = isset($_POST['file']) ? $_POST['file'] : "";
		$sPerm = isset($_POST['perm']) ? $_POST['perm'] : "";
		
		$aReturn = array(
			'status' => 'error',
			'message' => 'Cannot create new file '. $sFileName,
		);
		if (!empty($sFileName) && !empty($sPath))
		{
			$sFullPathFile = $sPath. $sFileName;
			if(file_exists($sFullPathFile))
			{
				$aReturn['message'] = 'The file has already existed in ' . $sPath;
			}
			else
			{
				$oFile = @fopen($sFullPathFile, 'w');
				if(!$oFile)
				{
					$aReturn['message'] = 'Cannot create the file';
				}
				else
				{
					@fwrite($oFile,"");
					$aReturn['message'] = 'File '. $sFileName . ' was created successfully';
					$aReturn['status'] = 'success';
				}
				@fclose($oFile);
				if(file_exists($sFullPathFile))
				{
					
					@chmod($sFullPathFile,intval($sPerm, 8));
				}
			}
		}
		echo json_encode($aReturn);
		exit;
	}
	public function uncompressAction()
	{
		$sPath = isset($_POST['path']) ? $_POST['path'] : "";
		$aData = isset($_POST['data']) ? $_POST['data'] : array();
		$aReturn = array(
			'status' => 'error',
			'message' => 'Cannot complete unzip process. Please try again',
		);
		if (count($aData) && !empty($sPath)) {
			$aData = $aData[0];
			if (!extension_loaded('zip')) {
				$aReturn = array(
					'message' => "Server is not supported ZIP method",
					'status' => 'error'
				);
			}
			else {
				
				if ($this->_unzip($aData, $sPath)) {
					$aReturn = array(
						'status' => 'success',
						'message' => 'Completed Unzip file',
					);
				}
				
			}
		}
		echo json_encode($aReturn);
		exit;
	}
	public function moveAction()
	{
		$this->copyAction();
		
	}
	public function removeAction()
	{
		$aData = isset($_POST['data']) ? $_POST['data'] : array();
		$aReturn = array(
			'status' => 'error',
			'message' => 'Cannot delete. Please try again',
		);
		if(count($aData))
		{
			foreach($aData as $mDeletedItem)
			{
				if(is_file($mDeletedItem))
				{
					@unlink($mDeletedItem);
				}
				else
				{
					$this->recurse_remove($mDeletedItem);
				}
			}
			$aReturn['status'] = 'success';
			$aReturn['message'] = 'Delete successfully';
		}
		echo json_encode($aReturn);
		exit;
	}
	public function recurse_remove($sSourcePath)
	{
		$dir = opendir($sSourcePath);
		while(false !== ( $file = readdir($dir)) ) {
			if (( $file != '.' ) && ( $file != '..' )) {
				if ( is_dir($sSourcePath . '/' . $file) ) {
					$this->recurse_remove($sSourcePath . '/' . $file);
				}
				else {
					@unlink($sSourcePath . '/' . $file);
				}
			}
		}
		closedir($dir);
		@rmdir($sSourcePath);
	} 
	public function copyAction()
	{
		$this->_bMoving = isset($_GET['moving']) ? $_GET['moving'] : 0;
		
		$sFolderPath = isset($_POST['file']) ? $_POST['file'] : "";
		$sPath = isset($_POST['path']) ? $_POST['path'] : "";
		$aData = isset($_POST['data']) ? $_POST['data'] : array();
		$aReturn = array(
			'status' => 'error',
			'message' => 'Cannot copy. Please try again',
		);
		if (count($aData) && !empty($sFolderPath))
		{
			if(!is_dir($sFolderPath))
			{
				@mkdir($sFolderPath,0755);
			}
			if(is_dir($sFolderPath))
			{
				foreach($aData as $mValue)
				{
					if(is_file($mValue))
					{
						@copy($mValue,$sFolderPath . '/' . basename($mValue));
						if($this->_bMoving)
						{
							@unlink($mValue);
						}
					}
					else
					{
						$this->recurse_copy($mValue, $sFolderPath);
					}
				}
				$aReturn = array(
					'status' => 'success',
					'message' => 'Completed '.(($this->_bMoving ==1 ) ?'move': 'copy').' process',
				);
			}
		}
		echo json_encode($aReturn);
		exit;
	}
	public function saveFileAction()
	{
		$sFile = isset($_POST['file'])?$_POST['file']:"";
		$sContent = isset($_POST['content'])?$_POST['content']:"";
		$aReturn = array(
			'status' => 'error',
			'message' => 'Cannot save file '. $sFile,
		);
		if(file_exists($sFile))
		{
			if(file_put_contents($sFile,$sContent))
			{
				
			}
			$aReturn = array(
				'status' => 'success',
				'message' => 'Saved file '. $sFile .' successfully',
			);
		}
		echo json_encode($aReturn);exit;
	}
	public function recurse_copy($sSourcePath,$sDestinationPath) {
		$sBaseSource = $sSourcePath;
		if(!is_dir($sDestinationPath))
		{
			@mkdir($sDestinationPath);
		}
		$sBaseFolderName = basename($sSourcePath);
		if(!is_dir($sDestinationPath.'/'. $sBaseFolderName))
		{
			@mkdir($sDestinationPath.'/'. $sBaseFolderName);
		}
		$sSourcePath = $sDestinationPath.'/'. $sBaseFolderName;
		$dir = opendir($sSourcePath);
		while(false !== ( $file = readdir($dir)) ) {
			if (( $file != '.' ) && ( $file != '..' )) {
				if ( is_dir($sSourcePath . '/' . $file) ) {
					$this->recurse_copy($sSourcePath . '/' . $file,$sDestinationPath . '/' . $file);
				}
				else {
					copy($sSourcePath . '/' . $file,$sDestinationPath . '/' . $file);
					if($this->_bMoving)
					{
						@unlink($sSourcePath . '/' . $file);
					}
				}
			}
		}
		closedir($dir);
		if($this->_bMoving)
		{
			
			@rmdir($sBaseSource);
		}
	} 
	public function compressAction()
	{
		$sFileName = isset($_POST['file']) ? $_POST['file'] : "";
		
		if(!is_writable(dirname(($sFileName))))
		{
			echo json_encode(array(
					'message' => "Cannot create zip here",
					'status' => 'error'
				));
			return;
		}
		$aData = isset($_POST['data']) ? $_POST['data'] : array();
		if (count($aData) && !empty($sFileName)) {
			if (!extension_loaded('zip')) {
				echo json_encode(array(
					'message' => "Server is not supported ZIP method",
					'status' => 'error'
				));
			}
			else {
				$aReturn = array(
					'status' => 'error',
					'message' => 'Cannot complete zip process. Please try again',
				);
				if ($this->_zip($aData, $sFileName, true)) {
					$aReturn = array(
						'status' => 'success',
						'message' => 'Completed zip process',
					);
				}
				echo json_encode($aReturn);
			}
		}
		exit;
	}
	public function _unzip($sSourcePath, $sDestinationPath)
	{
		$zip = new ZipArchive();
		$res = $zip->open($sSourcePath);
		if ($res == true) {
			$zip->extractTo($sDestinationPath);
			$zip->close();
			return true;
		}
		return false;
	}
	public function _zip($aSources, $sDestinationPath, $bIncludeDir = false)
	{
		if (!extension_loaded('zip')) {
			return false;
		}
		if (file_exists($sDestinationPath)) {
			@unlink($sDestinationPath);
		}
		$fp = fopen($sDestinationPath, 'w');
		if ($fp === FALSE){
			return false;
		}
		fclose($fp);
		$zip = new ZipArchive();
		if (!$zip->open($sDestinationPath, ZIPARCHIVE::CREATE)) {
			return false;
		}
		foreach ($aSources as $source) {
			$source = str_replace('\\', '/', realpath($source));
			if (is_dir($source) === true) {
				$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source) , RecursiveIteratorIterator::SELF_FIRST);
				if ($bIncludeDir) {
					$arr = explode("/", $source);
					$maindir = $arr[count($arr) - 1];
					$source = "";
					for ($i = 0; $i < count($arr) - 1; $i++) {
						$source.= '/' . $arr[$i];
					}
					$source = substr($source, 1);
					$zip->addEmptyDir($maindir);
				}
				foreach ($files as $file) {
					$file = str_replace('\\', '/', $file);
					if (in_array(substr($file, strrpos($file, '/') + 1) , array(
						'.',
						'..'
					))) continue;
					$file = realpath($file);
					if (is_dir($file) === true) {
						$zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
					}
					else if (is_file($file) === true) {
						$zip->addFromString(str_replace($source . '/', '', $file) , file_get_contents($file));
					}
				}
			}
			else if (is_file($source) === true) {
				$zip->addFromString(basename($source) , file_get_contents($source));
			}
		}
		return $zip->close();
	}
	public function isAjaxCall()
	{
		$bIsAjax = isset($_GET['ajax']) ? true : false;
		return $bIsAjax;
	}
	public function scanDir($sPath = null, $bUpdatePath = true)
	{
		if ($sPath == null) {
			$sPath = $this->_sCurrentPath;
		}
		else {
            if($bUpdatePath)
            {
                $this->setPath($sPath);    
            }
		}
		$mData = scandir($sPath, SCANDIR_SORT_ASCENDING);
		$aResult = array(
			'folder' => array() ,
			'file' => array() ,
		);
		if (count($mData)) {
			foreach ($mData as $key => $hFile)
			{
				if ($hFile == ".") {
					continue;
				}
				$aFile = array(
					'title' => $hFile,
					'path' => $sPath,
					'type' => 'folder',
					'time' => 'N/A',
					'size' => 'N/A',
					'perm' => '',
					'full_path' => $sPath ,
					'file_size_view' => 'N/A',
					'time_view' => 'N/A',
				);
				switch ($hFile) {
				case ".":
					break;

				case "..";
					$aFile['path'] = dirname($aFile['path']);
					$aFile['full_path'] = $aFile['path'];
				break;

				default:
					$sFullPath = $sPath . DIRECTORY_SEPARATOR . $hFile;
					if (is_dir($sFullPath)) {
						$aFile['type'] = "folder";
					}
					else {
						$aFile['type'] = "file";
						$aFile['time'] = filectime($sFullPath);
						$aFile['size'] = @filesize($sFullPath);
						$aFile['file_size_view'] = $this->convertFileSize($aFile['size']);
						$aFile['time_view'] = $this->getTime($aFile['time']);
					}
					$aFile['full_path'] = $sFullPath;
					break;
				}
				$aFile['perm'] = $this->getPermision($aFile['full_path']);
				$aResult[$aFile['type']][] = $aFile;
			}
		}
		return array_merge($aResult['folder'], $aResult['file']);
	}
	public function renderFolderView($aFiles, $bEcho = false)
	{
		$sLi = "";
		foreach ($aFiles as $iKey => $aFile) {
			if ($aFile['type'] != "folder" || $aFile['title'] == "." || $aFile['title'] == "..") continue;
			$sLi.= '<li class="tree-item li-' . $aFile['type'] . ' collapsed" path="' . $aFile['full_path'] . '">
								 <a class="file-name" href="javascript:void(0)">' . $aFile['title'] . '</a>
							   
							</li>';
		}
        if(!empty($sLi))
        {
            $sLi = '<ul class="tree-items-holder">' . $sLi . '</ul>';    
        }
		if ($bEcho) {
			echo $sLi;
			return;
		}
		return $sLi;
	}
	public function getPermision($mPath)
	{
		return substr(sprintf('%o', fileperms($mPath)) , -4);
	}
	public function convertFileSize($bytes, $sType = "")
	{
		$label = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB' );
        for( $i = 0; $bytes >= 1024 && $i < ( count( $label ) -1 ); $bytes /= 1024, $i++ );
        return( round( $bytes, 2 ) . " " . $label[$i] );
	}
	public function getTime($iTime = null)
	{
		if (!$iTime || $iTime == "N/A") {
			return "N/A";
		}
		return date("Y M d H:i:s", (int)$iTime);
	}
	public function setPath($sPath = "")
	{
		$this->_sCurrentPath = $sPath;
		return $this;
	}
	public function getPath()
	{
		return $this->_sCurrentPath;
	}
	public function setError($sCode, $sMessage)
	{
		$this->_mErrors[] = array(
			'code' => $sCode,
			'message' => $sMessage
		);
		return $this;
	}
	public function isError()
	{
		return (count($this->_mErrors) > 0) ? true : false;
	}
	public function getErrors()
	{
		return $this->_mErrors;
	}
	public function readFileContent($sFile)
	{
		if(is_dir($sFile))
		{
			
			return "Cannot read folder";
		}
		return file_get_contents($sFile);
	}
	function getMaximumFileUploadSize($bByteReturn = false)  
    {  
        $mValue =  min(ini_get('post_max_size'), ini_get('post_max_size'));
		if($bByteReturn)
		{
			$mValue = ((int)$mValue) * 1024;
		}
		return $mValue;
    }
}
?>
<?php
function d($mInfo, $bVarDump = false)
{
	$bCliOrAjax = (PHP_SAPI == 'cli');
	(!$bCliOrAjax ? print '<pre style="text-align:left; padding-left:15px;">' : false);
	($bVarDump ? var_dump($mInfo) : print_r($mInfo));
	(!$bCliOrAjax ? print '</pre>' : false);
}
?>
