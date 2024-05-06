<?php

define( "DEFAULT_CONFIG_FILENAME", "config.json" );

$session = json_decode( file_get_contents( "session" )  );

$appConfig = null;

function loadConfigFile()
{
  global $appConfig;
  global $session;

  if (!file_exists( $session->configFile )) {
    file_put_contents( $session->configFile, "{}" );
  }
  $appConfig = json_decode( file_get_contents( 
      ($session->configFile !== null ?  
        $session->configFile : 
        getcwd() . __bslash() . DEFAULT_CONFIG_FILENAME ) ) );

  if (!isset( $appConfig->jboss_home )) {
    $appConfig->jboss_home = "";
  }

  if (!isset( $appConfig->files_to_replace )) {
    $appConfig->files_to_replace = array();
  }

  if (!isset( $appConfig->repository_home )) {
    $appConfig->repository_home = "";
  }

  if (!isset( $appConfig->remote_files_path )) {
    $appConfig->remote_files_path = "";
  }

  if (!isset( $appConfig->local_files_path )) {
    $appConfig->local_files_path = "";
  }
}

loadConfigFile();

function endl()
{
  return "\n";
}

function __bslash()
{
  return "\\";
}


function stringListToText( $arr ) {
  $result = "";
  foreach ($arr as $k => $v) {
    $result .= $v . endl();
  }
  return $result;
}


function textToList( $text ) {
  $result = array();
  $lines = explode( "\n", $text );
  foreach ($lines as $line) {
    array_push( $result, $line );
  }
  return $result;
}


// Does not support flag GLOB_BRACE
function rglob( $pattern, $flags=0 )
{
  $files = glob( $pattern, $flags );
  foreach (glob(dirname($pattern) . __bslash() . '*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
    $files = array_merge( [], ...[$files, rglob( $dir . __bslash() . basename($pattern), $flags )] );
  }
  return $files;
}


$repositoryHome = "C:\\Users\\2039864\\AppData\\Local\\Workspace\\Repos\\FlyupOps";

function getDirectory( $basePath, $directory )
{
  return str_replace( $basePath, "", $directory );
}

function buildRepositoryPaths( $fromPath, $wherePath )
{
  global $repositoryHome;
  $files = rglob( $fromPath, null );
  foreach ($files as $file) {
    if (is_dir( $file )) {
      print 'Creating dir ' . getDirectory( $repositoryHome, $file ) . endl();
//      print_r( pathinfo(  $file ) );
    }
    
  }
  exit;
}

//buildRepositoryPaths( $repositoryHome . "\*", null );




$branchFilesFolder = getcwd() . __bslash() . 'branchfiles';

$ftrFilename = getcwd() . __bslash() .  'ftr.txt';

$filesToReplace = file_get_contents( $ftrFilename );

$files = explode( endl(), $filesToReplace );

$finalFiles = array();

foreach ($files as $f) {
  $finalPath = str_replace( '${repositoryHome}', $repositoryHome, $f );
  //print $finalPath . endl();
  array_push( $finalFiles, $finalPath );
}



// copy first the branch files to branchFiles
/*
foreach ($finalFiles as $branchFile) {
  $pathinfo = pathinfo( $branchFile );
  print_r( $pathinfo );
  print 'copying ' . $branchFile .  ' to ' .

  $branchFilesFolder . str_replace( $repositoryHome, "", $branchFile ) . endl();
}*/


function deleteDirectory($dir) {
  if (!file_exists($dir)) {
      return true;
  }

  if (!is_dir($dir)) {
      return unlink($dir);
  }

  foreach (scandir($dir) as $item) {
      if ($item == '.' || $item == '..') {
          continue;
      }

      if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
          return false;
      }

  }

  return rmdir($dir);
}

