<?php
// ============================================================================
//    PURPOSE:             Класс CFileSystem предоставляет методыдля взаимодействия
//                         с файловой системой
//
//    FUNCTIONAL AREA:
//    NAME:                CFileSystem.h
//    VERSION:             1.0
//    AUTHORS:             Dima
//    DESIGN REFERENCE:
//    MODIFICATION:
// ============================================================================
// =================================================================== INCLUDES
// =================================================================== SYNOPSIS
// ================================================================== CONSTANTS
// ================================================================== VARIABLES
// ================================================================== FUNCTIONS

class CFileSystem extends CDebugClass
{
   // ============================================================================
   function CFileSystem($myName)
   // ============================================================================
   {
      $this->CDebugClass($myName);
   }

   // ============================================================================
   function mkDir($dir)
   // ============================================================================
   //рекурсивно создает каталог $dir
   {
      if(!file_exists(dirname($dir)) && !$this->mkDir(dirname($dir)))
      {
         return false;
      };
      
      return is_dir($dir) || @mkdir($dir);
   }
   
   // ============================================================================
   function mkDir2($dir, $write=false)
   // ============================================================================
   //рекурсивно создает каталог $dir и дает права на запись в этот каталог всем пользователям (если установлен флаг $write)
   {
      global $indexPhpDocRoot;

      $dirAr = explode('/', $dir);

      $sitePathLen = strlen($indexPhpDocRoot);
      for ($i=0; $i<count($dirAr); $i++)
      {
         $dirS = $dirAr[$i];
         $slash = '';
         
         if ($i > 0)
         {
            $slash = '/'; 
         };
         $fullDirName = $fullDirName . $slash . $dirS;
         if ($fullDirName == '')
         {
            continue;
         };

         if (strpos(" " . $indexPhpDocRoot, $fullDirName) == 1)
         {
            //мы находимся в той части пути, которая выше корневой папки хостинга. Этот путь 100% существует
            continue;
         };

         if (file_exists($fullDirName))
         {
            //этот часть пути существует
            continue;
         };
         
         if (is_writeable(dirname($fullDirName)))
         {
            //родительская папка папки ($fullDirName) доступна для записи
            //это хорошо
         }
         else
         {
            $msg = '<!--CFileSystem mkDir2() ' . dirname($fullDirName) .' не доступна для записи!' . "-->";
            print ($msg . "\n");
            //пытаюсь установить права доступа на запись у этой папки
            @chmod(dirname($fullDirName), 0777);
            if (!is_writeable(dirname($fullDirName)))
            {
               $msg = ('<!--CFileSystem mkDir2() не удается установить права на запись в папку ' . dirname($fullDirName) . "-->");
               print ($msg . "\n");
            };
         };
         
         @mkdir($fullDirName);
         if ($write)
         {
            @chmod($fullDirName, 0777);
         };
      };
      return false;
   }
   
   // ============================================================================
   function isEmptyDir($dir)
   // ============================================================================
   //проверяет, пуста ли директория
   {
       if ($dh = @opendir($dir))
       {
           while ($file = readdir($dh))
           {
               if ($file != '.' && $file != '..') {
                   closedir($dh);
                   return false;
               }
           }
           closedir($dh);
           return true;
       }
       else return false; 
   }
   
