<?php
/**
 * getTree
 * layer бизнес-логики SYS
 */

namespace getTree\SYS;

use getTree\SYS\File;

include_once("File.h.php");

/**
 * Интерфейсный класс доступа к функциям работы с файловой системой
 */
class FileSystemFacade
{
    // ============================================================================
    /**
     * constructor 
     */
    function __construct() {}
   
    // ============================================================================
    /**
     * 
     * возвращает информацию о структуре каталога ($srcFolder)
     * @param string $srcFolder
     * @return array(File)
     */
    static function getFileTree($srcFolder) {
        //UI_ln("FileSystemFacade getFileTree()");
        $rootF = FileSystemFacade::getDirContents($srcFolder, "", "", true);
        return $rootF;
    }
   
    // ============================================================================
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
   
    // ============================================================================
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

      $dirF = new File($dirName, "", "dir", $dirName);
      
      $fullFName = FileSystemFacade::concat($srcFolder, $pathStart);
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
                  $f = FileSystemFacade::getDirContents(
                            $srcFolder,
                            $file, 
                            //FileSystemFacade::concat($dirName, $file), 
                            $pathStart.$file."/", $recursive
                       );
               }
               else {
                  $fsize        = filesize($fullFName.'/'.$file);
                  $fsize = sprintf("%u", $fsize);
                  $f            = new File($file, $fsize, $type, $pathStart.$file);
               };
               $dirF->addChild($f);
            }
         }
         closedir($handle);
      };
      
      return $dirF;
    }
   
   
   
   
   
    // ============================================================================
   /**
    * 
    * Записывают информацию о структуре каталога ($treeInforAr) в папку ($destFolder)
    * @param string $destFolder
    * @param string $subFolder
    * @param File $treeF
    * @return void
    */
   static function writeFileTree($destFolder, $subFolder, $treeF) {
      $folderF = $treeF;
      
      for ($i=0; $i<$folderF->getChildrenNumber(); $i++) {
         $f = $folderF->getChild($i);
         
         
         $fName       = $f->getName();
         $type        = $f->getType();
         
         $fullFName = FileSystemFacade::concat($destFolder, FileSystemFacade::concat($subFolder, $fName));
         if ($type == "dir") {
            mkdir($fullFName);
            FileSystemFacade::writeFileTree($destFolder, FileSystemFacade::concat($subFolder, $fName), $f);
         }
         else {
            $f1 = fopen($fullFName, "a");
            fclose($f1);
         };
                  
      }
      
   }
   
    // ============================================================================
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