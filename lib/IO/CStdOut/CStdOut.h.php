<?php
/**
 * CStdOut
 * @author Sasha
 */

/**
 * Последовательность перевода строки, который добавлять при выводе на stdout
 * @var string
 */
$_IO_br = "<BR>\n";

/**
 * Название функции, которую вызывать для перекодирования вывода на stdout
 * @var string
 */
$_IO_recodeFunction = "noConvert";

/**
 * Программный интерфейс вывода отладочной информации в стандартный поток
 *
 */
class CStdOut
{

   /**
    * Строковый буфер для вывода на stdout 
    * @var string
    */
   private $m_buf;
   
   /**
    * Режим вывода (например, "var")
    * @var string
    */
   private $m_mode;

   /**
    * constructor
    */
   function __construct() {$this->m_buf = ""; $this->m_mode = ""; }

   /**
    * set mode
    * @param string $o
    * @return string предыдущее значение mode
    */
   function setMode($o) {
      $a = $this->m_mode;
      $this->m_mode = $o;
      return $a;
   }

   /**
    * возвращает содержимое буфера. Очищает внутреннюю переменную буфера
    * @return string 
    */
   function flush()
   {
      $a = $this->m_buf;
      $this->m_buf = "";
      //UI_echo("mybuf", $a);
      return $a;
   }

   /**
    * вывод значения ($varValue) переменной ($varName)
    * @param $varName
    * @param $varValue
    * @return void
    */
   function e($varName, $varValue)   
   {
      $this->wr("$varName = $varValue<BR>");
   }

   /**
    * Вывод строки
    * @param string $s
    */
   function wr($s)   
   {
      if ($this->m_mode == "var")
      {
         $this->m_buf .= "$s";
      }
      else
      {
         global $_IO_recodeFunction;
         $s1 = $_IO_recodeFunction($s);

         print("$s1");
      };

   }

   /**
    * Вывод строки с html-переводом каретки
    * @param string $s
    */
   function ln($s)   
   {
      $this->wr("$s<BR>");
   }

   /**
    * Вывод строки ($msg) от имени процедуры ($subName)
    * @param $subName
    * @param $msg
    */
   function msg($subName, $msg)   
   {
      $this->ln("<br>$subName : $msg");
   }

};


/**
 * Вывод строки
 * @param string $s
 */
function UI_wr($s)
{
   global $_IO_recodeFunction;
   $s1 = $_IO_recodeFunction($s);

   print $s1;
}


/**
 * Вывод строки с переводом каретки
 * @param string $msg
 */
function UI_ln($msg)
{
   global $_IO_br;
   
   UI_wr($msg . $_IO_br);
}


/**
 * вывод значения ($varValue) переменной ($varName)
 * @param $varName
 * @param $varValue
 * @return void
 */
function UI_echo($varName, $varValue)
{
   UI_ln($varName . "=" . $varValue);
}

/**
 * Распечатка ассоциативного массива
 * @param unknown_type $name
 * @param unknown_type $arr
 */
function UI_echoAArr($name, $arr)
{
   while (list($key, $value) = each($arr))
   {
      UI_echo($name.".".$key, $value);
   };
}

/**
 * Распечатка array-массива с форматированием
 * @param string $name
 * @param string $arr
 */
function UI_echo2($name, $arr)
{
   print "<pre>";
   print_r($arr);
   print "</pre><hr>";
}

/**
 * Функция перекодировки по умолчанию
 * @param string $s
 */
function noConvert($s)
{
   return $s;
}


?>