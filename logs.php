<style>
    *{
        background-color: black;
        color: white;
    }
</style>
<?php

$myfile = fopen("logs.txt", "r") or die("Unable to open file!");
$myfile = fread($myfile,filesize("logs.txt"));
$datas = $myfile;
// fclose($myfile);

echo nl2br($datas);
