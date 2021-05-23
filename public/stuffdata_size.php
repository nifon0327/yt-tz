<?php 
//步骤1$DataIn.电信---yang 20120801
include "../model/modelhead.php";

echo"<html><head><META content='MSHTML 6.00.2900.2722' name=GENERATOR>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<link rel='stylesheet' href='../model/css/read_line.css'>
<link rel='stylesheet' href='../model/css/sharing.css'>
<link rel='stylesheet' href='../model/keyright.css'>
<script src='../model/pagefun_Sc.js' type=text/javascript></script>
<script src='../model/checkform.js' type=text/javascript></script>
<script language='javascript' type='text/javascript' src='../model/DatePicker/WdatePicker.js'></script></head>";
//include "../model/subprogram/stuffimg_PfileUpLoad.php";	//扫描上传图档	
//步骤2：需处理
$upFlag=$_GET["ID"];
if ($upFlag!=""){
 $JobType=$upFlag;
}
$ColsNumber=6;
$tableMenuS=1000;
ChangeWtitle("$SubCompany 外箱配件装箱重量列表");
$funFrom="stuffdata";
$From=$From==""?"size":$From;

	$Th_Col="选项|55|序号|40|外箱规格|280|产品<br>最大重(g)|70|产品<br>最小重(g)|70|最大重产品|280|最小重产品|280|产品数|50";
	
//$Pagination=$Pagination==""?1:$Pagination;
//$Page_Size = 200;
//$ActioToS="1,2,3,4,5,7,8,107,13,40,98";
$nowWebPage=$funFrom."_read";

include "../model/subprogram/read_model_3.php";
/*
if($From!="slist"){
	$SearchRows=" AND A.TypeId='9040' AND A.Estate=1 AND A.Spec!='' ";
}
*/ 
  //echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页   </option><option value='0' $Pagination0>不分页</option></select>
 // 	$CencalSstr	";
  $searchtable="stuffdata|D|Spec|0"; //快速搜索的表名，字段名. 表名|别名|字段|1  1表示带Estate字段,其它值无
  include "../model/subprogram/QuickSearch.php";

//}	
  echo "<input name='AcceptText' type='hidden' id='AcceptText' value='$upFlag'>";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
if($NameRule!=""){
  echo "<table border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP:       break-word' bgcolor='#FFFFFF' width='$tableWidth' ><tr ><td height='15' class='A0011' ><span style='color:red'>命名规则:</span>$NameRule</td></tr></table>";
  }
  $NowYear=date("Y");
$NowMonth=date("m");
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql ="SELECT C.Forshort,A.ProductId,P.cName,P.eCode,P.TestStandard,P.Code,P.Weight,D.StuffCname 
		FROM $DataIn.pands A
		LEFT JOIN $DataIn.stuffdata D ON  D.StuffId=A.StuffId 
		LEFT JOIN $DataIn.productdata P ON A.ProductId=P.ProductId
		LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
		WHERE D.TypeId='9040' AND D.Estate=1 AND D.Spec!='' AND P.Estate>0 $SearchRows  order by D.StuffCname";
