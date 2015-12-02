<?php
/**
 * CIO
 * @author Sasha
 */

global $_libPath;
include_once($_libPath . "/IO/CStdOut/CStdOut.h.php");
include_once($_libPath . "/IO/CStorage/CStorage.h.php");
include_once($_libPath . "/IO/CWebInterface/CWebInterface.h.php");
include_once($_libPath . "/IO/APP_LOG/APP_LOG.h.php");

/**
 * Главный класс подсистемы 
 * @author Sasha
 */
class CIO 
{
   /**
    * экземпляр класса CWebInterface
    * @var CWebInterface
    */
   private $m_webI;

   /**
    * экземпляр класса CStdOut
    * @var CStdOut
    */
   private $m_stdOut;

   /**
    * экземпляр класса CStorage
    * @var CStorage
    */
   private $m_storage;   
   
   
   /**
    * constructor
    */
   function __construct() 
   {
      $this->m_webI = new CWebInterface();
      $this->m_stdOut = new CStdOut();
      $this->m_storage = new CStorage();
      
      $this->m_webI->init($this);
   }
   
   /**
    * get 
    * @return CStdOut
    */
   function &getStdOut()   {return $this->m_stdOut;}
   
   /**
    * get
    * @return CWebInterface
    */
   function &getWebI()     {return $this->m_webI;}
   
   /**
    * get
    * @return CStorage
    */
   function &getStorage()  {return $this->m_storage;}

   /**
    * set 
    * @param $webI
    */
   function &setWebI(&$webI)     {$this->m_webI = $webI;}
   
};



?>