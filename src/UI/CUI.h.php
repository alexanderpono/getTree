<?php
/**
 * getTree
 */

namespace getTree\UI;
use getTree\SYS\CSystem;
use getTree\SYS\CFSFacade;

include_once("src/SYS/CSystem.h.php");
/**
 * Основной класс слоя интерфейса с пользователем
 */
class CUI
{
   // ============================================================================
   /**
    * constructor 
    */
   function __construct() {}
   
   // ============================================================================
   /**
    * 
    * Обработка события "go" 
    */
    function go() {
        $this->getParams($srcFolder, $destFolder);
   
        $SYS = new CSystem();
        try {
            $ver = CFSFacade::readFile("ver.txt");
            UI_ln("******************************************************************");
            UI_ln("getTree ver $ver. Сохраняет информацию о файловой структуре диска");
            $SYS->run($srcFolder, $destFolder);   
            UI_ln("$srcFolder -> $destFolder");
        }
        catch (\Exception $e) {
           $this->processException($e->getMessage(), $srcFolder, $destFolder);
        }
        
      
    }

   // ============================================================================
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
   
   
   // ============================================================================
   /**
    * 
    * Обрабатывает исключения
    * @param Exception $e
    * @param string $srcFolder 
    * @param string $destFolder 
    */
   function processException($msg, $srcFolder, $destFolder) {
      $codeS = $msg;
      $codeAr = explode("-", $codeS);
      $code = $codeAr[0];
      $param = $codeAr[1];
      $messageAr = array(
         "EDestDirIsNotEmpty" => "папка-назначение '$param' - непустая",
         "ENoParameters" => "не заданы параметры",
         "ENoIParameter" => "не задан параметр i",
         "ENoOParameter" => "не задан параметр o",
         "ENoInputFolder" => "не найдена входная папка '$srcFolder'",
         "EErrorCreatingOutputFolder" => "ошибка создания выходной папки '$destFolder'",
         "EOutputFolderWriteError" => "папка-назначение '$destFolder' недоступна для записи",
         "EOutputFolderNotZero" => "в папке-назначение '$destFolder' найдены файлы ненулевой длины",
         "EVersionReadError" => "ошибка чтения файла ver.txt"
      );
      
      $showCallInfoAr = array(
         "ENoParameters" => true,
         "ENoIParameter" => true,
         "ENoOParameter" => true
      );
      $message = $messageAr[$code];
      
      UI_ln("Аварийный останов: $message");
      if (array_key_exists($code, $showCallInfoAr)) {
         UI_ln("Вызов программы: getTree i=<путь к сканируемой папке> o=<путь к папке хранения информации>");
         UI_ln("Пример вызова: ./getTree i=../CL o=../CLInfo");
      };
   
   }
   
};
?>