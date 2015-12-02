<?php

//namespace getTree\SYS;

include_once("CRunHandler.h.php");
/**
 * Класс-координатор домена (бизнес-процессов)
 * 
 * @class CSystem
 */
// ============================================================================
class CSystem
// ============================================================================
{
   function CSystem() {}

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