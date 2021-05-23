<?php   
//$DataPublic.msg1_bulletin 二合一已更新
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=6;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 技术维护信息");
$funFrom="docs_technical";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|40|序号|40|信息分类|60|信息标题|250|信息内容|480|日期|70|操作员|50";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,87";
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows="";
	//月份
		$type_Result = mysql_query("SELECT Id,Name FROM $DataPublic.doc1_type WHERE Estate=1 ORDER BY Id",$link_id);
		if($CheckTypeRow = mysql_fetch_array($type_Result)) {
			echo"<select name='chooseType' id='chooseType' onchange='RefreshPage(\"$nowWebPage\")'>";
			do{			
				$TypeId=$CheckTypeRow["Id"];
                                $TypeName=$CheckTypeRow["Name"];
				$chooseType=$chooseType==""?$TypeId:$chooseType;
				if($chooseType==$TypeId){
                                        echo"<option value='$TypeId' selected>$TypeName</option>";
					$SearchRows.=" AND Type='$TypeId'";
					}
				else{
					echo"<option value='$TypeId'>$TypeName</option>";					
					}
				}while($CheckTypeRow = mysql_fetch_array($type_Result));
			echo"</select>&nbsp;";
			}
	}

echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT B.Id,B.Title,B.Content,T.Name AS Type,B.Date,B.Operator 
        FROM $DataPublic.doc1_technical B 
        LEFT JOIN $DataPublic.doc1_type T ON B.Type=T.Id 
        WHERE 1 $SearchRows ORDER BY B.Id DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Title=$myRow["Title"];
		$Content=nl2br($myRow["Content"]);		
		$Type=$myRow["Type"];
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Locks=1;
		if($Date<date("Y-m-d")){
			$Locks=0;
			}
		$ValueArray=array(
			array(0=>$Type,
					 1=>"align='center'"),
			array(0=>$Title),
			array(0=>$Content),
			array(0=>$Date,
					 1=>"align='center'"),
			array(0=>$Operator,
					 1=>"align='center'")
			);
		$checkidValue=$Id;
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