   // ============================================================================
   function clearDir($dirName, $recursive = false)
   // ============================================================================
   //удаляет содержимое каталога
   //возвращает true либо массив из всех файлов, которые ну удалось удалить
   {
      if(!is_dir($dirName))
      {
         return array(true, "'$dirName' is not a dir", array());
         //return array(false, "'$dirName' is not a directory", array());
      };
      
      if(!is_writeable($dirName))
      {
         return array(false, "has no write access to directory '$dirName'", array());
      };
      
      //массив из неудаленных файлов
      $notDeleted = array();
      
      $dir = opendir($dirName);
      while ($fName = readdir($dir))
      {
         if($fName == '.' || $fName=='..')
         {
            continue;
         };
         $file = $dirName . "/" . $fName;
         if (is_file($file)) 
         {
            //нужно выяснить, есть ли право на запись в директорию (если есть, то есть право на удаление файла)
            if(!unlink($file))
            {
               //UI_ln("Файл не удален:$file");
               //APP_LOG_ln("Файл не удален:$file");
               $notDeleted[] = $file;
            };
         }
         elseif($recursive && is_dir($file))
         {
            $resultAr = $this->clearDir($file, $recursive);
            $notDeleted = array_merge($notDeleted, $resultAr[2]);
            
            if(!@rmdir($file))
            {
               //APP_LOG_ln("Файл не удален:$file");
               //UI_ln("Папка не удалена:$file");
               $notDeleted[] = $file;
            }
         }
         else
         {
            //APP_LOG_ln("Файл не удален:$file");
            //UI_ln("Файл не удален:$file");
            $notDeleted[] = $file;
         }
      };
      
      return array(true, "", $notDeleted);
   }

   
   //TODO:должна возвращать bool
   //==============================================================================
   function dirCopy($basePath, $source, $dest, $overwrite = false, $ext= array("*"))
   //==============================================================================
   // копирует содержимое из одного каталога в другой
   // Массив $ext содержит расширения файлов, которые должны копироваться
   {
      if(!is_dir($basePath . $source))
      {
         return array(false, "'$basePath$source' is not a dir");
      }
      
      if(!is_dir($basePath . $dest))
      {
         $this->mkDir($basePath . $dest);
      }
      
      $messages = "";
      if($handle = opendir($basePath . $source))
      {
         while(false !== ($file = readdir($handle)))
         {
            if($file != '.' && $file != '..')
            {
               $path = $source . '/' . $file;
               if(is_file($basePath . $path))
               {
                  if((!is_file($basePath . $dest . '/' . $file) || $overwrite)&&($file != "Thumbs.db"))
                  {
                     $fileinfo = pathinfo($file);
                     if(!in_array($fileinfo['extension'],$ext, false) && !in_array("*",$ext,false))
                     {
                        continue;
                     }
                     $fileDestPath = $basePath . $dest . '/' . $file;
                     if(!copy($basePath . $path, $fileDestPath))
                     {
                        //UI_ln("Ошибка копирования в $fileDestPath");
                        //APP_LOG_ln('Файл '.$path.' не может быть скопирован; наиболее вероятно, что недостаточно прав доступа.');
                        //$this->chmod($fileDestPath, 0, false, true);
                        //if(!copy($basePath . $path, $basePath . $dest . '/' . $file))
                        //{
                        //};
                        
                        $messages .= ('Файл '.$path." не может быть скопирован в $dest; наиболее вероятно, что недостаточно прав доступа.<br>\n"); 
                        
                     };
                     
                  }
               } elseif(is_dir($basePath . $path))
               {
                  if(!is_dir($basePath . $dest . '/' . $file))
                  {
                     $this->mkDir($basePath . $dest . '/' . $file);
                  }
                  $this->dirCopy($basePath, $path, $dest . '/' . $file, $overwrite, $ext);
               }
            }
         }
         closedir($handle);
         
         return array(true, "$messages");
      }
   }
   //==============================================================================
   function copy($source, $dest, $overwrite = false)
   //==============================================================================
   //копирует файл $source в файл $dest
   {
      if(is_dir($source))
      {
         $this->dirCopy('', $source, $dest, $overwrite);
         return true;
      }
      if(!is_dir(dirname($dest)))
      {
         $this->mkDir(dirname($dest));
      }
      if(file_exists($dest) && !$overwrite)
      {
         return true;
      }
      if(!@copy($source, $dest)){
         return false;
      }
      return true;
   }
   
   //==============================================================================
   function getDirContents($dirName, $recursive=false)
   //==============================================================================
   //возвращает массив, содержащий имена всех файлов в каталоге.
   //если recursive = true, то в список включаются содержимое подкаталогов
   //либо false в случае неудачи
   {
      $result = array();
      
      if($handle = @opendir($dirName))
      {
         while(false !== ($file = readdir($handle)))
         {
            if($file != '.' && $file != '..')
            {
               //UI_ln($dirName.'/'.$file);
               $result[] = $file;
               if($recursive && is_dir($dirName.'/'.$file))
               {
                  $children = $this->getDirContents($dirName.'/'.$file, $recursive);
                  foreach($children as $child)
                  {
                     $result[] = $file.'/'.$child;
                  }
               }
            }
         }
         closedir($handle);
         return $result;
      }
      else
      {
         return false;
      }
   }
   
