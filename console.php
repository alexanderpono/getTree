<?php 
/**
 * cli-��������� ������� ��������� getTree
 * 
 *  Usage: getTree -i=<srcFolder> -o=<destFolder>
 *  
 *  srcFolder - ���� � �����, ������� ����� ��������������
 *  destFolder - ���� � �����, ��� ����� ������� ���������� � ����� (srcFolder)
 */


error_reporting(E_ALL);
global $_io;
global $_libPath;
$_libPath = "lib";

include_once('lib/lib.h.php');
include_once("src/CConsoleUI.h.php");
$_IO_br = "\n";

$_io = new CIO();


$UI = new CConsoleUI();
$UI->go();

?>
