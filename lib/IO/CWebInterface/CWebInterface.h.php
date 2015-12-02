<?php
/**
 * Программный интерфейс доступа к GET- и 
 * POST-параметрам вызова активной страницы сайта
 * @author Sasha
 */

/**
 * Программный интерфейс доступа к GET- и 
 * POST-параметрам вызова активной страницы сайта
 */
class CWebInterface 
{
   /**
    * экземпляр класса CStorage
    * @var CStorage
    */
   var $m_storage;
   
   /**
    * ассоциативный array-массив с загруженными xml-файлами
    * ключ = "$fullFPath-$getPost"
    * значение = основной массив в памяти 
    * @var array
    */
   var $m_cacheAr;      
   
   /**
    * значение header, которое нужно вывести в выходной поток
    * @var string
    */
   var $m_header;

   /**
    * IP-адрес клиентского компьютера. Используется в COrderDoc, CRunLocal
    * @var string
    */
   var $m_clientIP;    
   
   /**
    * constructor
    */
   function __construct() 
   {
      $this->m_cacheAr = array();
      $this->m_header = "";
      $this->m_header = "";
   }
   
    // ============================================================================
    /**
     * init
     * @param $io
     */
    function init(&$io) 
    {
        $this->m_storage = $io->getStorage(); 
    }
   
    // ============================================================================
    /**
     * хапоминает ассоциативный массив в кэше
     * @param string $structTableName - название структуры массива
     * @param string $dirName - путь к xml-файлу структуры массива на диске
     * @param bool $post
     * @param MainArray $FP - параметры url
     */
    function setMA($structTableName, $dirName, $post, &$FP)
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
    /**
     * непосредственный доступ к параметру url
     * @param $paramName
     * @param $post
     * @return string
     */
    function getUrlParam($paramName, $post=false)
    {
      $param = $_GET[$paramName];
      if ($post)
      {
         $param = $_POST[$paramName];
      };
      
      return $param;  
    }
   
    // ============================================================================
    /**
     * возвращает основной массив со значениями параметров командной строки
     * @param string $structTableName - имя таблицы с информацией о структуре параметров командной строки
     * @param bool $post - true, если брать значения из POST-переменных
     * @param string $dirName - путь к каталогу с таблицей командной строки
     * @param bool $useStructBlock - если true, то для загрузки структуры использовать блок <struct> xml-файла
     * @return MainArray - значения параметров командной строки
     */
    function getFileParameters_($structTableName, $post, $dirName, $useStructBlock=false)
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
         $MA->m_xml = $MA1->m_xml; //копирование древовидной xml-структуры данных файла. Она может быть полезна при низкоуровневом чтении блока STRUCT основного массива
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
    /**
     * возвращает основной массив со значениями параметров командной строки
     * @param string $structTableName - имя таблицы с информацией о структуре параметров командной строки
     * @param bool $post - true, если брать значения из POST-переменных
     * @return MainArray - значения параметров командной строки
     */
    function getFileParameters($structTableName, $post=false)
    {
      return $this->getFileParameters_($structTableName, $post, APP_DB_DIR);
    }

   // ============================================================================
   /**
    * возвращает основной массив со значениями параметров командной строки
    * @param $structTableName - имя таблицы с информацией о структуре параметров командной строки
    * @param $post - true, если брать значения из POST-переменных
     * @return MainArray - значения параметров командной строки
    */
    function getFileParameters2($structTableName, $post=false)
    {
      return $this->getFileParameters_($structTableName, $post, APP_DB_DIR, true);
    }
   
    // ============================================================================
    /**
     * запоминает http header
     * @param $header
     */
    function setHeader($header)
    {
      $this->m_header = $header;
    }

    // ============================================================================
    /**
     * передает ранее запомненный http header клиенскому компьютеру
     */
    function pushHeader()
    {
      if ($this->m_header != "")
      {
         header($this->m_header);
      };
    }
   
    // ============================================================================
    /**
     * запоминает IP-адрес клиентского компьютера
     * @param $ip
     */
    function setClientIP($ip)
    {
      $this->m_clientIP = $ip;
    }

    // ============================================================================
    /**
     * возвращает IP-адрес клиентского компьютера
     * @return string
     */
    function getClientIP()
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