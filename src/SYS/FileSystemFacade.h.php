<?php
/**
 * getTree
 * layer бизнес-логики SYS
 */

namespace getTree\SYS;

use getTree\SYS\File;
use getTree\FS\FileTree;

include_once("File.h.php");
include_once("src/FS/FileTree.h.php");

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
     * @return File
     */
    /*
   */
    static function readFileTree($srcFolder) {
        $fileTree = new FileTree();
        $rootFile = $fileTree->read($srcFolder);
        return $rootFile;
    }
    
    // ============================================================================
   /**
    * Записывает информацию о структуре каталога ($treeF) в папку ($destFolder)
    * @param string $destFolder
    * @param File $treeF
    */
   static function writeFileTree($destFolder, $rootFile) {
      $fileTree = new FileTree();
      $fileTree->write($destFolder, $rootFile);
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
   
   /**
    * Определяет, является ли папка пустой
    * @param string $dirPath
    * @return bool
    */
   static function isDirEmpty($dirPath) {
        $fs = new \CFileSystem("CFileSystem");
        $ar = $fs->getDirContents($dirPath, true);
        if (count($ar) > 0)
           return false; //папка непустая
        else 
           return true;
   }
   
   /**
    * Очищает папку
    * @param string $dirPath
    */
   static function clearDir($dirPath) {
        $fs = new \CFileSystem("CFileSystem");
        $fs->clearDir($dirPath, true);
   }
   
};
?>