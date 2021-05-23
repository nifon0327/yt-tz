<style type="text/css">
.list{position:relative;color:#FF0000;}
.list span img{ /*CSS for enlarged image*/
border-width: 0;
padding: 2px; width:100px;
}
.list span{ 
position: absolute;
padding: 3px;
border: 1px solid gray;
visibility: hidden;
background-color:#FFFFFF;
}
.list:hover{
background-color:transparent;
}
.list:hover span{
visibility: visible;
top:0; left:28px;
}
</style>

<?php 
//电信---yang 20120801
//代码、数据库合并后共享-EWEN
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=13;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 公司收款帐号");
$funFrom="my_bankinfo";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|40|序号|40|所属公司|70|银行标识|80|Logo|35|Beneficary|100|Bank|300|Bank Add|300|SwiftID|100|ACNO|200|CNAPS CODE|200|状态|40|操作时间|70|操作人|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="2,3,5,6,7,8";

//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT * FROM $DataPublic.my2_bankinfo A WHERE 1  $SearchRows ORDER BY A.Estate DESC,A.Id";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Title=$myRow["Title"];
		$Beneficary=$myRow["Beneficary"];
		$Bank=$myRow["Bank"];
		$BankAdd=$myRow["BankAdd"];
		$SwiftID=$myRow["SwiftID"];
		$ACNO=$myRow["ACNO"];
		$Locks=$myRow["Locks"];
		$CnapsCode=$myRow["CnapsCode"];
		$Date=$myRow["Date"];
        $Estate=$myRow["Estate"];
        $Estate=$Estate==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$cSignFrom=$myRow["cSign"];
		include"../model/subselect/cSign.php";
		
		$logoFilePath ="../download/banklogo/newbank_" . $Id . ".png";
		if(file_exists($logoFilePath)){
		        $noStatue="onMouseOver=\"window.status='none';return true\"";
			    $logoFileSTR="<span class='list' >View<span><img src='$logoFilePath' $noStatue/></span>";   
		}else{
			   $logoFileSTR="&nbsp;";
		}

		$ValueArray=array(
		    array(0=>$cSign, 	1=>"align='center'"),
			array(0=>$Title, 	1=>"align='center'"),
			array(0=>$logoFileSTR, 	1=>"align='center'"),
			array(0=>$Beneficary),
			array(0=>$Bank),
			array(0=>$BankAdd),
			array(0=>$SwiftID, 		1=>"align='center'"),
			array(0=>$ACNO),
			array(0=>$CnapsCode),
			array(0=>$Estate, 		1=>"align='center'"),
			array(0=>$Date, 		1=>"align='center'"),
			array(0=>$Operator, 		1=>"align='center'")
			
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