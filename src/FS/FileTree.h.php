<?php
/**
 * getTree
 * Дерево файлов на диске
 */

namespace getTree\FS;

use getTree\SYS\File;

include_once("src/SYS/File.h.php");

/**
 * Интерфейсный класс доступа к функциям работы с файловой системой
 */
class FileTree
{

    private $rootDirPath;
    private $destFolder;
    
    // ============================================================================
    /**
     * constructor 
     */
    function __construct() {
       $this->rootDirPath = "";
    }
   
    // ============================================================================
    /**
     * 
     * возвращает информацию о структуре каталога ($srcFolder)
     * @return File
     */
    function read($rootDirPath) {
        $this->rootDirPath = $rootDirPath;
        $rootF = $this->getDirContents("", "");
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
    private function concat($dirName, $fName) {
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
     * @return File - иерархия объектов File в памяти
     */
    private function getDirContents($dirName, $localPath)
    {
      $dirFile = File::createDir($dirName, $dirName);
      
      $fullFName = $this->concat($this->rootDirPath, $localPath);
      $dirHandle = @opendir($fullFName);
      if (!$dirHandle)
         return $dirFile;
      
      while(false !== ($fName = readdir($dirHandle))) {
         if ($this->isEmptyFileName($fName))
            continue;
         
         $fullPath      = $fullFName.'/'.$fName;
         $newLocalPath  = $localPath.$fName;
         
         if(is_dir($fullPath))
            $f = $this->getDirContents($fName, $newLocalPath."/");
         else
            $f = $this->createFile($fName, $newLocalPath, $fullPath);
            
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
    private function isEmptyFileName($fName) {
       return (($fName == '.') || ($fName == '..'));
    }
   
    // ============================================================================
    /**
     * Создает структуру данных с информацией о файле $fName
     * @param string $fName
     * @param string $localPath
     * @param string $fullPath
     */
    private function createFile($fName, $localPath, $fullPath) {
        $fsize = filesize($fullPath);
        $fsize = sprintf("%u", $fsize);
        $f     = File::createFile($fName, $fsize, $localPath);
        return $f;
    }
    
    
    // ============================================================================
    /**
     * записывает информацию о структуре каталога в папку ($destFolder)
     * @return File
     */
    public function write($destFolder, $rootF) {
        $this->destFolder = $destFolder;
        $this->writeFileTree("", $rootF);
        return $rootF;
    }
   
    
    // ============================================================================
   /**
    * 
    * Записывает информацию о структуре каталога ($treeInforAr) в папку ($destFolder)
    * @param string $destFolder
    * @param string $subFolder
    * @param File $treeF
    * @return void
    */
   private function writeFileTree($subFolder, $treeF) {
      $folderF = $treeF;
      
      for ($i=0; $i<$folderF->getChildrenNumber(); $i++) {
         $f          = $folderF->getChild($i);
         $fName      = $f->getName();
         $fullFName  = $this->concat($this->destFolder, $this->concat($subFolder, $fName));
         if ($f->isDir()) {
            mkdir($fullFName);
            $this->writeFileTree($this->concat($subFolder, $fName), $f);
         }
         else {
            $f1 = fopen($fullFName, "a");
            fclose($f1);
         };
                  
      }
      
   }
   
};
?>