   //==============================================================================
   function getFilesName($dirName)
   //==============================================================================
   //возвращает массив, содержащий имена всех файлов в каталоге(исключая папки).
   //если recursive = true, то в список включаются содержимое подкаталогов
   //либо false в случае неудачи
   {
      $result = array();
      //UI_ln('getFilesName');
      if($handle = @opendir($dirName))
      {
         while(false !== ($file = readdir($handle)))
         {
            if($file != '.' && $file != '..')
            {
               //UI_ln($dirName.'/'.$file);
               if (!is_dir($dirName.'/'.$file))
               {
                  $result[] = $file;
               }
               else
               {
                  $children = $this->getFilesName($dirName.'/'.$file);
                  foreach($children as $child)
                  {
                     if (!is_dir($file.'/'.$child))
                     {
                        $result[] = $file.'/'.$child;
                     }
                  }
               }
            }
         }
         closedir($handle);
         return $result;
      }
      else
      {
         return false;
      }
   }
   
   //==============================================================================
   function chmod($target, $newPerms,$recursive=false,$addMode=false)
   //==============================================================================
   //ставит на указанный файл права в соответствии с mode
   //если $addMode == true, то права добавляются к текущим
   //возвращает true в случае успеха, false, если нет файлов для изменения,
   //либо массив из файлов, права на которые поменять не удалось
   {
      if (!file_exists($target))
      {
         $badFiles[] = $target;
         return $badFiles;
      };    
      
      $filesAr = array($target);
      if(is_dir($target))
      {
         $resultAr = $this->getDirContents($target, $recursive);
         for ($i=0; $i<count($resultAr); $i++)
         {
            $fileS = $resultAr[$i];
            $fileS = $target . "/" . $fileS;
            $resultAr[$i] = $fileS;
         };
         
         if(is_array($resultAr))
         {
            $filesAr = array_merge($filesAr, $resultAr);
         };
         
      };
      
      $badFiles = array();
      
      foreach($filesAr as $file)
      {
         $fullFName = $file;//$target."/".$file;
         $perms = @fileperms($fullFName);
         //UI_echo($fullFName . ' $perms', decoct($perms));
         
         
         $perms = ($addMode === false)? $newPerms: ($perms | $newPerms);
         
         if(!@chmod($fullFName,$perms))
         {
            //UI_ln("Не удалось установить права на файл: $file");
            //APP_LOG_ln("Не удалось установить права на файл: $file");
            $badFiles[] = $fullFName;
         }
      }
      
      return (count($badFiles)>0)?$badFiles: true;
   }
   
   //==============================================================================
   function move($source, $dest)
   //==============================================================================
   //переименовывает файл $source в файл $dest
   {
      if(!file_exists($source))
      {
         return false;
      }
      
      if(!@rename($source, $dest))
      {
         return false;
      }
      else
      {
         return true;
      }
   }
   
   //==============================================================================
   function unlink($target)
   //==============================================================================
   //удаляет целевой файл. В случае каталога удаляет рекурсивно
   {
      $result = true;
      if(!file_exists($target))
      {
         //APP_LOG_ln("Удаляемый файл $target уже не существует");
         return true;
      }
      if(is_dir($target))
      {
         if($handle = opendir($target))
         {
            while(false !== ($file = readdir($handle)))
            {
               if($file != '.' && $file != '..')
               {
                  if(!$this->unlink($target."/".$file))
                  {
                     $result = false;
                  }
               }
            }
            closedir($handle);
         }     
         
         if(!@rmdir($target))
         {       
            //APP_LOG_ln("Каталог $target удалить не удалось");
            $result = false;
         }
         return $result;
      }

      if(!@unlink($target))
      {
         //APP_LOG_ln("Файл $target удалить не удалось");
         $result = false;
      }
      return $result;
   }

