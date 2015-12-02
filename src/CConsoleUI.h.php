<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace getTree\UI;

include_once("CUI.h.php");

/**
 * Основной класс слоя интерфейса с пользователем
 */
class CConsoleUI extends CUI
{
   /**
    * Создает экземпляр объекта
    * 
    */
   function CConsoleUI() {
      
   }
   
   /**
    * 
    * Получает значения параметров запуска
    * @param string $srcFolder OUT
    * @param string $destFolder OUT
    */
   function getParams(&$srcFolder, &$destFolder) {
      global $argv;
      
      $srcFolder    = "";
      $destFolder   = "";
      
      parse_str(implode('&', array_slice($argv, 1)), $_GET);      
      //UI_echo('getParams() $argv[1]', $argv[1]);
      //UI_echo('getParams() $argv[2]', $argv[2]);
      if (array_key_exists("i", $_GET)) {
         $srcFolder    = $_GET["i"];
      };
      if (array_key_exists("o", $_GET)) {
         $destFolder   = $_GET["o"];
      };
      //UI_echo('$srcFolder', $srcFolder);
      //UI_echo('$destFolder', $destFolder);
   }
   
};
?>