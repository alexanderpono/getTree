<?php 
/**
 * web-��������� ������� ��������� getTree
 * 
 *  Usage: index.php?i=<srcFolder>&o=<destFolder>
 *  
 *  srcFolder - ���� � �����, ������� ����� ��������������
 *  destFolder - ���� � �����, ��� ����� ������� ���������� � ����� (srcFolder)
 */

error_reporting(E_ALL);
global $_io;
global $_libPath;
$_libPath = "lib";

include_once('lib/lib.h.php');
include_once("src/CUI.h.php");

$_io = new CIO();


UI_ln("getTree.h.php");

$UI = new CUI();
$UI->go();
   
/*   
getParams($srcFolder, $destFolder);
UI_echo('$srcFolder', $srcFolder);
UI_echo('$destFolder', $destFolder);

$SYS = new CSystem();
$SYS->run($srcFolder, $destFolder);
*/


/**
 * 
 * �������� �������� ���������� �������
 * @param string $srcFolder OUT
 * @param string $destFolder OUT
 */
/*
function getParams(&$srcFolder, &$destFolder) {
   $srcFolder    = "";
   $destFolder   = "";
   
   if (array_key_exists("i", $_GET)) {
      $srcFolder    = $_GET["i"];
   };
   if (array_key_exists("o", $_GET)) {
      $destFolder   = $_GET["o"];
   };
}
*/

?>
