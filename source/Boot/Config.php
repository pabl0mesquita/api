<?php

################
### DATABASE ###
################
define("CONF_DB_HOST", "localhost");
define("CONF_DB_NAME", "fullstackphp");
define("CONF_DB_USER", "root");
define("CONF_DB_PASSWD", "");


############
### VIEW ###
############
define("CONF_VIEW_PATH", "/themes");
define("CONF_VIEW_EXT", "php");
define("CONF_VIEW_THEME_WEB", "Web");



################
### PASSWORD ###
################
define("CONF_PASSWD_MIN_LEN", 8);
define("CONF_PASSWD_MAX_LEN", 40);
define("CONF_PASSWD_ALGO", PASSWORD_DEFAULT);
define("CONF_PASSWD_OPTION", ["cost" => 10]);

define("CONF_UPLOAD_DIR", "storage");