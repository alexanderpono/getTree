<?php
/**
 * Программный интерфейс доступа к хранилищу xml-файлов на диске
 * @author Sasha
 */

/**
 * Программный интерфейс доступа к хранилищу xml-файлов на диске
 */
class CStorage 
{
   /**
    * array-массив с загруженными xml-файлами
    * @var array
    */
   var $m_cacheAr;      
   
   /**
    * ассоциативный array-массив {id файла -> индекс файла в m_cacheAr}
    * @var array
    */
   var $m_idToIndexAr;  
   
   // ============================================================================
   /**
    * constructor
    */
   function __construct() 
   {
      $this->m_cacheAr     = array();
      $this->m_idToIndexAr = array();
   }
   
   // ============================================================================
   /**
    * возвращает основной массив с идентификатором ($id) 
    * @param string $id
    * @param string $dirName
    * @param string $instanceName
    */
   function getMA($id, $dirName="", $instanceName="CFW_XML_Table")
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
      
      if ($this->m_idToIndexAr[$fullFName] != "")
      {
         $index = $this->m_idToIndexAr[$fullFName];
         
         $MA1 = $this->m_cacheAr[$index];
         if (is_object($MA1))
         {
            $MA = new CFW_XML_Table($instanceName);
            $MA1->copyStructureInto($MA);
            $MA->m_xml = $MA1->m_xml; //копирование древовидной xml-структуры данных файла. Она может быть полезна при низкоуровневом чтении блока STRUCT основного массива
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
      
      $code = $MA->load($fullFName, true);
      
      $this->m_cacheAr[] = $MA;
      $newMAIdex = (count($this->m_cacheAr) - 1);
      $this->m_idToIndexAr[$fullFName] = $newMAIdex;
      
      return $MA;
   }
   
};



?>