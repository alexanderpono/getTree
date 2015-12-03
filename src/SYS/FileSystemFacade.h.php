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
     * @param string $localPath - путь относительно папки ($srcFolder)
     * @param bool $recursive
     * @return File - иерархия объектов File в памяти
     */
    static function getDirContents($srcFolder, $dirName, $localPath, $recursive=false)
    {
      $dirFile = new File($dirName, "", File::TDIR, $dirName);
      
      $fullFName = FileSystemFacade::concat($srcFolder, $localPath);
      $dirHandle = @opendir($fullFName);
      if (!$dirHandle) {
         return $dirFile;
      };
      
      while(false !== ($fName = readdir($dirHandle))) 
      {
         if (FileSystemFacade::isEmptyFileName($fName)) {
            continue;
         };
         
         $fullPath      = $fullFName.'/'.$fName;
         $newLocalPath  = $localPath.$fName;
         
         if($recursive && is_dir($fullPath)) {
            $f = FileSystemFacade::getDirContents(
                    $srcFolder, $fName, $newLocalPath."/", $recursive
                 );
         }
         else {
            $f = FileSystemFacade::createFile(
                    $fName, $newLocalPath, $fullPath
                 );
         };
         $dirFile->addChild($f);
      };
      closedir($dirHandle);
      
      return $dirFile;
    }
    
    // ============================================================================
    /**
     * Определяет, пустое ли имя у файла ($fName)
     * @param string $fName
     * @return bool
     */
    private static function isEmptyFileName($fName) {
       return (($fName == '.') || ($fName == '..'));
    }
   
    // ============================================================================
    /**
     * Создает структуру данных с информацией о файле $fName
     * @param string $fName
     * @param string $localPath
     * @param string $fullPath
     */
    private static function createFile($fName, $localPath, $fullPath) {
        $fsize = filesize($fullPath);
        $fsize = sprintf("%u", $fsize);
        $f     = new File($fName, $fsize, File::TFILE, $localPath);
        return $f;
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