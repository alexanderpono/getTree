<?php
// ============================================================================
//    PURPOSE:             √лавный класс подсистемы IO
//
//    FUNCTIONAL AREA:     IO
//    NAME:                CIO.h.php 
//    VERSION:             ver.txt
//    AUTHORS:             Sasha
//    DESIGN REFERENCE:    
//    MODIFICATION:        
// ============================================================================
// =================================================================== INCLUDES
global $_libPath;
include_once($_libPath . "/IO/CStdOut/CStdOut.h.php");
include_once($_libPath . "/IO/CStorage/CStorage.h.php");
include_once($_libPath . "/IO/CWebInterface/CWebInterface.h.php");
include_once($_libPath . "/IO/APP_LOG/APP_LOG.h.php");
// =================================================================== SYNOPSIS
// ================================================================== CONSTANTS
// ================================================================== VARIABLES
// ================================================================== FUNCTIONS

// ============================================================================
class CIO 
// ============================================================================
{
   var $m_webI;      //экземпл€р класса CWebInterface
   var $m_stdOut;    //экземпл€р класса CStdOut
   var $m_storage;   //экземпл€р класса CStorage
   
   
   // ============================================================================
   function CIO() 
   // ============================================================================
   {
      //$this->CDebugClass($myName);
      $this->m_webI = new CWebInterface();
      $this->m_stdOut = new CStdOut();
      $this->m_storage = new CStorage();
      
      $this->m_webI->init($this);
      //$this->m_stdOut->setIO($this);
      //$this->m_storage->setIO($this);
   }
   
   function &getStdOut()   {return $this->m_stdOut;}
   function &getWebI()     {return $this->m_webI;}
   function &getStorage()  {return $this->m_storage;}

   function &setWebI(&$webI)     {$this->m_webI = $webI;}
   
};



?>