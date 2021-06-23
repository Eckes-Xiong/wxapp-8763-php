<?php
return array(
    'db' => array(
         'host' => 'localhost'
        ,'user' => 'root'
        ,'pass' => 't2dw4qdEPBUDZVaF'
        ,'charset' => 'utf8'
        ,'dbname' => 'myDb_eckes_top'
    ),

    'app' => array(
        'default_plateform' => 'admin',
        'default_method' => 'showPage',

        'default_controller' => 'index',
        'default_tb' => 'index',
        'default_view' => 'index_body',

        'login_controller'=> 'login',
        'login_tb' => 'login',
        'login_view' => 'login_body',

        'html_tb' => 'page_base_html',
        'html_head_view' => 'base/base_head',
        'html_foot_view' => 'base/base_foot',

        //front view
        'index_head' => 'base/index_head',
        'index_nav' => 'base/index_nav',
        'index_foot' => 'base/index_foot'
    ),

    'prefix'=>'e_',
    'tb' => array(
        'users_info' => 'users_info'//网站主体导航
    ),

    'release' => array(
        'info' => 'release_info'
        ,'product' => 'release_product'

        ,'view_head' => 'release_head'
        ,'view_main' => 'release_main'
        ,'view_foot' => 'release_foot'
    )
);