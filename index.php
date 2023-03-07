<?php

  // Get Rid of this in production!!!
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);

  $head = [];
  $indis = [];
  $fams = [];
  $handle = fopen('../familytree/gedcomfiles/backup.ged', 'r');

  $line = fgets($handle);

  while($line !== false){

    // $head will be an array of line not an array of arrays of lines
    if(strpos($line, " HEAD") !== false){
      $head[] = $line;
      while (
        ($line = fgets($handle)) !== false &&
        substr($line, 0, strpos($line, ' ')) != 0
      ) {
        $head[] = $line;
      }
    }

    if(strpos($line, " INDI") !== false){
      $arr = [];
      $arr[] = $line;
      while (
        ($line = fgets($handle)) !== false &&
        substr($line, 0, strpos($line, ' ')) != 0
      ) {
        $arr[] = $line;
      }
      $indis[] = $arr;
    }

    if(strpos($line, " FAM") !== false){
      $arr = [];
      $arr[] = $line;
      // $families[] = $line;
      while (
        ($line = fgets($handle)) !== false &&
        substr($line, 0, strpos($line, ' ')) != 0
      ) {
        $arr[] = $line;
      }
      $fams[] = $arr;
    }

    // this section ignores lines not corresponding to an array
    while (
      ($line = fgets($handle)) !== false &&
      substr($line, 0, strpos($line, ' ')) != 0
    ) {
      // do nothing with the lines
    }

  }



?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>
<body>
  <h1>Gedcom Reader</h1>
  <h2>Head</h2>
<?php
  foreach($head as $headLine){
    echo '<p>' . $headLine . '</p>';
  }
?>
  <h2>Individuals</h2>
<?php
  foreach($indis as $indiArray){
    foreach($indiArray as $indiLine)
    echo '<p>' . $indiLine . '</p>';
  }
?>
  <h2>Families</h2>
<?php
  foreach($fams as $famArray){
    foreach($famArray as $famLine){
      echo '<p>' . $famLine . '</p>';
    }
  }
?>
</body>
</html>
