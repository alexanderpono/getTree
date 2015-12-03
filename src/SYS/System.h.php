<?php
/**
 * getTree
 * layer бизнес-логики SYS
 */


namespace getTree\SYS;
use getTree\SYS\RunHandler;

include_once("RunHandler.h.php");
/**
 * Класс-координатор домена (бизнес-процессов)
 * 
 * @class System
 */
class System
{
   // ============================================================================
   /**
    * constructor 
    */
   function __construct() {}

   // ============================================================================
   /**
    * 
    * Обработка события "run" 
    * @param string $srcFolder
    * @param string $destFolder
    */
   function run($srcFolder, $destFolder) {
      $handler = new RunHandler();
      
      $handler->run($srcFolder, $destFolder);
   }

};
?>