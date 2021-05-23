<?php 
include "../model/modelhead.php";
$ColsNumber=13;
$tableMenuS=600;
$sumCols="5";		//求和列
$From=$From==""?"m":$From;
ChangeWtitle("$SubCompany 开发费用待审核列表");
$funFrom="prayforcost";
$Th_Col="选项|40|序号|40|所属公司|60|费用分类|80|请款日期|75|请款金额|60|货币类型|60|请款说明|450|凭证|40|请款人|50|状态|40";

//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="17,15";				//功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消,16审核通过

//步骤3：
$nowWebPage=$funFrom."_m";
include "../model/subprogram/read_model_3.php";
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";

//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
//$mySql="SELECT S.Id,S.ItemId,S.Description,S.Amount,S.Remark,S.Bill,S.Date,S.Provider,S.Estate,S.Operator FROM $DataIn.cwdyfsheet S WHERE 1 AND S.Estate=2 ORDER BY S.Date DESC";
$mySql="SELECT S.Id,S.ItemId,K.Name as KName,S.Date,S.Amount,C.Name as CName,S.ModelDetail,S.Description,S.Remark,S.Provider,S.Bill,S.Estate,S.Locks,S.Operator,S.cSign
 	FROM $DataIn.cwdyfsheet S 
	LEFT JOIN $DataPublic.kftypedata K ON K.ID=S.TypeID
	LEFT JOIN $DataPublic.currencydata C ON C.ID=S.Currency
	WHERE 1 AND S.Estate=2 AND S.TypeID!='15' ORDER BY S.Date DESC";
	
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$ItemId=$myRow["ItemId"];
		$KName=$myRow["KName"];
		$Description=$myRow["Description"]==""?"&nbsp":$myRow["Description"];
		$Amount=$myRow["Amount"];
		$CName=$myRow["CName"];
		$ModelDetail=$myRow["ModelDetail"]==""?"&nbsp":$myRow["ModelDetail"];		
		$Remark=$myRow["Remark"]==""?"&nbsp":"<img src='../images/remark.gif' alt='$myRow[Remark]' width='16' height='16'>";
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Provider=$myRow["Provider"];
		$Date=$myRow["Date"];
		$Estate=$myRow["Estate"];			
			switch($Estate){
				case "2":
					$Estate="<div align='center' class='yellowB' title='请款中...'>×.</div>";
					$Locks=1;
					break;
				case "0":
					$Estate="<div align='center' class='greenB' title='出错'>出错</div>";
					$Locks=0;
					break;
				}
		$Bill=$myRow["Bill"];
		$Dir=anmaIn("download/dyf/",$SinkOrder,$motherSTR);
		if($Bill==1){
			$Bill="DYF".$Id.".jpg";
			$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
			$Bill="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Bill="&nbsp;";
			}
			$cSignFrom=$myRow["cSign"];
		include"../model/subselect/cSign.php";
		//财务强制锁:非未处理皆锁定
		$ValueArray=array(
		    array(0=>$cSign,1=>"align='center'"),
			array(0=>$KName,3=>"..."),
			array(0=>$Date,1=>"align='center'"),			
			array(0=>$Amount,1=>"align='right'"),
			array(0=>$CName,1=>"align='center'"),
			array(0=>$Description,3=>"..."),
			array(0=>$Bill,1=>"align='center'"),			
			array(0=>$Operator,1=>"align='center'"),
			array(0=>$Estate,1=>"align='center'")
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