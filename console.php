<?php 
/**
 * cli-��������� ������� ��������� getTree
 * 
 *  Usage: getTree -i=<srcFolder> -o=<destFolder>
 *  
 *  srcFolder - ���� � �����, ������� ����� ��������������
 *  destFolder - ���� � �����, ��� ����� ������� ���������� � ����� (srcFolder)
 */



error_reporting(E_ALL);//E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR);


include_once('lib/lib.h.php');
//include_once("lib/IO/CStdOut.h.php");
$_IO_br = "\n";

UI_ln("getTree.h.php");
//include_once('./BatFile.h');
/*


set_time_limit (BACKUP_EXECUTION_TIMOUT);
//set_time_limit (2);
//initApp();

//correctPath($_argv[1]);
//correctPath($_argv[2]);
//correctPath($_argv[3]);
//UI_ln("mkdiff-main");
//UI_echo('$_argc', $_argc);

$curDir = getCurDir();
$sourceFolder        = getParam1();
$archiveBaseFolder   = getParam2();
$projectName         = getParam3();
$archiveName         = getParam4();
$echo                = getParam(5);

UI_ln("backup '$sourceFolder' ...");
if ($echo == "1")
{
   //UI_ln("====== backup ========");
   UI_echo('$curDir', $curDir);
   UI_echo('$sourceFolder', $sourceFolder);
   UI_echo('$archiveBaseFolder', $archiveBaseFolder);
   UI_echo('$projectName', $projectName);
   UI_echo('$archiveName', $archiveName);
};

if (
      ($sourceFolder == "") ||
      ($archiveBaseFolder == "") ||
      ($projectName == "") ||
      ($archiveName == "")
   )
{
   UI_ln("\nUsage: backup <sourceFolder> <archiveBaseFolder> <projectName> <archiveName>");
   die();
};
      
if(!file_exists($sourceFolder))
{
   UI_ln("directory '$sourceFolder' is not found");
   die();
};

if(!file_exists($archiveBaseFolder))
{
   UI_ln("directory '$archiveBaseFolder' is not found");
   die();
};

main_do($curDir, $sourceFolder, $archiveBaseFolder, $projectName, $archiveName, $echo);


// ============================================================================
function main_do($curDir, $sourceFolder, $archiveBaseFolder, $projectName, $archiveName, $echo)
// ============================================================================
{
   //UI_ln("main_do()");
   $workDir = getWorkDir();
   
   $dateS = date("Y-m-d");
   //$dateS2 = "($dateS)";
   //UI_echo('main_do() $dateS2', $dateS2);
   
   //������� ����� � ��������
      $fs = new CFileSystem("CFileSystem");
      $archivesFolder = $archiveBaseFolder . "/" . $projectName;
      if (!file_exists($archivesFolder))
      {
         $code = $fs->mkDir($archivesFolder);
         if (!$code)
         {
            UI_ln("error creating directory '$archivesFolder'");
            die();
         };
      };
   // /������� ����� � ��������
      
   
   
   $availableIndex = getArchiveIndexInThisDate($archiveBaseFolder, $projectName, $archiveName, $dateS);
   $fullBackupDirName = $archiveBaseFolder . "/" . $projectName . "/" . "$archiveName($dateS)-$availableIndex";
   if ($echo == "1")
   {
      UI_echo('$availableIndex', $availableIndex);
      UI_echo('$fullBackupDirName', $fullBackupDirName);
   };
   $code = $fs->copy($sourceFolder, $fullBackupDirName, true);
   if (!$code)
   {
      UI_ln("error copying '$sourceFolder' into '$fullBackupDirName'");
      die();
   };
}

function getArchiveIndexInThisDate($archiveBaseFolder, $projectName, $archiveName, $dateS)
{
   //��������� ������ ��������� ������ ������ �� ���� ($dateS)
   //���� � ����� ������� ������� ($projectName) ���� ����� � ��������� "$archiveName($dateS)-1", 
   //�� ����������� ������� ����� "$archiveName($dateS)-2", � �.�.
   
   $exitNow = false;
   $currentBackupIndex = "1";
   $maxCounter = 50;
   while (!$exitNow)
   {
      $localBackupName = "$archiveName($dateS)-$currentBackupIndex";
      $fullBackupDirName = $archiveBaseFolder . "/" . $projectName . "/" . $localBackupName;
      //UI_echo('$fullBackupDirName', $fullBackupDirName);
      
      if (!file_exists($fullBackupDirName))
      {
         //������ � ����� ��������� ���
         break;
      };
      
      $currentBackupIndex++;
      
      if ($currentBackupIndex > $maxCounter)
      {
         UI_ln("too many archives in folder $archiveBaseFolder/$projectName in date $archiveName($dateS)");
         die();
      };
   };
   
   return $currentBackupIndex;
}
/*
// ============================================================================
function createArchiveDir($diffDir)
// ============================================================================
{
   $fs = new CFileSystem("CFileSystem");
   if (file_exists($diffDir))
   {
      $fs->unlink($diffDir);
      if (file_exists($diffDir))
      {
         UI_ln("cannot delete dir '$diffDir'");
         die();
      };    
   };
   
   $fs->mkDir($diffDir);
}

// ============================================================================
function writeCommandsFile($fileMA, $diffDir)
// ============================================================================
{
   //UI_ln("==============");
   $commandsS = "";
   for ($i=1; $i<=$fileMA->getRecordsNumber(); $i++)
   {
      $fName = $fileMA->getVal(APP_LIB_DIFF_F_NAME, $i);
      $isDir = $fileMA->getVal(APP_LIB_DIFF_IS_DIR, $i);
      $result = $fileMA->getVal(APP_LIB_DIFF_RESULT, $i);
      $folder = $fileMA->getVal(APP_LIB_DIFF_FOLDER, $i);
      
      //UI_echo('$fName', $fName);
      //UI_echo('$isDir', $isDir);
      //UI_echo('$result', $result);
      //UI_echo('$folder', $folder);
      $zpt = "";
      if ($i > 1)
      {
         $zpt = "\n";
      };
      
      $lineS = "$zpt$isDir\t$result\t$folder\t$fName";
      
      $commandsS .= $lineS;
      //UI_ln($lineS);
      
   };
   
   //������ ���������� ����� �� ����
   $commandsFName = $diffDir . "/commands.txt";
   $f = new CFile("CFile");
   $code = $f->open($commandsFName, SYS_IO_FL_WRITE);
   if (!$code)
   {
      UI_ln("cannot write into the file '$commandsFName'");
      die();
   };
   
   $f->ln($commandsS);
   $f->close();
}

// ============================================================================
function createNewFilesDir($diffDir)
// ============================================================================
{
   $newFilesFolder = $diffDir . "/newfiles";
   
   $fs = new CFileSystem("CFileSystem");
   if (file_exists($newFilesFolder))
   {
      $fs->unlink($newFilesFolder);
      if (file_exists($newFilesFolder))
      {
         UI_ln("cannot delete dir '$newFilesFolder'");
         die();
      };    
   };
   
   $fs->mkDir($newFilesFolder);
}

// ============================================================================
function copyNewFiles($fileMA, $newDir, $diffDir, $echo)
// ============================================================================
{
   $newFilesFolder = $diffDir . "/newfiles";
   //UI_ln("==============");
   $newDirsAr = array();
   $newFilesAr = array();
   $ONLY_IN = "only in";
   $MORE_RECENT = "morerec";
   
   for ($i=1; $i<=$fileMA->getRecordsNumber(); $i++)
   {
      $fName = $fileMA->getVal(APP_LIB_DIFF_F_NAME, $i);
      $isDir = $fileMA->getVal(APP_LIB_DIFF_IS_DIR, $i);
      $result = $fileMA->getVal(APP_LIB_DIFF_RESULT, $i);
      $folder = $fileMA->getVal(APP_LIB_DIFF_FOLDER, $i);
      
      //UI_echo('$isDir', $isDir);
      //UI_echo('$result', $result);
      //UI_echo('$folder', $folder);
      if ($folder != "[new]")
      {
         continue;
      };
      
      //UI_echo('$fName', $fName);
      if ($isDir == "1")
      {
         $newDirsAr[] = $fName;
      }
      else
      {
         $newFilesAr[] = $fName;
      };
      
      $lineS = "$zpt$isDir\t$result\t$folder\t$fName";
      
      //$commandsS .= $lineS;
      //UI_ln($lineS);
      
   };
   
   //������ �����
   //UI_echo('count($newDirsAr)', count($newDirsAr));
   //UI_echo('count($newFilesAr)', count($newFilesAr));
   $fs = new CFileSystem("CFileSystem");
   for ($i=0; $i<count($newDirsAr); $i++)
   {
      $dirName = $newDirsAr[$i];
      $fullDirName = $newFilesFolder . "/" . $dirName;
      //UI_echo('$dirName', $dirName);
      //UI_echo('$fullDirName', $fullDirName);
      if ($echo == "1")
      {
         UI_ln($dirName);
      };
      $code = $fs->mkDir($fullDirName);
      if ($code != true)
      {
         UI_ln("error creating dir '$fullDirName'");
         die();
      };
   };
   
   for ($i=0; $i<count($newFilesAr); $i++)
   {
      $fName = $newFilesAr[$i];
      
      $fullSrcFName = $newDir . "/" . $fName;
      $fullDestFName = $newFilesFolder . "/" . $fName;
      //UI_echo('$fName', $fName);
      //UI_echo('$fullSrcFName', $fullSrcFName);
      //UI_echo('$fullDestFName', $fullDestFName);
      if ($echo == "1")
      {
         UI_ln($fName);
      };
      //copy($source, $dest, $overwrite = false)
      $code = $fs->copy($fullSrcFName, $fullDestFName, true);
      if ($code != true)
      {
         UI_ln("error copying file '$fullSrcFName' into '$fullDestFName'");
         die();
      };
   };
}


// ============================================================================
function diffScanFolder(&$fileMA, $folder1, $folder2, $folder1Name, $compareFiles)
// ============================================================================
//IN
//    ($fileMA) - MainArray   - ��������������������� ������, � ������� ��������� ����������
//    ($folder1) - string     - ���� � ������� ����� �� ���� �� �����
//    ($folder2) - string     - ���� � ������� ����� �� ���� �� �����
//    ($folder1Name) - string - ���, ������� ����� � ������� ����� � ����������� ���������
//    ($compareFiles) -  bool - ���� ����������� ����� � ����� ������ ����, ���������� �� �����
{
   $fs = new CFileSystem("CFileSystem");
   
   $moreRecent = "morerec";
   
   //����� �1 - ������
   //���� �����, ���������� ��� ������ �����, � ����� ��������� ������������ ����� �� ������������
   $contentsAr = $fs->getDirContents($folder1, true);
   //UI_echo('count($contentsAr)', count($contentsAr));
   //UI_echo('count($contentsAr)', count($contentsAr));
   $fileNo = $fileMA->getRecordsNumber();
   for ($i=0; $i<count($contentsAr); $i++)
   {
      $fileName = $contentsAr[$i];
      
      $f1name = $folder1.$fileName;
      
      $isDir = (int) is_dir($f1name);
      //if (is_dir($f1name))
      //{
         //����� �� ���������
         //continue;   
      //};
      $f2name = $folder2.$fileName;
      //UI_echo('$f1name', $f1name);
      //UI_echo('$f2name', $f2name);
      
      if(file_exists($f2name))
      {
         if (!$compareFiles)
         {
            //�� ���������� ������������ �����
            continue;   
         };
         //UI_ln("$f2name ����������");
         $f1size = filesize($f1name);
         $f2size = filesize($f2name);
         $f1time = filemtime($f1name);
         $f2time = filemtime($f2name);

         $mod = ($f2time < $f1time)?"[old]":"[new]";
         if ($folder1Name == "[new]")
         {
            $mod = ($f2time < $f1time)?"[new]":"[old]";
         };
         
         //�������� �������
         if($f1size != $f2size) 
         {
            //UI_echo('$fileName', $fileName);
            //UI_ln("������ �� ������");
            //$changes[] = "${append}${file}\tdifferent\t$mod\tis more recent\r\n";
            $fileNo++;
            $fileMA->setVal(APP_LIB_DIFF_F_NAME, $fileNo, $fileName);  
            $fileMA->setVal(APP_LIB_DIFF_RESULT, $fileNo, $moreRecent);  
            $fileMA->setVal(APP_LIB_DIFF_FOLDER, $fileNo, $mod);  
            $fileMA->setVal(APP_LIB_DIFF_IS_DIR, $fileNo, "$isDir");  
         }
         else 
         {
            //�������� ����
            $f1sha1 = md5(implode("\r\n",file($f1name)));
            $f2sha1 = md5(implode("\r\n",file($f2name)));
            if($f1sha1 != $f2sha1) 
            {
               //UI_echo('$fileName', $fileName);
               //UI_ln("��� �� ������: $mod is more recent");
               //$changes[] = "${append}${file}\tdifferent\t$mod\tis more recent\r\n";
               $fileNo++;
               $fileMA->setVal(APP_LIB_DIFF_F_NAME, $fileNo, $fileName);  
               $fileMA->setVal(APP_LIB_DIFF_RESULT, $fileNo, $moreRecent);  
               $fileMA->setVal(APP_LIB_DIFF_FOLDER, $fileNo, $mod);  
               $fileMA->setVal(APP_LIB_DIFF_IS_DIR, $fileNo, "$isDir");  
            };
         }
      }
      else
      {
         //if(count($child_changes) || !is_dir($f1name)){
         //UI_echo('$fileName', $fileName);
         //UI_ln("������ � ������ �����");
            //$changes[] = "${append}${file}\tonly in \t[old]\r\n";
         //}
         $fileNo++;
         $fileMA->setVal(APP_LIB_DIFF_F_NAME, $fileNo, $fileName);  
         $fileMA->setVal(APP_LIB_DIFF_RESULT, $fileNo, "only in");  
         $fileMA->setVal(APP_LIB_DIFF_FOLDER, $fileNo, $folder1Name);  
         $fileMA->setVal(APP_LIB_DIFF_IS_DIR, $fileNo, "$isDir");  
      };
   };
   $fileMA->setRecordsNumber($fileNo);
}
   

*/



?>
