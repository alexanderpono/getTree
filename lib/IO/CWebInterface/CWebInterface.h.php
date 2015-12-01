<?php
// ============================================================================
//    PURPOSE:             ����������� ��������� ������� � GET- � 
//                            POST-���������� ������ �������� �������� �����
//
//    FUNCTIONAL AREA:     IO
//    NAME:                CWebInterface.h.php 
//    VERSION:             ver.txt
//    AUTHORS:             Sasha
//    DESIGN REFERENCE:    
//    MODIFICATION:        
// ============================================================================
// ================================================================== CONSTANTS
// ================================================================== VARIABLES
// ================================================================== FUNCTIONS

// ============================================================================
class CWebInterface 
// ============================================================================
{
   var $m_storage;
   var $m_cacheAr;      //������������� array-������ � ������������ xml-�������
                        //���� = "$fullFPath-$getPost"
                        //�������� = �������� ������ � ������ 
   var $m_header;      //�������� header, ������� ����� ������� � �������� �����
   var $m_clientIP;    //IP-����� ����������� ����������. ������������ � COrderDoc, CRunLocal
   
   // ============================================================================
   function CWebInterface() 
   // ============================================================================
   {
      $this->m_cacheAr = array();
      $this->m_header = "";
      $this->m_header = "";
   }
   
   // ============================================================================
   function init(&$io) 
   // ============================================================================
   {
      $this->m_storage = $io->getStorage(); 
   }
   
   // ============================================================================
   function setMA($structTableName, $dirName, $post, &$FP)
   // ============================================================================
   {
      $getType = "get";
      if ($post)
      {
         $getType = "post";
      };
      
      $fullFName = $dirName . "/$structTableName.xml";
      if ($dirName == "")
      {
         $fullFName = "$structTableName.xml";
      };
      
      $key = $fullFName . "-" . $getType;
      //UI_echo('setMA() $key', $key);
      $this->m_cacheAr[$key] = $FP;
      
   }
   
   // ============================================================================
   function getUrlParam($paramName, $post=false)
   // ============================================================================
   //���������������� ������ � ��������� url
   {
      $param = $_GET[$paramName];
      if ($post)
      {
         $param = $_POST[$paramName];
      };
      
      return $param;  
   }
   
   // ============================================================================
   function getFileParameters_($structTableName, $post, $dirName, $useStructBlock=false)
   // ============================================================================
   //PURPOSE:
      //���������� �������� ������ �� ���������� ���������� ��������� ������

   //INPUT:
      //($structTableName) - ��� ������� � ����������� � ��������� ���������� ��������� ������
      //($post)            - true, ���� ����� �������� �� POST-����������
      //($dirName)         - ���� � �������� � �������� ��������� ������
      //($useStructBlock)  - ���� true, �� ��� �������� ��������� ������������ ���� <struct> xml-�����


   //OUTPUT:
      //��� ������� - (mainArray) - �������� ���������� ��������� ������
   {
      $getType = "get";
      if ($post)
      {
         $getType = "post";
      };
      
      $fullFName = $dirName . "/$structTableName.xml";
      if ($dirName == "")
      {
         $fullFName = "$structTableName.xml";
      };
      
      $key = $fullFName . "-" . $getType;
      $instanceName = "<b>$structTableName</b> from $getType";      
      
      
      $MA1 = $this->m_cacheAr[$key];
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
         
      
      $httpVarsArray = array();
      if ($post)
      {
         foreach($_POST as $var => $val)
         {
            $httpVarsArray[$var] = $val;            
         };
      }
      else
      {
         foreach($_GET as $var => $val)
         {
            $httpVarsArray[$var] = $val;            
         };
      };

      if (!$useStructBlock)
      {
         /*$paramsStruct = new CFW_XML_Table("CFW_XML_Table");
         $paramsStruct->setDebugMode(true);
         $fullFName = $dirName . "/$structTableName.xml";
         if ($dirName == "")
         {
            $fullFName = "$structTableName.xml";
         };
         //UI_echo('$fullFName', $fullFName);
         $code = $paramsStruct->load($fullFName, true);*/
         
         $paramsStruct = $this->m_storage->getMA($structTableName, $dirName, "CFW_XML_Table");

         $FP = new CMainArray("CMainArray");

         for ($i=1; $i<=$paramsStruct->getRecordsNumber(); $i++)
         {
            $dbFieldName   = $paramsStruct->getVal(SYS_FW_FP__NAME, $i);
            $dbFieldType   = $paramsStruct->getVal(SYS_FW_FP__TYPE, $i);
            $VB_id         = $paramsStruct->getVal(SYS_FW_FP__VB_ID, $i);

            //data type for i-nth field
               $FP->_setFieldType($i, $dbFieldName, $dbFieldType, $VB_id);
            // /data type for i-nth field

            //value for for i-nth field
               $val = $httpVarsArray[$dbFieldName];
               $val = str_replace("\\\"", "\"", $val);
               $val = STARTER_sequreUrl($val);               
               $FP->setVal($i, $val);
            // /value for for i-nth field
         };
         $FP->setFieldsNumber($paramsStruct->getRecordsNumber());
         $FP->setRecordsNumber(1);
      }
      else
      {
         $FP = new CFW_XML_Table("<b>$structTableName</b> from $getType");
         $FP->setDebugMode(true);
         $code = $FP->load($fullFName, true);

         for ($i=1; $i<=$FP->getFieldsNumber(); $i++)
         {
            $dbFieldName   = $FP->getDBFieldName($i);
            $val = $httpVarsArray[$dbFieldName];
            $val = str_replace("\\\"", "\"", $val);
            $val = STARTER_sequreUrl($val);               
            
            $FP->setVal($i, $val);
         };
         $FP->setRecordsNumber(1);
      };

      $this->m_cacheAr[$key] = $FP;
      
      return $FP;
   }

   // ============================================================================
   function getFileParameters($structTableName, $post=false)
   // ============================================================================
   //PURPOSE:
      //���������� �������� ������ �� ���������� ���������� ��������� ������

   //INPUT:
      //($structTableName) - ��� ������� � ����������� � ��������� ���������� ��������� ������
      //($post)            - true, ���� ����� �������� �� POST-����������

   //OUTPUT:
      //��� ������� - (mainArray) - �������� ���������� ��������� ������
   {
      return $this->getFileParameters_($structTableName, $post, APP_DB_DIR);
   }

   // ============================================================================
   function getFileParameters2($structTableName, $post=false)
   // ============================================================================
   //PURPOSE:
      //���������� �������� ������ �� ���������� ���������� ��������� ������

   //INPUT:
      //($structTableName) - ��� ������� � ����������� � ��������� ���������� ��������� ������
      //($post)            - true, ���� ����� �������� �� POST-����������

   //OUTPUT:
      //��� ������� - (mainArray) - �������� ���������� ��������� ������
   {
      return $this->getFileParameters_($structTableName, $post, APP_DB_DIR, true);
   }
   
   // ============================================================================
   function setHeader($header)
   // ============================================================================
   //���������� http header
   {
      $this->m_header = $header;
   }

   // ============================================================================
   function pushHeader()
   // ============================================================================
   //�������� ����� ����������� http header ���������� ����������
   {
      if ($this->m_header != "")
      {
         header($this->m_header);
      };
   }
   
   // ============================================================================
   function setClientIP($ip)
   // ============================================================================
   //���������� IP-����� ����������� ����������
   {
      $this->m_clientIP = $ip;
   }

   // ============================================================================
   function getClientIP()
   // ============================================================================
   //���������� IP-����� ����������� ����������
   {
      if ($this->m_clientIP != "")
      {
         return $this->m_clientIP;
      }
      else
      {
         $ip = $_SERVER['REMOTE_ADDR'];
         return $ip;
      };
   }

};



?>