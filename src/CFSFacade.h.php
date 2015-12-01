<?php
include_once("CFile.h.php");


/**
 * ������������ ����� ������� � �������� ������ � �������� ��������
 */
// ============================================================================
class CFSFacade
// ============================================================================
{
   function CFSFacade() {}

   /**
    * 
    * ���������� ���������� � ��������� �������� ($srcFolder)
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
    * ��������� ����� ���� 2 ������
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
    * ���������� ������������� ��������� ������ � ����������� � ������ ��������
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
               UI_ln($pathStart.$file);
               $type = "file";
               if (is_dir($fullFName.'/'.$file)) {
                  $type = "dir";
               };
               
               if($recursive && ($type == "dir"))
               {
                  $f = CFSFacade::getDirContents(
                            $srcFolder, 
                            CFSFacade::concat($dirName, $file), 
                            $pathStart.$file."/", $recursive
                       );
               }
               else {
                  $fsize        = filesize($fullFName.'/'.$file);
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
    * ���������� ���������� � ��������� �������� ($treeInforAr) � ����� ($destFolder)
    * @param string $destFolder
    * @param array(CFile) $treeInforAr
    * @throws EDestDirIsNotEmpty
    * @return void
    */
   static function writeFileTree($destFolder, $treeF) {
      //UI_ln("CFSFacade getFileTree()");
      
      //��������, ���� �� ����� � �����-����������. ���� ���� - ��������� ����� 
      $fs = new CFileSystem("CFileSystem");
      $ar = $fs->getDirContents($destFolder, true);
      UI_echo('CFSFacade writeFileTree() $destFolder', $destFolder);
      UI_echo('CFSFacade writeFileTree() count($ar)', count($ar));
      
      if (count($ar) > 0) {
         throw new Exception("EDestDirIsNotEmpty-$destFolder");
         return;
      };
      
      $folderF = $treeF;
      //$ar = array(); //����
      //UI_echo('CFSFacade writeFileTree() $folderF->getChildrenNumber()', $folderF->getChildrenNumber());
      
      for ($i=0; $i<$folderF->getChildrenNumber(); $i++) {
         $f = $folderF->getChild($i);
         
         
         $fName       = $f->getName();
         //$fullFName   = $srcFolder . "/" . $fName;
         //$fsize       = $f->getSize();
         $type        = $f->getType();
         UI_echo('$fName', $fName);
         UI_echo('$type', $type);
         
         $fullFName   = $destFolder . "/" . $fName;
         if ($type == "dir") {
            mkdir($fullFName);
            CFSFacade::writeFileTree($destFolder, $f);
         }
         else {
            $f1 = fopen($fullFName, "a");
            fclose($f1);
         };
                  
         /*
         
         //UI_echo('$fullFName', $fullFName);
         */
      }
      
   }
   
   
};
?>