//		echo $mySql;
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
if($myRow = mysql_fetch_array($myResult)){
	$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
	$StuffCname=$myRow["StuffCname"];
	$NameArray=explode("cm", $StuffCname);
	$oldCName=$NameArray[0];
	$PMaxWeight=0; $PMinWeight=0;$pMaxCName="&nbsp;";$pMinCName="&nbsp;";$pTotals=0;
	do{
		$m=1;
		$StuffCname=$myRow["StuffCname"];
	    $NameArray=explode("cm", $StuffCname);
        $sName=$NameArray[0];
        
        $extraWeight=0;
        $productId=$myRow["ProductId"];
		$cName=$myRow["cName"];		
		$eCode=$myRow["eCode"];
		$Code=$myRow["Code"];
		$Weight=$myRow["Weight"];
		
		$extraWeight=0;
		include "../model/subprogram/weightCalculate.php";
	  if ($Weight>0){
		   $extraWeight=$extraWeight == "error"?"0":$extraWeight+($Weight*$boxPcs); 
	   } 
	   
	   if($extraWeight>0){
					if($extraWeight>$PMaxWeight  || $PMaxWeight==0){$PMaxWeight=$extraWeight;$pMaxCName=$cName;} else 
					if($extraWeight<$PMinWeight  ||   $PMinWeight==0){$PMinWeight=$extraWeight;$pMinCName=$cName;}
	   }
	   
		if ($sName<>$oldCName){
				$URL="Stuffdata_size_ajax.php";
		        $theParam="cName=$oldCName" . "cm";
				$showPurchaseorder="<img onClick='PubblicShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$URL\",\"$theParam\",$i,\"\",\"public\");' name='showtable$i' src='../images/showtable.gif' 
				alt='显示或隐藏产品关联的情况.' width='13' height='13' style='CURSOR: pointer'>";
				$StuffListTB="
					<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
					<tr bgcolor='#B7B7B7'>
					<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
		
					$ValueArray=array(
						array(0=>$oldCName . "cm"),
						array(0=>$PMaxWeight, 		1=>"align='right'"),
						array(0=>$PMinWeight,  1=>"align='right' style='CURSOR: pointer'",
		                              2=>"onmousedown='window.event.cancelBubble=true;' onclick='updateLoadBearing($i,\"$oldCName" .  "cm\")'"),
						array(0=>$pMaxCName),
						array(0=>$pMinCName),
						array(0=>number_format($pTotals), 		1=>"align='right'")
						);

				$checkidValue=$Id;
				include "../model/subprogram/read_model_6.php";
				echo $StuffListTB;
				
				$PMaxWeight=0; $PMinWeight=0;$pMaxCName="&nbsp;";$pMinCName="&nbsp;";$pTotals=0;
			   $oldCName=$sName;
		   }
		   $pTotals++;
		}while ($myRow = mysql_fetch_array($myResult));
		        $URL="Stuffdata_size_ajax.php";
		        $theParam="cName=$oldCName" . "cm";
				$showPurchaseorder="<img onClick='PubblicShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$URL\",\"$theParam\",$i,\"\",\"public\");' name='showtable$i' src='../images/showtable.gif' 
				alt='显示或隐藏产品关联的情况.' width='13' height='13' style='CURSOR: pointer'>";
				$StuffListTB="
					<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
					<tr bgcolor='#B7B7B7'>
					<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
		
					$ValueArray=array(
						array(0=>$oldCName . "cm"),
						array(0=>$PMaxWeight, 		1=>"align='right'"),
						array(0=>$PMinWeight,  1=>"align='right' style='CURSOR: pointer'",
		                              2=>"onmousedown='window.event.cancelBubble=true;' onclick='updateLoadBearing($i,\"$oldCName\")'"),
						array(0=>$pMaxCName),
						array(0=>$pMinCName),
						array(0=>number_format($pTotals), 		1=>"align='right'")
						);
		
				$checkidValue=$Id;
				include "../model/subprogram/read_model_6.php";
				echo $StuffListTB;
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
//$myResult = mysql_query($mySql,$link_id);
//$RecordToTal= mysql_num_rows($myResult);
$RecordToTal=$i;
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>

<script language="JavaScript" type="text/JavaScript">
<!--
function checkChange(obj){
	var e=document.getElementById("checkAccept");
    if (e.checked){
	  	//document.getElementById("AcceptText").value="";
		document.location.replace("../Admin/stuffdata_read.php");
		}
	}
function updateLoadBearing(index,Ids){
     var inputStr=prompt("请输入外箱承重极限");
     if(inputStr) {
        inputStr=inputStr.replace(/(^\s*)|(\s*$)/g,"");  //去除前后空格
        //数字检查
		var checkValue=fucCheckNUM(inputStr,"Price");
		if(checkValue==0){
		alert("格式不符");
		return false;}
		var url="cg_cgdsheet_updated.php?Sid="+Ids+"&ActionId=702&Weight="+inputStr; 
        var ajax=InitAjax(); 
	    ajax.open("GET",url,true);
	    ajax.onreadystatechange =function(){
		 if(ajax.readyState==4){// && ajax.status ==200
		 alert(ajax.responseText);
			 if(ajax.responseText=="Y"){//更新成功
             	var tabIndex="ListTable" + index;
                var TDid=document.getElementById(tabIndex).rows[0].cells[20];
                if (inputStr==0){
                	TDid.innerHTML="<span class='redB'>未设置</span>"; 
                    }
				else{
                	TDid.innerHTML=inputStr; 
                    }
			     }
			 else{
			    alert ("更新失败！"); 
			  }
			}
		 }
	   ajax.send(null); 
	 }
 }
</script>