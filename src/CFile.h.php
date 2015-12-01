<?php

/**
 * Класс, который соответствует файлу
 */
// ============================================================================
class CFile
// ============================================================================
{
   private $m_name;
   private $m_size;
   private $m_type;
   private $m_path;
   private $m_children;
   
   function CFile($name="", $size="", $type="", $path="") {
      $this->m_name = $name;
      $this->m_size = $size;
      $this->m_type = $type;
      $this->m_path = $path;
      $this->m_children = array();
   }
   
   function setName($name) {$this->m_name = $name;}
   function setSize($size) {$this->m_size = $size;}
   function setType($type) {$this->m_type = $type;}
   function setPath($path) {$this->m_path = $path;}
   function addChild(&$f) {array_push($this->m_children, $f);}
   
   function getName() {return $this->m_name;}
   function getSize() {return $this->m_size;}
   function getType() {return $this->m_type;}
   function getPath() {return $this->m_path;}
   function getChildrenNumber() {return count($this->m_children);}
   function getChild($i) {return $this->m_children[$i];}
   
   /**
    * 
    * Возвращает информацию о размере файла в соответствующих единицах
    * @return string
    */
   function getSizeStr() {
      $sizeStr = "{$this->m_size}b";
      $kb = 1024;
      if ($this->m_size >= $kb) {
         $sizeStr = $this->m_size / $kb;
         $sizeStr = round($sizeStr * 10) / 10;
         $sizeStr = $sizeStr . "Kb";
      };
      
      $mb = $kb * 1024;
      if ($this->m_size >= $mb) {
         $sizeStr = $this->m_size / $mb;
         $sizeStr = round($sizeStr * 10) / 10;
         $sizeStr = $sizeStr . "Mb";
      };
      
      $gb = $mb * 1024;
      if ($this->m_size >= $gb) {
         $sizeStr = $this->m_size / $gb;
         $sizeStr = round($sizeStr * 10) / 10;
         $sizeStr = $sizeStr . "Gb";
      };
      
      return $sizeStr;
   }
   
   function e() {
      UI_ln("$this->m_name, $this->m_size, $this->m_type, $this->m_path");
      for ($i=0; $i<count($this->m_children); $i++) {
         $node = $this->m_children[$i];
         $node->e();
      }
      
   }
   
   /**
    * 
    * Возвращает размер узла в байтах (папки или файла)
    * @return void
    */
   function calcSize() {
      if ($this->m_type != "dir") {
         return;// $this->getSize();
      };
      
      $mySize = 0;
      for ($i=0; $i<count($this->m_children); $i++) {
         $node = $this->m_children[$i];
         if ($node->getType() == "dir") {
            $node->calcSize();
         };
         $mySize += $node->getSize();
      };
      
      $this->setSize($mySize);
   }

   /**
    * 
    * Заносит в имя файла информацию о его размере
    * @return void
    */
   function updateName() {
      $name    = $this->getName();
      $type    = $this->getType();
      $sizeStr = $this->getSizeStr();
      $path    = $this->getPath();

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
      //UI_echo('CFile updateName() $f', "$name $type $sizeStr $name2");
   
      $path2 = str_replace($name, $name2, $path);
   
      $this->setName($name2);
      
      for ($i=0; $i<count($this->m_children); $i++) {
         $node = $this->m_children[$i];
         $node->updateName();
      };
      
   }

};
?>