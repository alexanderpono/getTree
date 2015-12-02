<?php

//namespace getTree\SYS;

use getTree\SYS\CFile;

include_once("CFile.h.php");
include_once("lib/CT_file/CT_file.h.php");

/**
 * Интерфейсный класс доступа к функциям работы с файловой системой
 */
// ============================================================================
class CFSFacade
// ============================================================================
{
   function CFSFacade() {}

   /**
    * 
    * возвращает информацию о структуре каталога ($srcFolder)
    * @param string $srcFolder
    * @return array(CFile)
    */
    static function getFileTree($srcFolder) {
        //UI_ln("CFSFacade getFileTree()");
        $rootF = CFSFacade::getDirContents($srcFolder, "", "", true);
        return $rootF;
   }
   
   /**
    * 
    * Соединяет через слэш 2 строки
    * @param string $dirName
    * @param string $fName
    * @return string
    */
   static function concat($dirName, $fName) {
        $slash = "";
        if (($dirName != "") && ($fName != "")) {
           $slash = "/";
        };       
        $s = $dirName . $slash . $fName;
        return $s;     
    }
   
   /**
    * 
    * Возвращает иерархическую структуру данных с информацией о файлах каталога
    * @param string $srcFolder
    * @param string $dirName
    * @param string $pathStart
    * @param bool $recursive
    * @return string
    */
   static function getDirContents($srcFolder, $dirName, $pathStart, $recursive=false)
   {
      $result = array();

      $dirF = new CFile($dirName, "", "dir", $dirName);
      
      $fullFName = CFSFacade::concat($srcFolder, $pathStart);
      if($handle = @opendir($fullFName))
      {
         while(false !== ($file = readdir($handle)))
         {
            if($file != '.' && $file != '..')
            {
               $type = "file";
               if (is_dir($fullFName.'/'.$file)) {
                  $type = "dir";
               };
               
               if($recursive && ($type == "dir"))
               {
                  //UI_ln("getDirContents() dir '$file'");
                  $f = CFSFacade::getDirContents(
                            $srcFolder,
                            $file, 
                            //CFSFacade::concat($dirName, $file), 
                            $pathStart.$file."/", $recursive
                       );
               }
               else {
                  $fsize        = filesize($fullFName.'/'.$file);
                  $fsize = sprintf("%u", $fsize);
                  $f            = new CFile($file, $fsize, $type, $pathStart.$file);
               };
               $dirF->addChild($f);
            }
         }
         closedir($handle);
      };
      
      return $dirF;
   }
   
   
   
   
   
   /**
    * 
    * Записывают информацию о структуре каталога ($treeInforAr) в папку ($destFolder)
    * @param string $destFolder
    * @param string $subFolder
    * @param CFile $treeF
    * @return void
    */
   static function writeFileTree($destFolder, $subFolder, $treeF) {
      $folderF = $treeF;
      
      for ($i=0; $i<$folderF->getChildrenNumber(); $i++) {
         $f = $folderF->getChild($i);
         
         
         $fName       = $f->getName();
         $type        = $f->getType();
         
         $fullFName = CFSFacade::concat($destFolder, CFSFacade::concat($subFolder, $fName));
         if ($type == "dir") {
            mkdir($fullFName);
            CFSFacade::writeFileTree($destFolder, CFSFacade::concat($subFolder, $fName), $f);
         }
         else {
            $f1 = fopen($fullFName, "a");
            fclose($f1);
         };
                  
      }
      
   }
   
   /**
    * 
    * Считывает содержимое файла ($fName)
    * @param string $fName
    * @return string
    */
   static function readFile($fName) {
      if (!file_exists($fName)) {
         throw new \Exception("EVersionReadError-"); 
      };
      $f = new \CT_file("f");
      $s = $f->load($fName);

      return $s;
   }
   
   
};
?>