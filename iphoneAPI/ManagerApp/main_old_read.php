<?php 
//主界面页面
$ReadModuleTypeSign=2;
$ReadAccessSign=3;
include "user_access.php";  //用户权限

$testCZ_PJ = ($LoginNumber == 11965|| $LoginNumber == 10341) ? true : false;

$ServerId=0;        
$topArray=array(); 
$dataArray1=array(); $dataArray2=array(); $dataArray3=array();   $dataArray4=array();     
//模块ID; 功能模块名称；子模块参数；区分系统Id；可用状态；行数；列数；右上角数字标记  
//导航显示
$topArray[] = array("Id"=>"101","Name"=>"通知","ModuleId"=>"101","ServerId"=>"$ServerId","Estate"=>"1","Row"=>"1","Col"=>"1");
$topArray[]=array("Id"=>"102","Name"=>"监控","ModuleId"=>"102","ServerId"=>"$ServerId","Estate"=>"1","Row"=>"1","Col"=>"3");
$topArray[]=array("Id"=>"113","Name"=>"个人助理","ModuleId"=>"113","ServerId"=>"$ServerId","Estate"=>"1","Row"=>"1","Col"=>"4");

//默认权限-内容
$Row3=2;$Col=1;
$dataArray3[] = array("Id"=>"112","Name"=>"非BOM","ModuleId"=>"101","ServerId"=>"$ServerId","Estate"=>"1","Row"=>"1","Col"=>"1");
$dataArray3[] = array("Id"=>"119","Name"=>"资产","ModuleId"=>"101","ServerId"=>"$ServerId","Estate"=>"0","Row"=>"1","Col"=>"2");
$dataArray3[] = array("Id"=>"116","Name"=>"查询","ModuleId"=>"116","ServerId"=>"$ServerId","Estate"=>"1","Row"=>"1","Col"=>"4");

$dataArray3[] = array("Id"=>"115","Name"=>"门禁","ModuleId"=>"115","ServerId"=>"$ServerId","Estate"=>"1","Row"=>"2","Col"=>"1");
$dataArray3[] = array("Id"=>"103","Name"=>"人员","ModuleId"=>"103","ServerId"=>"$ServerId","Estate"=>"1","Row"=>"2","Col"=>"2"); 
$dataArray3[] = array("Id"=>"134","Name"=>"文件","ModuleId"=>"134","ServerId"=>"$ServerId","Estate"=>"1","Row"=>"2","Col"=>"3");
$dataArray3[] = array("Id"=>"135","Name"=>"系统","ModuleId"=>"135","ServerId"=>"$ServerId","Estate"=>"0","Row"=>"2","Col"=>"4"); 

$Row4=2;$Col=1;
if (in_array("101",$itemArray)){
     $dataArray4[] = array("Id"=>"117","Name"=>"授权书","ModuleId"=>"117","ServerId"=>"$ServerId","Estate"=>"1","Row"=>"1","Col"=>"$Col");
     $Col++;
}
$dataArray4[] = array("Id"=>"114","Name"=>"证书","ModuleId"=>"114","ServerId"=>"$ServerId","Estate"=>"1","Row"=>"1","Col"=>"$Col"); 
$Col++;
$dataArray4[] = array("Id"=>"123","Name"=>"首页","ModuleId"=>"123","ServerId"=>"$ServerId","Estate"=>"1","Row"=>"1","Col"=>"$Col"); 
$Col++;
$dataArray4[] = array("Id"=>"122","Name"=>"地图","ModuleId"=>"122","ServerId"=>"$ServerId","Estate"=>"1","Row"=>"1","Col"=>"$Col"); 
$dataArray4[] = array("Id"=>"104","Name"=>"培训","ModuleId"=>"104","ServerId"=>"$ServerId","Estate"=>"1","Row"=>"2","Col"=>"1"); 
$dataArray4[] = array("Id"=>"136","Name"=>"模板","ModuleId"=>"136","ServerId"=>"$ServerId","Estate"=>"0","Row"=>"2","Col"=>"2"); 
$dataArray4[] = array("Id"=>"121","Name"=>"简介","ModuleId"=>"121","ServerId"=>"$ServerId","Estate"=>"1","Row"=>"2","Col"=>"3"); 
$dataArray4[] = array("Id"=>"137","Name"=>"APP","ModuleId"=>"137","ServerId"=>"$ServerId","Estate"=>"0","Row"=>"2","Col"=>"4"); 