function cleanJboss( $jbossHome )
{
  $jbossMainDirectories = array(
    trim( $jbossHome, "\\" ) );

  $deploymentsDirectory = "\\standalone\\deployments";
  $tmpDirectory = "\\standalone\\tmp";
  $dataDirectory = "\\standalone\\data";

  $directoriesToDelete = array();

  foreach ($jbossMainDirectories as $dir) {
    array_push(
      $directoriesToDelete, array(
        $dir . $deploymentsDirectory,
        $dir . $tmpDirectory,
        $dir . $dataDirectory ) );
  }

  foreach ($directoriesToDelete as $jbossHome) {
    foreach ($jbossHome as $dir) {
      deleteDirectory( $dir );
    }
  }
}


function backupBranchFiles( $branchPath, $filesDestination )
{
  global $appConfig;
  // backup branch files on remote filtes path
  $filesToBackup = $appConfig->files_to_replace;

  if (is_array( $filesToBackup )) {
    foreach ($filesToBackup as $file) {
      $source = $branchPath . $file;
      $dest = $filesDestination . $file;


      
      $dir = dirname( $dest ); 
      if (!is_dir( $dir )) {
        mkdir( $dir, 0777, true );
      }

      copy( $source, $dest );


    }
  }

}


function pointBranchToRemote( $branchPath, $remoteFilesDirectory )
{
  global $appConfig;
  // backup branch files on remote filtes path
  $filesForLocal = $appConfig->files_to_replace;

  if (is_array( $filesForLocal )) {
    foreach ($filesForLocal as $file) {
      $source = $remoteFilesDirectory . $file;
      $dest = $branchPath . $file;

      copy( $source, $dest );

    }
  }
}

function pointBranchToLocal( $branchPath, $localFilesDirectory )
{

  global $appConfig;
  // backup branch files on remote filtes path
  $filesForLocal = $appConfig->files_to_replace;

  if (is_array( $filesForLocal )) {
    foreach ($filesForLocal as $file) {
      $source = $localFilesDirectory . $file;
      $dest = $branchPath . $file;

      copy( $source, $dest );


    }
  }
}

function sendResponse( $response )
{
  header("HTTP/1.1 200 OK");    
  header('Content-Type: application/json; charset=utf-8');
  $result = [ 'response' => $response ];
  print json_encode($result);
  exit;
}

function saveConfigFile()
{
  global $session;
  global $appConfig;
  $session->configFile=$_POST['path'];
  file_put_contents( "session", json_encode( $session ) );
  loadConfigFile();
  print json_encode( $appConfig );
  exit;
}

function saveConfig()
{
  global $session;
  global $appConfig;

  $session->configFile=$_POST['path'];
  file_put_contents( "session", json_encode( $session ) );
  loadConfigFile();
  if ($appConfig !== null) {
    $appConfig->files_to_replace = textToList( str_replace("\r", "", $_POST['files_to_replace'] ) );
    $appConfig->jboss_home = $_POST['jboss_home'];
    $appConfig->repository_home = $_POST['repository_home'];
    $appConfig->remote_files_path = $_POST['remote_files_path'];
    $appConfig->local_files_path = $_POST['local_files_path'];

    file_put_contents( $session->configFile, json_encode( $appConfig ) );
    sendResponse($appConfig);
  }
}

if (isset( $_POST['action'] )) {
  switch ($_POST['action']) {
    case 'set_config_file':
      saveConfigFile();
      sendResponse('set_config_file');
      break;
    case 'clean_jboss':
      cleanJboss( $appConfig->jboss_home );
      sendResponse('clean_jboss');
      break;
    case 'refresh':
      cleanJboss( $appConfig->jboss_home );
      sendResponse('refresh');
      break;
    case 'point_to_local':
      pointBranchToLocal( $appConfig->repository_home, $appConfig->local_files_path );
      sendResponse('point_to_local');
      break;
    case 'point_to_remote':
      pointBranchToRemote( $appConfig->repository_home, $appConfig->remote_files_path );
      sendResponse('point_to_remote');
      break;
    case 'backup_branch':
      backupBranchFiles( $appConfig->repository_home, $appConfig->remote_files_path  );
      sendResponse('backup_branch');
      break;   
    case 'set_config':
      saveConfig();
      break;     

  }
}

else {
  include("page.php");
}



?>