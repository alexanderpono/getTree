<?php
include_once("CSystem.h.php");
/**
 * Основной класс слоя интерфейса с пользователем
 */
// ============================================================================
class CUI
// ============================================================================
{
    function CUI() {}
   
   /**
    * 
    * Обработка события "go" 
    */
    function go() {
        $this->getParams($srcFolder, $destFolder);
   
        $SYS = new CSystem();
        try {
            $SYS->run($srcFolder, $destFolder);   
        }
        catch (Exception $e) {
           $this->processException($e, $srcFolder, $destFolder);
        }
      
      
    }

   /**
    * 
    * Получает значения параметров запуска
    * @param string $srcFolder OUT
    * @param string $destFolder OUT
    */
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
   
   
   /**
    * 
    * Обрабатывает исключения
    * @param Exception $e
    * @param string $srcFolder 
    * @param string $destFolder 
    */
   function processException($e, $srcFolder, $destFolder) {
      $codeS = $e->getMessage();
      $codeAr = explode("-", $codeS);
      $code = $codeAr[0];
      $param = $codeAr[1];
      $messageAr = array("EDestDirIsNotEmpty" => "Папка-назначение '$param' - непустая");
      //if ()
      $message = $messageAr[$code];
      
      UI_ln("Ошибка $code: $message");
   }
   
};
?>