$Row=1;$Col=1;
$dataArray1[] = array("Id"=>"105","Name"=>"审计","ModuleId"=>"105","ServerId"=>"$ServerId","Estate"=>"1","Row"=>"$Row","Col"=>"$Col"); 
$Col++;
            
if (in_array("1044",$modelArray) || in_array("1245",$modelArray)  || in_array("1347",$modelArray)){
    $dataArray1[] = array("Id"=>"106","Name"=>"审核","ModuleId"=>"1044","ServerId"=>"$ServerId","Estate"=>"1","Row"=>"$Row","Col"=>"$Col"); 
    $Col++;
}


$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(CURDATE(),1) AS CurWeek",$link_id));
$curWeeks=substr($dateResult["CurWeek"],4,2);
$dataArray1[] = array("Id"=>"100","Name"=>"行事历","ModuleId"=>"100","ServerId"=>"$ServerId","Estate"=>"1","Row"=>"1","Col"=>"4","IconType"=>"4","Value"=>"$curWeeks"); 

$oldRow=$Row;$Col=1;
if (in_array("173",$itemArray)){
      $Row=$Row==$oldRow?$oldRow+1:$Row;
	  $dataArray1[] = array("Id"=>"109","Name"=>"新单","ModuleId"=>"109","ServerId"=>"$ServerId","Estate"=>"1","Row"=>"$Row","Col"=>"$Col");
	  $Col++;
}
if (in_array("101",$itemArray)){
      $Row=$Row==$oldRow?$oldRow+1:$Row;
      $checkSql="SELECT SUM(S.Qty*S.Price*D.Rate) AS Amount 
								FROM $DataIn.yw1_ordersheet S
								LEFT JOIN $DataIn.yw1_ordermain M  ON S.OrderNumber=M.OrderNumber 
								LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
								LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency  WHERE S.Estate>0";
         $checkRow=mysql_fetch_array(mysql_query($checkSql,$link_id));
         $Amount=$checkRow["Amount"]==""?0:round($checkRow["Amount"]/10000000,1);  
            
	  $dataArray1[] = array("Id"=>"110","Name"=>"未出","ModuleId"=>"110","ServerId"=>"$ServerId","Estate"=>"1","Row"=>"$Row","Col"=>"$Col","IconType"=>"5","Value"=>"$Amount");
	  $Col++;
}

if (in_array("104",$itemArray)){
      $Row=$Row==$oldRow?$oldRow+1:$Row;
	  $dataArray1[] = array("Id"=>"111","Name"=>"已出","ModuleId"=>"104","ServerId"=>"$ServerId","Estate"=>"1","Row"=>"$Row","Col"=>"$Col");
	  $Col++;
	  
	  //$Row=$Row==$oldRow?$oldRow+1:$Row;
	  $dataArray1[] = array("Id"=>"125","Name"=>"3PL","ModuleId"=>"125","ServerId"=>"$ServerId","Estate"=>"0","Row"=>"$Row","Col"=>"$Col");
	  $Col++;
}

