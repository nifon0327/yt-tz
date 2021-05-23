<?php 
include "../model/modelhead.php";
$ColsNumber=7;				
$tableMenuS=600;
$From=$From==""?"read":$From;
$funFrom="aqsc02";
$nowWebPage=$funFrom."_read";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,7,8";
ChangeWtitle("$SubCompany 安全操作说明书");
$Th_Col="选项|45|序号|45|分类|250|说明书名称|400|内容|40|版本|40|培训记录|60|更新日期|80|操作员|60";
//步骤3：
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
	$SearchRows ="";
	//类型
    $TypeResult = mysql_query("SELECT * FROM  $DataPublic.aqsc03_type WHERE Estate='1' ORDER BY Id",$link_id);
	if($TypeRow = mysql_fetch_array($TypeResult)) {
		echo"<select name='TypeId' id='TypeId' onchange='RefreshPage(\"$nowWebPage\")'>";
       echo"<option value='' selected>全部</option>";	
		do{			
              $thisTypeId=$TypeRow["Id"];
              $thisName=$TypeRow["Name"];
			  if($TypeId==$thisTypeId){
				     echo"<option value='$thisTypeId' selected>$thisName</option>";
				     $SearchRows.=" and B.Id='$thisTypeId' ";
				     }
			  else{
				      echo"<option value='$thisTypeId'>$thisName</option>";					
				    }
			}while ($TypeRow = mysql_fetch_array($TypeResult));
		echo"</select>&nbsp;";
		}
	}
//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT A.Id,A.Caption,A.Attached,A.Ver,A.Date,A.Locks,B.Name AS Type,A.Operator
FROM $DataPublic.aqsc03 A
LEFT JOIN $DataPublic.aqsc03_type B ON B.Id=A.TypeId 
WHERE 1 $SearchRows ORDER BY A.Id ASC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d=anmaIn("download/aqsc/",$SinkOrder,$motherSTR);	
	do{
		$m=1;
		$Id=$myRow["Id"];		
		$Type=$myRow["Type"];
		$Caption=$myRow["Caption"];
		$Attached=$myRow["Attached"];
		if($Attached!=""){
			$Attached="aqsc03_".$Id.".pdf";
			$f=anmaIn($Attached,$SinkOrder,$motherSTR);
			$Attached="<a href=\"openorload.php?d=$d&f=$f&Type=&Action=6\" target=\"download\">View</a>";
			}
		else{
			$Attached="-";
			}
		$Ver=$myRow["Ver"];
		$Date=$myRow["Date"];
		$Today=date("Y-m-d");
		$Type=$myRow["Type"];
		$Locks=$myRow["Locks"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$ValueArray=array(
			array(0=>$Type),
			array(0=>$Caption),
			array(0=>$Attached,1=>"align='center'"),
			array(0=>$Ver,1=>"align='center'"),
			array(0=>"&nbsp;",1=>"align='center'"),
			array(0=>$Date,1=>"align='center'"),	
			array(0=>$Operator,1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
List_Title($Th_Col,"0",0);
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>