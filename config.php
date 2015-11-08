<?php
/* File related definitions */
define("DS", "\\");
define("PATH", realpath(__DIR__) . DS);

/* Include files */
require(PATH . 'libs'. DS . 'database.php');
require(PATH . 'libs' . DS . 'antiexploit.php');
require(PATH . 'libs' . DS . 'user.php');
require(PATH . 'libs' . DS . 'products.php');
require(PATH . 'libs' . DS . 'news.php');

/* Testing related definitions */
define("DEBUG", true);


/* mysql related definitions */
define("HOST", "localhost");
define("DBNAME", "fitshop");
define("DBUSER", "root");
define("DBPASS", "");

?>
