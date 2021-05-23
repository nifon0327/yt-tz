<?php   
//$DataIn.电信---yang 20120801
if ($Picture==1){
    $f=anmaIn($ProcessId.".jpg",$SinkOrder,$motherSTR); 
    $ProcessName="<span onClick='OpenOrLoad(\"$d\",\"$f\")' style='CURSOR: pointer;color:#F00;'>$ProcessName</span>";
 }
 
 $Gfile="&nbsp;";
 $CheckFileSql=mysql_query("SELECT FileName,Type FROM $DataIn.process_file WHERE ProcessId='$ProcessId' AND Type='1' LIMIT 1",$link_id);
  if($CheckFileRow=mysql_fetch_array($CheckFileSql)){
      $FileName=$CheckFileRow["FileName"];
      $Gfile=anmaIn($FileName,$SinkOrder,$motherSTR);
      $Gfile="<a href=\"../admin/openorload.php?d=$d&f=$Gfile&Type=cut&Action=6\" target=\"download\"><img src='../images/down.gif' alt='点击下载' width='18' height='18' style='border:0'></a>";
  }
?>