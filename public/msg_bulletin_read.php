
<?php
//$DataPublic.msg1_bulletin 二合一已更新电信---yang 20120801
include "../model/modelhead.php";
$gl = include "../basic/global.conf.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=6;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 电子公告记录");
$funFrom="msg_bulletin";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|40|序号|40|公告分类|60|公告标题|120|内容|490|日期|70|操作员|50";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = isset($gl['page'])?$gl['page']:10;
$ActioToS="1,2,3,4,87";
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	    $SearchRows="";
      //选择公司名称
        $SelectFrom=5;
        $cSignTB="B";
        $SharingShow="Y";
        include "../model/subselect/cSign.php";
	  //月份
		$date_Result = mysql_query("SELECT B.Date FROM $DataPublic.msg1_bulletin B WHERE 1 $SearchRows GROUP BY DATE_FORMAT(B.Date,'%Y-%m') ORDER BY B.Date DESC",$link_id);
		if($dateRow = mysql_fetch_array($date_Result)) {
			echo"<select name='chooseDate' id='chooseDate' onchange='RefreshPage(\"$nowWebPage\")'>";
			do{			
				$dateValue=date("Y-m",strtotime($dateRow["Date"]));
				$chooseDate=$chooseDate==""?$dateValue:$chooseDate;
				if($chooseDate==$dateValue){
					echo"<option value='$dateValue' selected>$dateValue</option>";
					$SearchRows.=" AND DATE_FORMAT(B.Date,'%Y-%m')='$dateValue'";
					}
				else{
					echo"<option value='$dateValue'>$dateValue</option>";					
					}
				}while($dateRow = mysql_fetch_array($date_Result));
			echo"</select>&nbsp;";
			}
	}

echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'> <option value='1' $Pagination1>分页</option> <option value='0' $Pagination0>不分页</option> </select>$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT B.Id,B.Title,B.Content,B.Type,B.Date,B.Operator FROM $DataPublic.msg1_bulletin B WHERE 1 $SearchRows ORDER BY B.Id DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Title=$myRow["Title"];
		$Content=nl2br($myRow["Content"]);		
		$Type=$myRow["Type"];
		switch($Type){
			case 0:
			$Type="长期公告";
			break;
			default:
			$Type="当天公告";
			break;
			}
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