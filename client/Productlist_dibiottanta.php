<?php   
//电信-zxq 2012-08-01
/*
$DataIn.productdata
$DataIn.trade_object
$DataIn.producttype 
二合一已更新
*/
include "../model/modelhead.php";
include "../model/subprogram/sys_parameters.php";
$ColsNumber=10;$tableMenuS=500;
ChangeWtitle("Product List");
//序号，产品代码，描述，单价，装箱数量，外箱尺寸，重量，净重
$Th_Col="No.|40|Product Code|200|Description|350|Unit/Carton|70|Carton Size(CM)|120|NW(KG)|50|GW(KG)|50";

$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$ChooseOut="N";
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
include "../admin/subprogram/read_model_5.php";
$i=1;$j=1;
List_Title($Th_Col,"1",1);
$mySql= "SELECT P.eCode,P.Description,P.Price,P.Weight,G.Relation,S.Spec
	FROM $DataIn.productdata P
	LEFT JOIN $DataIn.pands G ON. G.ProductId=P.ProductId
	LEFT JOIN $DataIn.stuffdata S ON S.StuffId=G.StuffId
	WHERE 1 AND S.TypeId='9040' AND P.Estate=1 AND P.CompanyId='1069' ORDER BY P.Id DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d3=anmaIn("download/productfile/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		$eCode=$myRow["eCode"]==""?"&nbsp;":$myRow["eCode"];
		$Description=$myRow["Description"]==""?"&nbsp;":$myRow["Description"];
		$Weight=$myRow["Weight"];
		$Relation=explode("/",$myRow["Relation"]); 
		$Boxs=$Relation[1];
		$Spec=$myRow["Spec"];
		$NW=($Boxs*$Weight)/1000;
		if($NW>0){
			$GW=$NW+1;
		}
		else{
			$NW="&nbsp;";$GW="&nbsp;";
			}
			$ValueArray=array(
				array(0=>$eCode,			3=>"..."),
				array(0=>$Description),
				array(0=>$Boxs,			1=>"align='center'"),
				array(0=>$Spec,			1=>"align='center'"),
				array(0=>$NW,			1=>"align='center'"),
				array(0=>$GW,			1=>"align='center'")
				);
		$checkidValue=$Id;
		include "../admin/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
include "../model/subprogram/read_model_menu.php";
?>