<?php
$Field=explode("|",$Th_Col);
$Count=count($Field);
$ColsNumber=$ColsNumber==""?$Count/2:$ColsNumber;//行底色着色列数 $ColsNumber为空时，整行选定着色 ewen 2013-03-06
$widthArray=array();
for ($i=0;$i<$Count;$i++){
	$i=$i+1;
	$widthArray[]=$Field[$i];
	$tableWidth=$tableWidth+$Field[$i];
	}
$tableMenuS=$tableWidth-200;
//还要关注modelfuction.php 中的标题显示  function List_Title($Th_Col,$Sign,$Height){
if(isFireFox()==1){	 //是FirFox add by zx 2011-0326  兼容IE,FIREFOX
	//echo "FireFox";
	$tableWidth=$tableWidth+$Count*2;
}

if (isSafari6()==1){
   $tableWidth=$tableWidth+ceil($Count*1.5)+1; 
}


if (isGoogleChrome()==1){
	$tableWidth=$tableWidth+ceil($Count*1.5);
}
	
if($From=="slist"){
   	$CencalSstr="<input name='CencalS' type='checkbox' id='CencalS' value='1' checked onclick='javascript:ToReadPage(\"$nowWebPage\",\"$Pagination\")'><LABEL for='CencalS'>查询结果</LABEL>";
	// add by zx 20100809 
	//if (($SearchRows=="") && ($searchtable!="") && ($search!="") ){  //来自快速搜索
	if ($FromSearch=="FromSearch") {  //来自快速搜索
			$Arraysearch=explode("|",$searchtable);
			$TAsName=$Arraysearch[1];
			$TField=$Arraysearch[2];
			$SearchRows=" AND $TAsName.$TField like '$search%'";
		}
  	}
else{
	$SearchRows="";
	}
$Page=$Page==""?1:$Page;
if($Pagination==1){
	$Pagination1="selected";
	$PageSTR=($Page-1)*$Page_Size.",".$Page_Size;
	$PageSTR="LIMIT ".$PageSTR;
	}
else{
	$Pagination0="selected";
	$Page=1;
	$PageSTR="";
	}
echo"<body onkeydown='unUseKey()' oncontextmenu='event.returnValue=false' onhelp='return false;'>
<form name='form1' id='checkFrom' enctype='multipart/form-data' method='post' action=''>
<input name='funFrom' type='hidden' id='funFrom' value='$funFrom'>
<input name='fromWebPage' type='hidden' id='fromWebPage' value='$nowWebPage'>
<input name='From' type='hidden' id='From' value='$From'>
<input name='OrderAction' type='hidden' id='OrderAction' value='$OrderAction'>
<div class='div-select' style='width:$tableWidth;height:27px;'>
<table border='0' width='$tableWidth' cellpadding='0' cellspacing='0' bgcolor='#F5F5F5'>
  <tr>
    <td class='' id='menuT1' width='$tableMenuS'>";
//求和sumAmount
$MergeRows=$MergeRows==""?0:$MergeRows;
$sumCols=$sumCols==""?"":$sumCols;
echo"<input name='sumCols' type='hidden' id='sumCols' value='$sumCols'><input name='MergeRows' type='hidden' id='MergeRows' value='$MergeRows'>";

include "../model/subprogram/read_model_right.php";
?>