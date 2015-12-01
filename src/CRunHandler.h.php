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

        //проверяю, есть ли файлы в папке-назначении. Если есть - аварийный выход 
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