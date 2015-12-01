<?php
// ============================================================================
//    PURPOSE:             Класс CT_file умеет загружать текст из файла
//
//    FUNCTIONAL AREA:     T (Text)
//    NAME:                CT_file.h 
//    VERSION:             ver.txt
//    AUTHORS:             Sasha
//    DESIGN REFERENCE:    
//    MODIFICATION:        
// ============================================================================
// =================================================================== INCLUDES
// =================================================================== SYNOPSIS
// ================================================================== CONSTANTS
// ================================================================== VARIABLES
// ================================================================== FUNCTIONS
class CT_file extends CDebugClass
{
   var $m_html;
   var $m_path;

   // ============================================================================
   function CT_file($myName)
   // ============================================================================
   {
      $this->CDebugClass($myName);
      $this->m_html = "";
      $this->m_path = "";
   }

   // ============================================================================
   function fileExists($fName, $path = "")
   // ============================================================================
   {
      $fullPath = ($path == "") ? ($fName) : ($path . "/" . $fName);

      //UI_echo('CT_file $fullPath', $fullPath);
      $exist    = file_exists($fullPath);
      
      return $exist;
   }

   // ============================================================================
   function load($fName, $path = "")
   // ============================================================================
   //ожидает путь к файлу (fName) от корневого каталога
   //до каталога, где находится файл с шаблоном (без слэш на конце);
   {
      $this->m_path = $path;

      $fullPath = ($path == "") ? ($fName) : ($path . "/" . $fName);
      $inFile = fopen($fullPath, "r");
      //UI_echo('CFW_Template $inFile', $inFile);
      $s = "";
      if ($inFile == "")
      {
         $this->m_html = $s;
         return $s;
      };
      
      //UI_echo('CFW_Template $inFile1', $inFile);
      while (!feof($inFile)) {
        $s .= fgets($inFile, 16384);
        //echo $buffer;
      }
      //$s = fread($inFile, 65536);
      fclose($inFile);

      //UI_echo('CFW_Template $inFile2', $inFile);
      $this->m_html = $s;

      return $s;
   }

   // ============================================================================
   function correctPicturesPaths($html, $path)
   // ============================================================================
   {
      global $_prjPicturesRoot;
      //UI_echo('CFW_Template _prjPicturesRoot', $_prjPicturesRoot);
      $directoryPath = ($path == "") ? ("") : ($_prjPicturesRoot . $path . "/");
      //теперь надо заменить все ссылки на картинки: <img src="
      //на модифицированные ссылки <img src="


      $html1 = str_replace("<img src=\"", "<img  src=\"$directoryPath", $html);
      return $html1;
   }

   // ============================================================================
   function replace($text, $id, $insertValue, $startShar="@")
   // ============================================================================
   {
      $s = str_replace("$startShar$id;", $insertValue, $text);
      return $s;
   }

   // ============================================================================
   function replace2($text, $id, $insertValue)
   // ============================================================================
   {
      $s = str_replace($id, $insertValue, $text);
      return $s;
   }

   // ============================================================================
   function save($fName, $s, $path = "")
   // ============================================================================
   //ожидает путь к файлу (fName) от корневого каталога
   //до каталога, где находится файл с шаблоном (без слэш на конце);
   {
      $this->m_path = $path;

      $fullPath = ($path == "") ? ($fName) : ($path . "/" . $fName);
      $outFile = fopen($fullPath, "wt");
      //UI_echo('CFW_Template $inFile', $inFile);
      //$s = "";
      if ($outFile == "")
      {
         //$this->m_html = $s;
         return false;
      };
      
      //UI_echo('CFW_Template $inFile1', $inFile);
      //while (!feof($inFile)) {
      //  $s .= fgets($inFile, 16384);
        //echo $buffer;
      //}

      //$s = fread($inFile, 65536);

      fputs($outFile, $s);
      fclose($outFile);

      //UI_echo('CFW_Template $inFile2', $inFile);
      //$this->m_html = $s;

      return $s;
   }


};
?>