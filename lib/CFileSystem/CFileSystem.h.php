<?php
// ============================================================================
//    PURPOSE:             ����� CFileSystem ������������� ��������� ��������������
//                         � �������� ��������
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
   //���������� ������� ������� $dir
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
   //���������� ������� ������� $dir � ���� ����� �� ������ � ���� ������� ���� ������������� (���� ���������� ���� $write)
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
            //�� ��������� � ��� ����� ����, ������� ���� �������� ����� ��������. ���� ���� 100% ����������
            continue;
         };

         if (file_exists($fullDirName))
         {
            //���� ����� ���� ����������
            continue;
         };
         
         if (is_writeable(dirname($fullDirName)))
         {
            //������������ ����� ����� ($fullDirName) �������� ��� ������
            //��� ������
         }
         else
         {
            $msg = '<!--CFileSystem mkDir2() ' . dirname($fullDirName) .' �� �������� ��� ������!' . "-->";
            print ($msg . "\n");
            //������� ���������� ����� ������� �� ������ � ���� �����
            @chmod(dirname($fullDirName), 0777);
            if (!is_writeable(dirname($fullDirName)))
            {
               $msg = ('<!--CFileSystem mkDir2() �� ������� ���������� ����� �� ������ � ����� ' . dirname($fullDirName) . "-->");
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
   //���������, ����� �� ����������
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
   //������� ���������� ��������
   //���������� true ���� ������ �� ���� ������, ������� �� ������� �������
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
      
      //������ �� ����������� ������
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
            //����� ��������, ���� �� ����� �� ������ � ���������� (���� ����, �� ���� ����� �� �������� �����)
            if(!unlink($file))
            {
               //UI_ln("���� �� ������:$file");
               //APP_LOG_ln("���� �� ������:$file");
               $notDeleted[] = $file;
            };
         }
         elseif($recursive && is_dir($file))
         {
            $resultAr = $this->clearDir($file, $recursive);
            $notDeleted = array_merge($notDeleted, $resultAr[2]);
            
            if(!@rmdir($file))
            {
               //APP_LOG_ln("���� �� ������:$file");
               //UI_ln("����� �� �������:$file");
               $notDeleted[] = $file;
            }
         }
         else
         {
            //APP_LOG_ln("���� �� ������:$file");
            //UI_ln("���� �� ������:$file");
            $notDeleted[] = $file;
         }
      };
      
      return array(true, "", $notDeleted);
   }

   
   //TODO:������ ���������� bool
   //==============================================================================
   function dirCopy($basePath, $source, $dest, $overwrite = false, $ext= array("*"))
   //==============================================================================
   // �������� ���������� �� ������ �������� � ������
   // ������ $ext �������� ���������� ������, ������� ������ ������������
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
                        //UI_ln("������ ����������� � $fileDestPath");
                        //APP_LOG_ln('���� '.$path.' �� ����� ���� ����������; �������� ��������, ��� ������������ ���� �������.');
                        //$this->chmod($fileDestPath, 0, false, true);
                        //if(!copy($basePath . $path, $basePath . $dest . '/' . $file))
                        //{
                        //};
                        
                        $messages .= ('���� '.$path." �� ����� ���� ���������� � $dest; �������� ��������, ��� ������������ ���� �������.<br>\n"); 
                        
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
   //�������� ���� $source � ���� $dest
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
   //���������� ������, ���������� ����� ���� ������ � ��������.
   //���� recursive = true, �� � ������ ���������� ���������� ������������
   //���� false � ������ �������
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
   //���������� ������, ���������� ����� ���� ������ � ��������(�������� �����).
   //���� recursive = true, �� � ������ ���������� ���������� ������������
   //���� false � ������ �������
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
   //������ �� ��������� ���� ����� � ������������ � mode
   //���� $addMode == true, �� ����� ����������� � �������
   //���������� true � ������ ������, false, ���� ��� ������ ��� ���������,
   //���� ������ �� ������, ����� �� ������� �������� �� �������
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
            //UI_ln("�� ������� ���������� ����� �� ����: $file");
            //APP_LOG_ln("�� ������� ���������� ����� �� ����: $file");
            $badFiles[] = $fullFName;
         }
      }
      
      return (count($badFiles)>0)?$badFiles: true;
   }
   
   //==============================================================================
   function move($source, $dest)
   //==============================================================================
   //��������������� ���� $source � ���� $dest
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
   //������� ������� ����. � ������ �������� ������� ����������
   {
      $result = true;
      if(!file_exists($target))
      {
         //APP_LOG_ln("��������� ���� $target ��� �� ����������");
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
            //APP_LOG_ln("������� $target ������� �� �������");
            $result = false;
         }
         return $result;
      }

      if(!@unlink($target))
      {
         //APP_LOG_ln("���� $target ������� �� �������");
         $result = false;
      }
      return $result;
   }

   //==============================================================================
   function makeDirDiff($dir1,$dir2,&$diff, $append='')
   //==============================================================================
   // ���������� ��� �������� $dir1 � $dir2
   //OUT:
   // ���������� true, ���� �������� ��������
   // ������ $diff[0] �������� ���� � ������, ���������� ��� $dir1
   // ������ $diff[1] �������� ���� � ������, ���������� ��� $dir2
   // ������ $diff[2] �������� ���� � ������, ������������� � $dir1 � $dir2
   // ������ $diff[3] �������� ��������� �� �������
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
            //������ ��������� ��������� ������ ���� ��� ����� ������� �� ������������ �����
            $diff[3][] = "open dir failed($dir1$append)";
         };
      }
      else {
         //���� �����, ���������� ��� ������ �����, � ����� ��������� ������������ ����� �� ������������
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

               //�������� �������
               if($f1size != $f2size)
               {
                  $diff[2][] = "${append}${file}";
                  $return = true;
               }
               else
               {
                  //�������� ����
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

      //���������, ���� �� � ����� ����� ���������� �����
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
   // $dirName - (string) - ���� � ������� �����
   // $contents - array-������, ���������� ����� ������ � ����� (� �� ���� ���������).
   //OUT : ��� ������� - (int) ��������� ������ ������ � ������ 
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