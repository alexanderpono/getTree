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
        $rootF->e();
        $rootF->updateName();
        
        $rootF->e();
        
        CFSFacade::writeFileTree($destFolder, $rootF);
        /*
        foreach ($treeInforAr as $key => &$f) {
            $name    = $f->getName();
            $type    = $f->getType();
            $sizeStr = $f->getSizeStr();
            $path    = $f->getPath();

            //добавляем информацию о длине файла до расширения имени
            $nameAr           = explode(".", $name);
            $nameSegmentIndex = count($nameAr) - 2;

            if (count($nameAr) == 1) {
                //имя файла - без расширения
                $nameSegmentIndex = 0;
            };
         
            $nameSegment = $nameAr[$nameSegmentIndex];
            $nameSegment = $nameSegment . "(" . $sizeStr . ")";
            $nameAr[$nameSegmentIndex] = $nameSegment;
         
            $name2 = implode(".", $nameAr);
            UI_echo('CRunHandler run() $f', "$name $type $sizeStr $name2");
         
            $path2 = str_replace($name, $name2, $path);
         
            $f->setName($name2);
            $f->setPath($path2);
        };
      */
      /*
      //UI_echo('CRunHandler run() count($treeInforAr)', count($treeInforAr));
      for ($i=0; $i<count($treeInforAr); $i++) {
         $f = $treeInforAr[$i];
         
         $name    = $f->getName();
         $type    = $f->getType();
         $sizeStr = $f->getSizeStr();
         $path    = $f->getPath();
         
         //if ($type == "dir") {
         //   continue;
         //};
         
         //добавляем информацию о длине файла до расширения имени
         $nameAr           = explode(".", $name);
         $nameSegmentIndex = count($nameAr) - 2;

         if (count($nameAr) == 1) {
            //имя файла - без расширения
            $nameSegmentIndex = 0;
         };
         
         $nameSegment = $nameAr[$nameSegmentIndex];
         $nameSegment = $nameSegment . "(" . $sizeStr . ")";
         $nameAr[$nameSegmentIndex] = $nameSegment;
         
         $name2 = implode(".", $nameAr);
         //UI_echo('CRunHandler run() $f', "$name $type $sizeStr $name2");
         
         $path2 = str_replace($name, $name2, $path);
         
         $f->setName($name2);
         $f->setPath($path2);
         $treeInforAr[$i] = $f;
      };
      
      CFSFacade::writeFileTree($destFolder, $treeInforAr);
      */
   }

};
?>