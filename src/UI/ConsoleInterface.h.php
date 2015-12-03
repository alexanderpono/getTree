<?php
/**
 * getTree
 */

namespace getTree\UI;

include_once("WebInterface.h.php");

/**
 * Основной класс слоя интерфейса с пользователем (консольное приложение)
 */
class ConsoleInterface extends WebInterface
{
   // ============================================================================
   /**
    * constructor 
    */
   function __construct() {}
      
   // ============================================================================
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
      if (array_key_exists("i", $_GET)) {
         $srcFolder    = $_GET["i"];
      };
      if (array_key_exists("o", $_GET)) {
         $destFolder   = $_GET["o"];
      };
   }
   
};
?>