<?php
/**
 * getTree
 * layer бизнес-логики SYS
 */

namespace getTree\SYS;
use \getTree\SYS\FileSystemFacade;
include_once("src/SYS/FileSystemFacade.h.php");

/**
 * Обработчик системного события "run" 
 */
class RunHandler
{
   // ============================================================================
   /**
    * constructor 
    */
   function __construct() {}
   
   // ============================================================================
   /**
    * 
    * Обработка системного события "run" 
    * @param string $srcFolder
    * @param string $destFolder
    */
    function run($srcFolder, $destFolder) {
        $rootF = FileSystemFacade::readFileTree($srcFolder);
        $rootF->calcSize();
        $rootF->updateName();

        $this->checkNoParameters($srcFolder, $destFolder);
        $this->checkNoIParameter($srcFolder);
        $this->checkNoOParameter($destFolder);
        $this->checkNoInputFolder($srcFolder);
        $this->checkNoOutputFolder($destFolder);
        $this->checkWriteOutputFolder($destFolder);
        $this->checkOutputFolderNotZeroSize($destFolder);
        $this->clearOutputFolderIfNotEmpty($destFolder);
        
        FileSystemFacade::writeFileTree($destFolder, $rootF);
    }
    
    /**
     * 2. проверка, что ни один параметр не задан 
     * @param string $srcFolder
     * @param string $destFolder
     * @throws \Exception
     */
    private function checkNoParameters($srcFolder, $destFolder) {
        if (($srcFolder == "") && ($destFolder == "")) {
           throw new \Exception("ENoParameters-");
        };
    }

    /**
     * 3. проверка, задан ли параметр i
     * @param string $srcFolder
     * @throws \Exception
     */
    private function checkNoIParameter($srcFolder) {
        if ($srcFolder == "") {
           throw new \Exception("ENoIParameter-");
        };
    }
        
    /**
     * 4. проверка, задан ли параметр o
     * @param string $destFolder
     * @throws \Exception
     */
    private function checkNoOParameter($destFolder) {
        if ($destFolder == "") {
           throw new \Exception("ENoOParameter-");
        };
    }

    /**
     * 5. проверка, существует ли входная папка
     * @param string $srcFolder
     * @throws \Exception
     */
    private function checkNoInputFolder($srcFolder) {
        if ((!file_exists($srcFolder)) || (!is_dir($srcFolder))) {
           throw new \Exception("ENoInputFolder-");
        };
    }

    /**
     * 6. Проверка существования папки-назначения. Если папки 
     * назначения нет, то попытка создать ее. Если не создалась,
     * то исключение EErrorCreatingOutputFolder
     * @param string $destFolder
     * @throws \Exception
     */
    private function checkNoOutputFolder($destFolder) {
        if (!file_exists($destFolder)) {
            mkdir($destFolder);
            if (!file_exists($destFolder)) {
               throw new \Exception("EErrorCreatingOutputFolder-");
            };
        };
    }
        
    /**
     * 7. Проверка папки-назначения на запись. Если не доступна на запись, 
     * то исключение EOutputFolderWriteError
     * @param string $destFolder
     * @throws \Exception
     */
    private function checkWriteOutputFolder($destFolder) {
        if (!is_writeable($destFolder)) {
            throw new \Exception("EOutputFolderWriteError-");
        };
    }
        
    /**
     * 8. Если папка-назначение содержит файлы, то узнать их суммарный размер. 
     * Если он более 0, то исключение EOutputFolderNotZero. 
     * @param string $destFolder
     * @throws \Exception
     */
    private function checkOutputFolderNotZeroSize($destFolder) {
        $destF = FileSystemFacade::readFileTree($destFolder);
        $destF->calcSize();
        $allSize = $destF->getSize();
        if ($allSize > 0) {
           throw new \Exception("EOutputFolderNotZero-$destFolder");
           return;
        };
    }

    /**
     * 9. Если папка-назначение непустая, то ее очистка. Если возникла ошибка, 
     * то исключение EOutputFolderWriteError
     * @param string $destFolder
     * @throws \Exception
     */
    private function clearOutputFolderIfNotEmpty($destFolder) {
        if (!FileSystemFacade::isDirEmpty($destFolder)) {
            FileSystemFacade::clearDir($destFolder);
            if (!FileSystemFacade::isDirEmpty($destFolder)) {
               throw new \Exception("EOutputFolderWriteError-$destFolder");
            };
        };
    }
        
    
    
};
?>