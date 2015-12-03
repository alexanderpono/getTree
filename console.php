<?php 
/**
 * cli-интерфейс запуска программы getTree
 * 
 *  Usage: getTree -i=<srcFolder> -o=<destFolder>
 *  
 *  srcFolder - путь к папке, которую нужно просканировать
 *  destFolder - путь к папке, где нужно создать информацию о папке (srcFolder)
 */

use getTree\UI\ConsoleInterface;

error_reporting(E_ALL);
global $_io;
global $_libPath;
$_libPath = "lib";

include_once('lib/lib.h.php');
include_once("src/UI/ConsoleInterface.h.php");
$_IO_br = "\n";

$_io = new CIO();


$UI = new ConsoleInterface();
$UI->go();

?>
