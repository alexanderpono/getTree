<?php
include_once("CRunHandler.h.php");
/**
 * �����-����������� ������ (������-���������)
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
    * ��������� ������� "run" 
    * @param string $srcFolder
    * @param string $destFolder
    */
   function run($srcFolder, $destFolder) {
      $handler = new CRunHandler();
      
      $handler->run($srcFolder, $destFolder);
   }

};
?>