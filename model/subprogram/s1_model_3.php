<?php
$isPage=$isPage==""?0:$isPage;
$Pagination=$Pagination==""?$isPage:$Pagination;	//默认分页方式:1分页，0不分页
$Field=explode("|",$Th_Col);
$Count=count($Field);
$widthArray=array();
for ($i=0;$i<$Count;$i++){
	$i=$i+1;
	$widthArray[]=$Field[$i];
	$tableWidth=$tableWidth+$Field[$i];
	}
if(isFireFox()==1){	 //是FirFox add by zx 2011-0326  兼容IE,FIREFOX
	//echo "FireFox";
	$tableWidth=$tableWidth+$Count*2;
}

if (isSafari6()==1){
   $tableWidth=$tableWidth+ceil($Count*1.5)+1;
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
echo"	
<script type=text/javascript>window.name='win_test'</script><BASE target=_self>
<body onload='closeLoading()' onkeydown='unUseKey()' oncontextmenu='event.returnValue=false' onhelp='return false;'>
<form name='form1' enctype='multipart/form-data' method='post' action='' target='win_test'>";
//echo "$Parameter  --- <br>";
//想把变量带过来，必须在_s1中加入,可见cg_cgdmain_s1.php
//$Parameter.=",CompanyId,$CompanyId,BuyerId,$BuyerId";  //这几个要带过去，也就是要带到 ,可见cg_cgdmain_s2.php,加入,可见cg_cgdmain_s1.php
PassParameter($Parameter);
echo"<table border='0' width='$tableWidth' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF'>
  <tr style='background-color: #f2f3f5;'>
    <td class='timeTop' id='menuT1' width='$tableMenuS'>";
//求和sumAmount
$MergeRows=$MergeRows==""?0:$MergeRows;
$sumCols=$sumCols==""?"":$sumCols;
echo"<input name='sumCols' type='hidden' id='sumCols' value='$sumCols'><input name='MergeRows' type='hidden' id='MergeRows' value='$MergeRows'>";
?>