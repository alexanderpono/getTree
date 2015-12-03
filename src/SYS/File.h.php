<?php
/**
 * getTree
 * layer бизнес-логики SYS
 */



namespace getTree\SYS;


/**
 * Класс, который соответствует файлу
 */
class File
{
   /**
    * Имя файла
    * @var string 
    */
   private $name;
   
   /**
    * Размер файла в байтах
    * @var int 
    */
   private $size;
   
   const TDIR = "dir";   
   const TFILE = "file";
       
   /**
    * Тип файла: "file", "dir"
    * @var string 
    */
   private $type;
   
   /**
    * Путь к файлу(папке) внутри корневой папки
    * @var string 
    */
   private $path;
   
   /**
    * Список дочерних файлов (папок)
    * @var array(CFile) 
    */
   private $children;
   
   // ============================================================================
   /**
    * 
    * Конструктор
    * @param string $name
    * @param string $size
    * @param string $type
    * @param string $path
    */
   function __construct($name="", $size="", $type="", $path="") {
      $this->name = $name;
      $this->size = $size;
      $this->type = $type;
      $this->path = $path;
      $this->children = array();
   }
   
   /**
    * Альтернативный конструктор - создает объект "папка"
    * @param string $fName
    * @param string $localPath
    * @return File
    */
   static public function createDir($fName, $localPath) {
      $f = new File($fName, 0, File::TDIR, $localPath); 
      return $f;
   }
   
   /**
    * Альтернативный конструктор - создает объект "файл"
    * @param string $fName
    * @param int $fsize
    * @param string $localPath
    * @return File
    */
   static public function createFile($fName, $fsize, $localPath) {
      $f = new File($fName, $fsize, File::TFILE, $localPath); 
      return $f;
   }
   
   // ============================================================================
   /**
    * set
    * @param string $name
    */
   function setName($name) {$this->name = $name;}
   
   // ============================================================================
   /**
    * set
    * @param int $size
    */
   function setSize($size) {$this->size = $size;}
   
   // ============================================================================
   /**
    * set
    * @param string $type - "file", "dir"
    */
   function setType($type) {$this->type = $type;}
   
   // ============================================================================
   /**
    * set - установка пути к файлу
    * @param string $path
    */
   function setPath($path) {$this->path = $path;}
   
   // ============================================================================
   /**
    * Добавление элемента к списку дочерних узлов
    * @param string $path
    */
   function addChild(&$f) {array_push($this->children, $f);}
   
   // ============================================================================
   /**
    * get name
    * @return string 
    */
   function getName() {return $this->name;}
   
   // ============================================================================
   /**
    * get size
    * @return int 
    */
   function getSize() {return $this->size;}
   
   // ============================================================================
   /**
    * get type
    * @return string 
    */
   function getType() {return $this->type;}
   
   // ============================================================================
   /**
    * get path to file
    * @return string 
    */
   function getPath() {return $this->path;}
   
   /**
    * get - количество дочерних элементов в папке
    * @return int 
    */
   function getChildrenNumber() {return count($this->children);}
   
   // ============================================================================
   /**
    * get - дочерний узел с порядковым номером ($i) - нумерация с 0
    * @param int $i
    * @return CFile 
    */
   function getChild($i) {return $this->children[$i];}
   
   // ============================================================================
   /**
    * 
    * Возвращает информацию о размере файла в соответствующих единицах
    * @return string
    */
   function getSizeStr() {
      $sizeStr = "{$this->size}b";
      $kb = 1024;
      if ($this->size >= $kb) {
         $sizeStr = $this->size / $kb;
         $sizeStr = round($sizeStr * 10) / 10;
         $sizeStr = $sizeStr . "Kb";
      };
      
      $mb = $kb * 1024;
      if ($this->size >= $mb) {
         $sizeStr = $this->size / $mb;
         $sizeStr = round($sizeStr * 10) / 10;
         $sizeStr = $sizeStr . "Mb";
      };
      
      $gb = $mb * 1024;
      if ($this->size >= $gb) {
         $sizeStr = $this->size / $gb;
         $sizeStr = round($sizeStr * 10) / 10;
         $sizeStr = $sizeStr . "Gb";
      };
      
      return $sizeStr;
   }
   
   // ============================================================================
   /**
    * Распечатка информации о файле
    * @return void
    */
   function e() {
      UI_ln("$this->name, $this->size, $this->type, $this->path");
      for ($i=0; $i<count($this->children); $i++) {
         $node = $this->children[$i];
         $node->e();
      }
      
   }
   
   // ============================================================================
   /**
    * 
    * Возвращает размер узла в байтах (папки или файла)
    * @return void
    */
   function calcSize() {
      if ($this->type != "dir") {
         return;
      };
      
      $mySize = 0;
      for ($i=0; $i<count($this->children); $i++) {
         $node = $this->children[$i];
         if ($node->getType() == "dir") {
            $node->calcSize();
         };
         $mySize += $node->getSize();
      };
      
      $this->setSize($mySize);
   }

   // ============================================================================
   /**
    * 
    * Заносит в имя файла информацию о его размере
    * @return void
    */
   function updateName() {
      $name    = $this->getName();
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
   
      $path2 = str_replace($name, $name2, $path);
   
      $this->setName($name2);
      
      for ($i=0; $i<count($this->children); $i++) {
         $node = $this->children[$i];
         $node->updateName();
      };
      
   }
   
   function isDir() {
      return ($this->type == File::TDIR);
   }

};
?>