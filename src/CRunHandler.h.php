<?php
include_once("CFSFacade.h.php");

/**
 * Обработчик системного события "run" 
 */
// ============================================================================
class CRunHandler
// ============================================================================
{
   function CRunHandler() {}

   /**
    * 
    * Обработка системного события "run" 
    * @param string $srcFolder
    * @param string $destFolder
    */
    function run($srcFolder, $destFolder) {
        //UI_ln("CRunHandler run()");

        $rootF = CFSFacade::getFileTree($srcFolder);
        $rootF->calcSize();
        //$rootF->e();
        $rootF->updateName();
        //$rootF->e();

        //2. проверка, что ни один параметр не задан 
        if (($srcFolder == "") && ($destFolder == "")) {
           throw new Exception("ENoParameters-");
        };
        
        //3. проверка, задан ли параметр i
        if ($srcFolder == "") {
           throw new Exception("ENoIParameter-");
        };
        
        //4. проверка, задан ли параметр o
        if ($destFolder == "") {
           throw new Exception("ENoOParameter-");
        };
        
        //5. проверка, существует ли входная папка
        if ((!file_exists($srcFolder)) || (!is_dir($srcFolder))) {
           throw new Exception("ENoInputFolder-");
        };
        
        //6. Проверка существования папки-назначения. Если папки 
        //назначения нет, то попытка создать ее. Если не создалась, 
        //то исключение EErrorCreatingOutputFolder
        if (!file_exists($destFolder)) {
            mkdir($destFolder);
            if (!file_exists($destFolder)) {
               throw new Exception("EErrorCreatingOutputFolder-");
            };
        };
        
        //7. Проверка папки-назначения на запись. Если не доступна на запись, 
        //то исключение EOutputFolderWriteError
        if (!is_writeable($destFolder)) {
            throw new Exception("EOutputFolderWriteError-");
        };
        
        //8. Если папка-назначение содержит файлы, то узнать их суммарный размер. 
        //Если он более 0, то исключение EOutputFolderNotZero. 
        $destF = CFSFacade::getFileTree($destFolder);
        //$destF->e();
        $destF->calcSize();
        $allSize = $destF->getSize();
        //UI_echo('$allSize', $allSize);
        if ($allSize > 0) {
           throw new Exception("EOutputFolderNotZero-$destFolder");
           return;
        };
        
        //9. Если папка-назначение непустая, то ее очистка. Если возникла ошибка, 
        //то исключение EOutputFolderWriteError
        $fs = new CFileSystem("CFileSystem");
        $ar = $fs->getDirContents($destFolder, true);
        if (count($ar) > 0) {
            $fs->clearDir($destFolder, true);
            $ar = $fs->getDirContents($destFolder, true);
            if (count($ar) > 0) {
               throw new Exception("EOutputFolderWriteError-$destFolder");
            };
        };
        
        $fs = new CFileSystem("CFileSystem");
        $ar = $fs->getDirContents($destFolder, true);
      
        if (count($ar) > 0) {
           throw new Exception("EDestDirIsNotEmpty-$destFolder");
           return;
        };
        
        CFSFacade::writeFileTree($destFolder, "", $rootF);
   }

};
?>