   //==============================================================================
   function makeDirDiff($dir1,$dir2,&$diff, $append='')
   //==============================================================================
   // сравнивает два каталога $dir1 и $dir2
   //OUT:
   // возвращает true, если каталоги различны
   // массив $diff[0] содержит пути к файлам, уникальным для $dir1
   // массив $diff[1] содержит пути к файлам, уникальным для $dir2
   // массив $diff[2] содержит пути к файлам, различающимся в $dir1 и $dir2
   // массив $diff[3] содержит сообщения об ошибках
   {
      $return = false;
      $dir1 .= (substr($dir1,-1) != '/')?'/':'';
      $dir2 .= (substr($dir2,-1) != '/')?'/':'';
      $hd = @opendir($dir1.$append);
      $diff[3] = array();
      if($hd === false)
      {
         if ($append == "")
         {
            //выдаем аварийное сообщение только если нет самой верхней из сравниваемых папок
            $diff[3][] = "open dir failed($dir1$append)";
         };
      }
      else {
         //ищем файлы, уникальные для старой папки, а также проверяем существующие файлы на идентичность
         while(false !== ($file = readdir($hd)))
         {
            if(($file == '.') || ($file == '..'))
            {
               continue;
            }
            $f1name = $dir1.$append.$file;
            $f2name = $dir2.$append.$file;
            //UI_ln($f1name);
            if(is_dir($f1name))
            {
               if($this->makeDirDiff($dir1,$dir2,$diff,$append.$file.'/'))
               {
                  $return = true;
               }
               continue;
            };
            if(file_exists($f2name))
            {
               $f1size = filesize($f1name);
               $f2size = filesize($f2name);
               $f1time = filemtime($f1name);
               $f2time = filemtime($f2name);

               $mod = ($f2time < $f1time)?"[old]":"[new]";

               //проверка размера
               if($f1size != $f2size)
               {
                  $diff[2][] = "${append}${file}";
                  $return = true;
               }
               else
               {
                  //проверка хеша
                  $f1md5 = md5(implode("\r\n",file($f1name)));
                  $f2md5 = md5(implode("\r\n",file($f2name)));
                  if($f1md5 != $f2md5)
                  {
                     $diff[2][] = "${append}${file}";
                     $return = true;
                  };
               }
            }
            else
            {
               if(count($child_changes) || !is_dir($f1name))
               {
                  $diff[0][] = "${append}${file}";
                  $return = true;
               }
            };
         }
         closedir($hd);
      
      }

      //проверяем, есть ли в новой папке уникальные файлы
      $hd = @opendir($dir2.$append);
      if($hd === false)
      {
         return $changes;
      }

      $file='';
      while(false !== ($file = readdir($hd)))
      {
         if(($file == '.') || ($file == '..'))
            continue;
         $f1name = $dir1.$append.$file;
         $f2name = $dir2.$append.$file;
         //UI_ln($f2name);
         if(!file_exists($f1name))
         {
            if(is_dir($f2name))
            {
               if($this->makeDirDiff($dir1,$dir2,$diff,$append.$file.'/'))
               {
                  $return = true;
               }
            }
            else
            {
               $diff[1][] = "${append}${file}";
               $return = true;
            }
         };
      }
      closedir($hd);
      return $return;
   }
   
   //==============================================================================
   function getSize($dirName, $contents)
   //==============================================================================
   //IN : 
   // $dirName - (string) - путь к базовой папке
   // $contents - array-массив, содержащий имена файлов в папке (и во всех подпапках).
   //OUT : имя функции - (int) суммарный размер файлов в байтах 
   {
      //UI_ln('CFileSystem getSize()');
      $totalSize = 0;
      for ($i=0; $i<count($contents); $i++)
      {
         $fPath = $contents[$i];
         $fullFPath = $dirName . '/' . $fPath;
         if (is_dir($fullFPath))
         {
            continue;
         };
         
         $size = filesize($fullFPath);
         //UI_echo('CFileSystem getSize() $fPath', $fPath);
         //UI_echo('CFileSystem getSize() $size', $size);
         $totalSize += $size;
      };
      
      return $totalSize;
   }
   
}
?>