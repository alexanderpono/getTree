<?
// ============================================================================
//    PURPOSE:             
//
//    FUNCTIONAL AREA:     
//    NAME:                
//    VERSION:             
//    AUTHORS:             
//    DESIGN REFERENCE:    
//    MODIFICATION:        
//    MODIFICATION:        
// ============================================================================
// =================================================================== INCLUDES
// =================================================================== SYNOPSIS

include_once("lib/IO/CIO/CIO.h.php");
include_once("lib/IO/CStdOut/CStdOut.h.php");
include_once("lib/CDebugClass/CDebugClass.h.php");
include_once("lib/CFileSystem/CFileSystem.h.php");

/*
include_once("lib/CDebugClass.h");
include_once("lib/CIO.h");
include_once("lib/SYS.h");
include_once("lib/CMainArray.h");
include_once("lib/CList.h");
include_once("lib/CT_file.h");
include_once("lib/lib.h");
include_once("lib/CSyntax/CSyntax.h");
include_once("lib/CLex/CLex.h");
include_once("lib/CDB_XMLCore/CDB_XMLCore.h");
include_once("lib/CDB_MYCore/CDB_MYCore.h");
include_once("lib/CFile.h");
include_once("lib/CFW5_XML_file.h");
include_once("lib/CFW5_XML_table.h");
include_once("lib/CFW_XML_table.h");
include_once("lib/CFileSystem.h");

//verbose mode if not commented
//define('VERBOSE','1');
global $_IO_recodeFunction;
//$_IO_recodeFunction = "SYS_convertWinToDos";


// ============================================================================
function correctSlashes($s)
// ============================================================================
{
   return str_replace("/", "\\", $s);
}



// ================================================================== FUNCTIONS
function callWindiff($dirsAr, $exec=false, $recalc=true)
// ================================================================== FUNCTIONS
{
   $s = "";

   global $_output_dir, $_curDir;
   //UI_echo('$_curDir', $_curDir);
   
   $outDirName = $_output_dir;
   
   if(!file_exists($outDirName))
      mkdir($outDirName);

   for ($i=0; $i<(count($dirsAr) - 1); $i++)
   {
      $dir1Name = $dirsAr[$i];
      $dir2Name = $dirsAr[$i + 1];

      $fName = basename($dir1Name) . "_" . basename($dir2Name) . ".tmp3";

      if (file_exists("$outDirName/$fName"))
      {
         if (!$recalc)
         {
            continue;
         };
      };
      
      $handle = fopen("$outDirName/$fName", "w");
      $changes = array();
      $changes = diff($dir1Name, $dir2Name);
      foreach($changes as $ch)
      {
         fwrite($handle,correctSlashes($ch));
      };
      fclose($handle);
   };
}

// ================================================================== FUNCTIONS
function diff($dir1, $dir2, $append='')
// ================================================================== FUNCTIONS
{
   $changes = array();
   $dir1 .= (substr($dir1,-1) != '/')?'/':'';
   $dir2 .= (substr($dir2,-1) != '/')?'/':'';
   $hd = @opendir($dir1.$append);
   if($hd !== false)
   {
      //ищем файлы, уникальные для старой папки, а также проверяем существующие файлы на идентичность
      while(false !== ($file = readdir($hd)))
      {
         if(($file == '.') || ($file == '..'))
            continue;
         
         $f1name = $dir1.$append.$file;
         $f2name = $dir2.$append.$file;
        // UI_ln("В старой. Сравниваем $f1name и  $f2name");
         $child_changes = array();
         //UI_ln($f1name);
         if(is_dir($f1name))
         {
            $child_changes = diff($dir1,$dir2,$append.$file.'/');
            $changes = array_merge($changes, $child_changes);
         };
         if(file_exists($f2name))
         {
            //UI_ln("$f2name существует");
            $f1size = filesize($f1name);
            $f2size = filesize($f2name);
            $f1time = filemtime($f1name);
            $f2time = filemtime($f2name);

            $mod = ($f2time < $f1time)?"[old]":"[new]";

            //проверка размера
            if($f1size != $f2size) 
            {
               //UI_ln("размер не совпал");
               $changes[] = "${append}${file}\tdifferent\t$mod\tis more recent\r\n";
            }
            else 
            {
               //проверка хеша
               $f1sha1 = md5(implode("\r\n",file($f1name)));
               $f2sha1 = md5(implode("\r\n",file($f2name)));
               if($f1sha1 != $f2sha1) 
               {
                  //UI_ln("хеш не совпал");
                  $changes[] = "${append}${file}\tdifferent\t$mod\tis more recent\r\n";
               };
            }
         }
         else
         {
            if(count($child_changes) || !is_dir($f1name)){
               $changes[] = "${append}${file}\tonly in \t[old]\r\n";
            }
         };
      }
      closedir($hd);
   }
   
   //проверяем, есть ли в новой папке уникальные файлы
  // UI_ln("Открываем папку $dir2.$append");
   $hd = @opendir($dir2.$append);
   if($hd !== false)
   {
      $file='';
      while(false !== ($file = readdir($hd)))
      {
         if(($file == '.') || ($file == '..'))
            continue;
         
         $f1name = $dir1.$append.$file;
         $f2name = $dir2.$append.$file;
         //UI_ln("В новой. сравниваем $f1name и $f2name");
         $child_changes = array();
         //UI_ln($f2name);
         if(!file_exists($f1name))
         {
          //  UI_ln("$f1name  не существует");
            if(is_dir($f2name))
            {
          //     UI_ln("$f2name  - папка! смотрим в ней");
               $changes[] = $append."$file\tonly in \t[new]\r\n";
               $child_changes = diff($dir1,$dir2,$append.$file.'/');
               $changes = array_merge($changes, $child_changes);
            }
            else
            {
               $changes[] = $append."$file\tonly in \t[new]\r\n";
            }
         };
      }
      closedir($hd);
   }
   return $changes;
}

// ============================================================================
function getDir($fullFilePath, $delim = "\\")
// ============================================================================
//выкусывает из строки путь к папке
{
   $pos = strrpos($fullFilePath, $delim);
   $dirPath = substr($fullFilePath, 0, $pos);

   return $dirPath;
}

// ============================================================================ FUNCTIONS
function getAccount($project, $type="mysql")
// ============================================================================ FUNCTIONS
// получает данные о проекте из нужного backend'a
// @return : array("ftp", "login", "pwd", "http");
// ftp - путь к данным на фтп-сервере вида ftp://ftp.server.com/path/to/dir/, 
// путь задается относительно директории, в которую пользователь попадает после логина
// login - логин к ftp
// pwd - пароль к ftp
// http - путь к данным, доступный по web вида http://server.com/path/to/dir/
{
   if($type == "mysql")
   {
      return getAccountMysql($project);
   }
   else
   {
      return getAccountXML($project);
   }
}

// ============================================================================ FUNCTIONS
function getAccountMysql($project)
// ============================================================================ FUNCTIONS
// получает данные о проекте из mysql-базы
{
   global $_curDir;

   $db = new CDB_MYCore("CDB_MYCore");
   $db->open(APP_DB_NAME, APP_DB_HOST, APP_DB_USER, APP_DB_PWD);

   $account = $db->selectIntoMainAr("SELECT * FROM ftpaccount WHERE `project`= '$project'", APP_DB_DIR . "/select_account.xml");
   //хаки, необходимый для обхода неработающего ftp_nlist
   $res_http = $account->getVal(ACC_HTTP);
   if(!$res_http)
   {
      //при отсутствии записи в базе считаем, что путь на ftp и http одинаковый
      $res_http = str_replace("ftp://","http://",$account->getVal(ACC_FTP)); 
   };

   return array("ftp" => $account->getVal(ACC_FTP),
                "login" => $account->getVal(ACC_LOGIN),
                "pwd" => $account->getVal(ACC_PWD),
                "http" => $res_http,
                "port" => $account->getVal(ACC_PORT));
}
// ============================================================================ FUNCTIONS
function getAccountXML($project)
// ============================================================================ FUNCTIONS
// получает данные о проекте из xml-базы
{
   // global $_curDir;
   // define ("APP_DB_DIR", "$_curDir/data");

   $db = new CDB_XMLCore("CDB_XMLCore");
   $db->open(APP_DB_DIR);

   $account = $db->selectIntoMainAr("SELECT * FROM ftpaccount WHERE `project`= '$project'", APP_DB_DIR . "/select_account.xml");
   
   $res_http = $account->getVal(ACC_HTTP);
   if(!$res_http)
   {
      //при отсутствии записи в базе считаем, что путь на ftp и http одинаковый
      $res_http = str_replace("ftp://","http://",$account->getVal(ACC_FTP)); 
   };
   
   return array("ftp" => $account->getVal(ACC_FTP),
                "login" => $account->getVal(ACC_LOGIN),
                "pwd" => $account->getVal(ACC_PWD),
                "http" => $res_http,
                "port" => $account->getVal(ACC_PORT));
}

*/

?>
