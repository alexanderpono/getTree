<?php
/**
 * Класс CDebugClass хранит название объекта, 
 * которое может быть использовано для диагностики ошибок
 * @author Sasha
 *
 */
class CDebugClass
{
   /**
    * имя экземпляра класса
    * @var string
    */
   private $m_instanceName;
   
   /**
    * экземпляр объекта вывода на stdout
    * @var CIO
    */
   private $m_io;

   /**
    * если true, то не останавливает выполнение программы при ошибках
    * (добавлено 15.12.2004)
    * @var bool
    */
   private $m_debugMode;

   /**
    * constructor 
    * @param string $myName
    */
   function __construct($myName) {
      global $_io;
      
      $this->m_instanceName      = $myName;
      
      $this->m_io                = $_io->getStdOut();
   }

   /**
    * get
    * @return string
    */
   function getInstanceName() {return $this->m_instanceName;}
   
   /**
    * set
    * @param string $name
    */
   function setInstanceName($name) {$this->m_instanceName = $name;}

   /**
    * вывод строки ($msg) от имени процедуры ($subName) 
    * этого экземпляра объекта
    * @param $subName
    * @param $msg
    */
   function msg($subName, $msg) {   
      global $_IO_br;
      
      $objName = $this->getInstanceName();
      $this->m_io->ln("$_IO_br$objName::$subName() : $msg");
   }

   /**
    * set
    * @param CIO $io
    */
   function setIO($io)
   {
      $this->m_io = $io;
   }

   /**
    * stopIfEmpty
    * @param $val
    * @param $subName
    * @param $message
    */
   function stopIfEmpty($val, $subName, $message)
   {
      $this->stopIfFalse(($val != ""), $subName, $message);
   }


   /**
    * stopIfFalse
    * @param $val
    * @param $subName
    * @param $message
    */
   function stopIfFalse($val, $subName, $message)
   {
      if (!$val)
      {
         $this->msg($subName, $message);
         die();
      };
   }

   /**
    * вывод строки ($msg) от имени процедуры ($subName) этого экземпляра объекта
    * @param $varName
    * @param $val
    */
   function echoVar($varName, $val)   
   //
   {
      $objName = $this->getInstanceName();
      UI_echo($this->getInstanceName() . " $varName", $val);
   }

   /**
    * set
    * @param bool $mode
    */
   function setDebugMode($mode) {
      $this->m_debugMode = $mode;
   }
};


?>