$oldRow=$Row;$Col=1;
if (in_array("128",$itemArray) || in_array("101",$itemArray)){
     $Row=$Row==$oldRow?$oldRow+1:$Row;
     //计算待定百分比(含订单锁、配件锁)
      $OrderResult=mysql_fetch_array(mysql_query("SELECT SUM(IF(ISNULL(A.Weeks) OR A.Type=2 OR A.Locks>0, A.Amount,0)) AS TBCAmount,SUM(A.Amount) AS Amount 
     FROM (
            SELECT YEARWEEK(substring(IFNULL(PI.Leadtime,PL.Leadtime),1,10),1)  AS Weeks,(S.Price * S.Qty * D.Rate ) AS Amount,E.Type,SUM(IF(L.locks=0,1,0)) AS Locks 
		     FROM $DataIn.yw1_ordersheet S
		     LEFT JOIN $DataIn.yw1_ordermain M ON S.OrderNumber = M.OrderNumber
		     LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
		     LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency 
		     LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id  
		     LEFT JOIN $DataIn.yw3_pileadtime PL ON PL.POrderId=S.POrderId 
		     LEFT JOIN $DataIn.yw2_orderexpress E ON E.POrderId=S.POrderId AND E.Type=2 
             LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId 
             LEFT JOIN $DataIn.cg1_lockstock L ON L.StockId=G.StockId AND L.locks=0 
		    WHERE S.Estate >0  GROUP BY S.POrderId) A ",$link_id));
 
     $TBCAmount=$OrderResult["TBCAmount"]==""?0:$OrderResult["TBCAmount"];
     $TotalAmount=$OrderResult["Amount"]==""?0:$OrderResult["Amount"];
     $TBCPercent=$TotalAmount>0?round($TBCAmount/$TotalAmount*100):0;
     
    $dataArray1[] = array("Id"=>"118","Name"=>"业务","ModuleId"=>"118","ServerId"=>"$ServerId","Estate"=>"1","Row"=>"$Row","Col"=>"$Col","IconType"=>"2","Percent"=>"$TBCPercent");
     $Col++;
}

if (in_array("1006",$modelArray)){
   $Row=$Row==$oldRow?$oldRow+1:$Row;
   $dataArray1[] = array("Id"=>"107","Name"=>"采购","ModuleId"=>"107","ServerId"=>"$ServerId","Estate"=>"1","Row"=>"$Row","Col"=>"$Col"); 
    $Col++;
} 

$Row=$Row==$oldRow?$oldRow+1:$Row;
$dataArray1[] = array("Id"=>"133","Name"=>"仓库","ModuleId"=>"133","ServerId"=>"$ServerId","Estate"=>"1","Row"=>"$Row","Col"=>"$Col"); 
$Col++;

$dataArray1[] = array("Id"=>"108","Name"=>"组装","ModuleId"=>"108","ServerId"=>"$ServerId","Estate"=>"1","Row"=>"$Row","Col"=>"$Col"); 

$oldRow=$Row;$Col=1;
if (in_array("128",$itemArray) || in_array("101",$itemArray)){
     $Row=$Row==$oldRow?$oldRow+1:$Row;
    $dataArray1[] = array("Id"=>"120","Name"=>"产品","ModuleId"=>"120","ServerId"=>"$ServerId","Estate"=>"1","Row"=>"$Row","Col"=>"$Col");
     $Col++;
 }


$Row=$Row==$oldRow?$oldRow+1:$Row;
$dataArray1[] = array("Id"=>"126","Name"=>"包装","ModuleId"=>"126","ServerId"=>"$ServerId","Estate"=>"0","Row"=>"$Row","Col"=>"$Col"); 
$Col++;
$Estate127=$LoginNumber==10868 || $LoginNumber==10341?1:0;
$dataArray1[] = array("Id"=>"127","Name"=>"项目","ModuleId"=>"127","ServerId"=>"$ServerId","Estate"=>"$Estate127","Row"=>"$Row","Col"=>"$Col"); 


$oldRow=$Row;$Col=1;
 if (in_array("106", $itemArray) || in_array("1078",$modelArray)) {
   $Row=$Row==$oldRow?$oldRow+1:$Row;
	$dataArray1[] = array("Id" => "124", "Name" => "产品", "ModuleId" => "124", "ServerId" => "$ServerId", "Estate" => "1", "Row" => "$Row", "Col" => "$Col");
	$Col++;
}

 if (in_array("1077",$modelArray)) {
   $Row=$Row==$oldRow?$oldRow+1:$Row;
	//$tmpId=versionToNumber($AppVersion)>301?128:124;
	$dataArray1[] = array("Id" => "128", "Name" => "配件", "ModuleId" => "128", "ServerId" => "$ServerId", "Estate" =>"1", "Row" => "$Row", "Col" => "$Col");
	$Col++;
}


 $Row=$Row==$oldRow?$oldRow+1:$Row;
 $dataArray1[] = array("Id"=>"129","Name"=>"新品","ModuleId"=>"129","ServerId"=>"$ServerId","Estate"=>"0","Row"=>"$Row","Col"=>"4"); 
 $Col++;
$Row1=$Row;


if (count($dataArray1)>0){
	$jsonArray[]=array("GroupName"=>"","Rows"=>"$Row1","data"=>$dataArray1);
}

 include "main_pt_read.php";
if (count($dataArray2)>0){
	$jsonArray[]=array("GroupName"=>"皮套","Rows"=>"$Row2","data"=>$dataArray2);
}	

if (count($dataArray3)>0){
	$jsonArray[]=array("GroupName"=>"内部","Rows"=>"$Row3","data"=>$dataArray3);
}	 

if (count($dataArray3)>0){
	$jsonArray[]=array("GroupName"=>"应用","Rows"=>"$Row4","data"=>$dataArray4);
}	          

?>