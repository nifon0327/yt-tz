<?php   
$nowWebPage=$funFrom."_read";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Field=explode("|",$Th_Col);
$Count=count($Field);
$widthArray=array();
for ($i=0;$i<$Count;$i++){
	$i=$i+1;
	$widthArray[]=$Field[$i];
	$tableWidth=$tableWidth+$Field[$i];
	}

if($From=="slist"){
   	$CencalSstr="<input name='CencalS' type='checkbox' id='CencalS' value='1' checked onclick='javascript:ToReadPage(\"$funFrom\",0)'><LABEL for='CencalS'>查询结果</LABEL>";
  	}
else{
    $From="read";
	}
//ҳ
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
<form name='form1' enctype='multipart/form-data' method='post' action=''>
<input name='funFrom' type='hidden' id='funFrom' value='$funFrom'>
<input name='From' type='hidden' id='From' value='$From'>
<table border='0' width='$tableWidth' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF'>
  <tr>
    <td class='timeTop' id='menuT1' width='$tableMenuS'>";
?>