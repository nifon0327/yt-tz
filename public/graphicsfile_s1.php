<?php 
//电信-EWEN
//MC代码不一样，BOM回传参数加入成本和货币ID
//步骤1
include "../model/subprogram/s1_model_1.php";
//步骤2：需处理
$Th_Col="选项|40|序号|40|产品ID|80|图档分类|80|图档说明|280|图档下载|80|客户|80|状态|40|更新日期|70|操作|60";
$ColsNumber=10;
$tableMenuS=600;
$Page_Size = 100;							//每页默认记录数量
$isPage=1;//是否分页
//已传入参数：目的查询页面，来源页面，可选记录数，动作，类别uType
switch($Action){
	case "1"://
		$sSearch.=" AND S.FileType=1";
		break;
	
	}
include "../model/subprogram/s1_model_3.php";
//步骤5：
include "../model/subprogram/s1_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql="SELECT S.Id,S.FileRemark,S.FileName,S.Estate,S.Locks,S.Date,S.Operator,S.FileType AS FileId,
T.Name AS FileType,C.Forshort AS Company,P.TypeName,S.ProductType
FROM $DataIn.doc_standarddrawing S 
LEFT JOIN $DataPublic.doc_type T ON T.Id=S.FileType 
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=S.CompanyId
LEFT JOIN $DataIn.producttype P ON P.TypeId=S.ProductType
WHERE 1 $sSearch ORDER BY S.Id ASC";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$FileId=$myRow["FileId"];
		$ProductType=$myRow["ProductType"];
		$FileType=$myRow["FileType"];
		$FileRemark=$myRow["FileRemark"];
		$FileName=$myRow["FileName"];
		$Company=$myRow["Company"];
		$TypeName=$FileId==1?$ProductType:$myRow["TypeName"];
		$f=anmaIn($FileName,$SinkOrder,$motherSTR);
		$FileName="<a href=\"openorload.php?d=$d&f=$f&Type=&Action=6\" target=\"download\"><img src='../images/down.gif' style='$style' width='18' height='18'></a>";
		
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Estate=$myRow["Estate"]==0?"<div class='redB'>×</div>":"<div class='greenB'>√</div>";
		$Locks=$myRow["Locks"];
		switch($Action){
		       case 1:
			   $Bdata=$ProductType."^^".$FileRemark;
			   break;
		
		}
		$ValueArray=array(	
		    array(0=>$TypeName,	1=>"align='center'"),	  
			array(0=>$FileType),
			array(0=>$FileRemark,1=>"align='center'"),
			array(0=>$FileName,	1=>"align='center'"),
			array(0=>$Company,	1=>"align='center'"),
			array(0=>$Estate,	1=>"align='center'"),
			array(0=>$Date,	1=>"align='center'"),
			array(0=>$Operator,	1=>"align='center'")
			);

		$checkidValue=$Bdata;
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>