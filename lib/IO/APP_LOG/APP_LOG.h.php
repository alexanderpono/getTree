<?php
/**
 * Программный интерфейс вывода отладочной информации в лог-файл
 * @author Sasha
 */

// ============================================================================
/**
 * Вывод строки в лог-файл
 * @param string $s
 */
function APP_LOG_ln($s)
{
   $filename = APP_LOG_DIR . '/changelog.txt';
   $content = $s . "\n";

   $write = false;

   if (is_writeable(dirname($filename)))
   {
      //родительская папка папки ($fullDirName) доступна для записи
      //это хорошо
      if (!file_exists($filename))
      {
         $write = true;
      };
   };
   
   if (is_writable($filename))
   {
      $write = true;
   };

   if ($write) 
   {
      // Открываем $filename в режиме "дописать в конец".
      // Таким образом, смещение установлено в конец файла и
      // наш $content допишется в конец при использовании fwrite().
      if (!$handle = fopen($filename, 'a')) 
      {
         echo "Не могу открыть файл ($filename)";
         exit;
      }

      // Записываем $content в наш открытый файл.
      if (fwrite($handle, $content) == FALSE) 
      {
         echo "Не могу произвести запись в файл ($filename)";
         exit;
      }

      //echo "Ура! Записали ($content) в файл ($filename)";

      fclose($handle);

      $fs = new CFileSystem('CFileSystem');
      $fs->chmod($filename, 0666);
      //APP_LOG_echo('APP_LOG_ln() $filename', $filename);
   } 
   else 
   {
      $msg = "CCMSApplication log_ln(): Файл $filename недоступен для записи";
      print "<!--$msg-->\n";
   };

   
}

// ============================================================================
/**
 * Вывод пары $v=$s в лог-файл
 * @param $v
 * @param $s
 */
function APP_LOG_echo($v, $s)
{
   $filename = APP_LOG_DIR . '/changelog.txt';
   $content = $v . " = " . $s . "\n";
    
   if (is_writeable(dirname($filename)))
   {
      //родительская папка папки ($fullDirName) доступна для записи
      //это хорошо
      if (!file_exists($filename))
      {
         $write = true;
      };
   };
   
   if (is_writable($filename))
   {
      $write = true;
   };

   if ($write) 
   {
      // Открываем $filename в режиме "дописать в конец".
      // Таким образом, смещение установлено в конец файла и
      // наш $content допишется в конец при использовании fwrite().
      if (!$handle = fopen($filename, 'a')) {
            echo "Не могу открыть файл ($filename)";
            exit;
      }

      // Записываем $content в наш открытый файл.
      if (fwrite($handle, $content) === FALSE) {
          echo "Не могу произвести запись в файл ($filename)";
          exit;
      }

      fclose($handle);
       
      $fs = new CFileSystem('CFileSystem');
      $badFiles = $fs->chmod($filename, 0666);
   }
   else
   {
      $msg = "CCMSApplication log_echo(): Файл $filename недоступен для записи";
      //UI_ln();
      print "<!--$msg-->\n";
   };
} 

// ============================================================================
/**
 * Вывод информации о старте транзакции в лог-файл
 * @param string $s
 */
function APP_LOG_TRANS_BEGIN($s)
{

    $date = date("Y-m-d");
    $time = date("H:i:s");
    $content =  
"================================================================================\n".
"$date $time $s:начало транзакции\n".
"--------------------------------------------------------------------------------";

    APP_LOG_ln($content);

}

// ============================================================================
/**
 * Вывод информации о завершении транзакции в лог-файл
 * @param string $s
 */
function APP_LOG_TRANS_END($s)
{

    $date = date("Y-m-d");
    $time = date("H:i:s");
    $content =    
"--------------------------------------------------------------------------------\n".
"$date $time $s:конец транзакции\n".
"================================================================================\n\n";

    APP_LOG_ln($content);

}

// ============================================================================
/**
 * Распечатка основного массива в лог-файл
 * @param $maName
 * @param $ma
 */
function APP_LOG_echoMA($maName, $ma)
{
    APP_LOG_ln("Печать основного массива '$maName'");
    $s = $ma->e(false, false);
    APP_LOG_echo($maName, $s);
}

// ============================================================================
/**
 * Распечатка ассоциативного массива в лог-файл
 * @param unknown_type $name
 * @param unknown_type $arr
 */
function APP_LOG_echoAArr($name, $arr)
{
    APP_LOG_ln("Печать ассоциативного массива '$name'");
    while (list($key, $value) = each($arr))
    {
       APP_LOG_echo($name.".".$key, $value);
    };
}


?>