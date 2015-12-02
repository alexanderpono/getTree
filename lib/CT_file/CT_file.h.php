<?php
/**
 * Класс CT_file умеет загружать текст из файла
 * @author Sasha
 */

/**
 * Класс CT_file умеет загружать текст из файла
 */
class CT_file extends CDebugClass
{
   /**
    * содержимое загруженного файла
    * @var string
    */
   var $m_html;
   
   /**
    * путь к загруженному файлу
    * @var string
    */
   var $m_path;

   // ============================================================================
   /**
    * constructor
    * @param string $myName - название экземпляра класса
    */
   function __construct($myName)
   {
      parent::__construct($myName);
      $this->m_html = "";
      $this->m_path = "";
   }

   // ============================================================================
   /**
    * Возвращает, существует ли указанный файл
    * @param string $fName - название файла (может содердать полный путь к файлу)
    * @param string $path - путь к папке, где лежит файл
    */
   function fileExists($fName, $path = "")
   {
      $fullPath = ($path == "") ? ($fName) : ($path . "/" . $fName);

      $exist    = file_exists($fullPath);
      
      return $exist;
   }

   // ============================================================================
   /**
    * Загружает текстовый файл в память, возвращает его текст.
    * ожидает путь к файлу (fName) от корневого каталога
    * до каталога, где находится файл с шаблоном (без слэш на конце);
    * @param $fName
    * @param $path
    * @return string - текст загруженного файла
    */
   function load($fName, $path = "")
   {
      $this->m_path = $path;

      $fullPath = ($path == "") ? ($fName) : ($path . "/" . $fName);
      $inFile = fopen($fullPath, "r");
      $s = "";
      if ($inFile == "")
      {
         $this->m_html = $s;
         return $s;
      };
      
      while (!feof($inFile)) {
        $s .= fgets($inFile, 16384);
      }
      fclose($inFile);

      $this->m_html = $s;

      return $s;
   }

   // ============================================================================
   /**
    * замена путей к картинкам в html-файле
    * @param $html
    * @param $path
    */
   function correctPicturesPaths($html, $path)
   {
      global $_prjPicturesRoot;
      $directoryPath = ($path == "") ? ("") : ($_prjPicturesRoot . $path . "/");
      //теперь надо заменить все ссылки на картинки: <img src="
      //на модифицированные ссылки <img src="

      $html1 = str_replace("<img src=\"", "<img  src=\"$directoryPath", $html);
      return $html1;
   }

   // ============================================================================
   /**
    * замена точки вставки на значение
    * @param $text
    * @param $id
    * @param $insertValue
    * @param $startShar
    * @return string
    */
   function replace($text, $id, $insertValue, $startShar="@")
   {
      $s = str_replace("$startShar$id;", $insertValue, $text);
      return $s;
   }

   // ============================================================================
   /**
    * замена точки вставки на значение
    * @param $text
    * @param $id
    * @param $insertValue
    * @return string
    */
   function replace2($text, $id, $insertValue)
   {
      $s = str_replace($id, $insertValue, $text);
      return $s;
   }

   // ============================================================================
   /**
    * сохраняет содержимое строки ($s) в указанный файл ($fName) в папке ($path).
    * ожидает путь к файлу (fName) от корневого каталога
    * до каталога, где находится файл с шаблоном (без слэш на конце);
    * @param $fName
    * @param $s
    * @param $path
    */
   function save($fName, $s, $path = "")
   {
      $this->m_path = $path;

      $fullPath = ($path == "") ? ($fName) : ($path . "/" . $fName);
      $outFile = fopen($fullPath, "wt");
      if ($outFile == "")
      {
         return false;
      };
      
      fputs($outFile, $s);
      fclose($outFile);

      return $s;
   }


};
?>