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
if ($myCompanyId==1004 || $myCompanyId==1059  || $myCompanyId==1072){  //CEL-A OR CEL-B
    $CompanySTR="and (P.CompanyId='1004' OR P.CompanyId='1059' OR P.CompanyId='1072') ";
   }
else{
	
	if ($myCompanyId==1081 || $myCompanyId==1002 || $myCompanyId==1080 || $myCompanyId==1065 ) {
		$CompanySTR="and (P.CompanyId in ('1081','1002','1080','1065'))";
	}
	else {
    	$CompanySTR=" AND P.CompanyId='$myCompanyId'";
	}
}

//序号，产品代码，描述，单价，装箱数量，外箱尺寸，重量，净重
$Th_Col="No.|40|Product Code|180|Description|380|Unit/Carton|80|ItemWeight<br>(g)|80|TotalWeight<br>(KG)|80|Carton Size(CM)|120";

$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$ChooseOut="N";
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
include "../admin/subprogram/read_model_5.php";
$i=1;$j=1;
List_Title($Th_Col,"1",1);
/*
$mySql= "SELECT P.eCode,P.Description,P.Price,P.Weight,G.Relation,S.Spec
	FROM $DataIn.productdata P
	LEFT JOIN $DataIn.pands G ON. G.ProductId=P.ProductId
	LEFT JOIN $DataIn.stuffdata S ON S.StuffId=G.StuffId
	WHERE 1 AND S.TypeId='9040' AND P.Estate=1  ORDER BY P.Id DESC";
*/	
$mySql= "SELECT P.eCode,P.Description,P.Price,P.Weight,G.Relation,S.Spec,P.ProductId,P.TestStandard,S.Weight AS BoxWeight
	FROM $DataIn.productdata P
	LEFT JOIN $DataIn.pands G ON. G.ProductId=P.ProductId
	LEFT JOIN $DataIn.stuffdata S ON S.StuffId=G.StuffId
	WHERE 1 AND S.TypeId='9040' AND P.Estate=1 $CompanySTR ORDER BY P.Id DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d3=anmaIn("download/productfile/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		$ProductId=$myRow["ProductId"];
		$TestStandard=$myRow["TestStandard"];
       $eCode=$myRow["eCode"];
		if($TestStandard==1){
			$FileName="T".$ProductId.".jpg";
			$f=anmaIn($FileName,$SinkOrder,$motherSTR);
			$d=anmaIn("download/teststandard/",$SinkOrder,$motherSTR);			
			$eCode="<span onClick='OpenOrLoad(\"$d\",\"$f\")' style='CURSOR: pointer;color:#FF6633;'>$eCode</span>";
			}
  		$eCode=$eCode==""?"&nbsp;":$eCode;

		$Description=$myRow["Description"]==""?"&nbsp;":$myRow["Description"];
		$Weight=$myRow["Weight"];
         $BoxWeight=$myRow["BoxWeight"];
		$Relation=explode("/",$myRow["Relation"]); 
		$Boxs=$Relation[1];
		$Spec=$myRow["Spec"];
       if($Weight>0){
               $MainWeight=($Boxs*$Weight+$BoxWeight)/1000;
              $MainWeight=sprintf("%.2f",$MainWeight);
          }
       else {
                $MainWeight="&nbsp;";
$Weight="&nbsp;";
                 }
			$ValueArray=array(
				array(0=>$eCode),
				array(0=>$Description),
				array(0=>$Boxs,			1=>"align='center'"),
			    array(0=>$Weight,			1=>"align='center'"),
			    array(0=>$MainWeight,			1=>"align='center'"),
				array(0=>$Spec,			1=>"align='center'")
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