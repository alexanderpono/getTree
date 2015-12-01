<?php
// ============================================================================
//    PURPOSE:             ����������� ��������� ������� � ��������� xml-������ �� �����
//
//    FUNCTIONAL AREA:     IO
//    NAME:                CStorage.h.php 
//    VERSION:             ver.txt
//    AUTHORS:             Sasha
//    DESIGN REFERENCE:    
//    MODIFICATION:        
// ============================================================================
// ================================================================== CONSTANTS
// ================================================================== VARIABLES
// ================================================================== FUNCTIONS

// ============================================================================
class CStorage 
// ============================================================================
{
   var $m_cacheAr;      //array-������ � ������������ xml-�������
   var $m_idToIndexAr;  //������������� array-������ {id ����� -> ������ ����� � m_cacheAr}
   
   // ============================================================================
   function CStorage() 
   // ============================================================================
   {
      $this->m_cacheAr     = array();
      $this->m_idToIndexAr = array();
   }
   
   // ============================================================================
   function getMA($id, $dirName="", $instanceName="CFW_XML_Table")
   // ============================================================================
   //���������� �������� ������ � ��������������� ($id) 
   {
      $fullFName = $dirName . "/$id.xml";
      if ($dirName == "")
      {
         $tail       = substr($id, strlen($id) - 4, 4);
         //UI_echo('$tail', $tail);
         $fullFName  = $id;
         if ($tail != ".xml")
         {
            $fullFName = "$id.xml";
         };
      };
      //UI_echo('$id', $id);
      //UI_echo('$fullFName', $fullFName);
      
      if ($this->m_idToIndexAr[$fullFName] != "")
      {
         //APP_LOG_echo('xml cache hit', $fullFName);
         //UI_echo('$fullFName cache hit', $fullFName);
         $index = $this->m_idToIndexAr[$fullFName];
         
         $MA1 = $this->m_cacheAr[$index];
         if (is_object($MA1))
         {
            $MA = new CFW_XML_Table($instanceName);
            $MA1->copyStructureInto($MA);
            $MA->m_xml = $MA1->m_xml; //����������� ����������� xml-��������� ������ �����. ��� ����� ���� ������� ��� �������������� ������ ����� STRUCT ��������� �������
            $MA->copyFrom(
                              $MA1, 
                              1, 
                              1, 
                              $MA1->getRecordsNumber()
                           );
            return $MA;
         };
         
      };
      
      $MA = new CFW_XML_Table($instanceName);
      $MA->setDebugMode(true);
      
      //UI_echo('$fullFName', $fullFName);
      $code = $MA->load($fullFName, true);
      
      $this->m_cacheAr[] = $MA;
      $newMAIdex = (count($this->m_cacheAr) - 1);
      $this->m_idToIndexAr[$fullFName] = $newMAIdex;
      
      return $MA;
   }
   
};



?>