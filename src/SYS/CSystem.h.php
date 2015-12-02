<?php
/**
 * getTree
 * layer бизнес-логики SYS
 */


namespace getTree\SYS;
use getTree\SYS\CRunHandler;

include_once("CRunHandler.h.php");
/**
 * Класс-координатор домена (бизнес-процессов)
 * 
 * @class CSystem
 */
class CSystem
{
   /**
    * constructor 
    */
   function __construct() {}

   /**
    * 
    * Обработка события "run" 
    * @param string $srcFolder
    * @param string $destFolder
    */
   function run($srcFolder, $destFolder) {
      $handler = new CRunHandler();
      
      $handler->run($srcFolder, $destFolder);
   }

};
?>