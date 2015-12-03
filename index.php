<?php 
/**
 * web-интерфейс запуска программы getTree
 * 
 *  Usage: index.php?i=<srcFolder>&o=<destFolder>
 *  
 *  srcFolder - путь к папке, которую нужно просканировать
 *  destFolder - путь к папке, где нужно создать информацию о папке (srcFolder)
 */

use getTree\UI\WebInterface;

error_reporting(E_ALL);
global $_io;
global $_libPath;
$_libPath = "lib";

include_once('lib/lib.h.php');
include_once("src/UI/WebInterface.h.php");

$_io = new CIO();


//UI_ln("getTree.h.php");

$UI = new WebInterface();
$UI->go();
   
?>
