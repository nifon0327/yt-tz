<?php 
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=12;
$tableMenuS=500;
$sumCols="4";		//求和列
$From=$From==""?"m":$From;
ChangeWtitle("$SubCompany 供应商其它扣款待审核列表");
$funFrom="cw_fkhk";
$Th_Col="选项|40|序号|40|供应商|80|说明|400|返回金额|60|货币|40|单据|40|状态|40|请款人|50|请款日期|75";
//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="17,15";
//步骤3：
$nowWebPage=$funFrom."_m";
include "../model/subprogram/read_model_3.php";
//非必选,过滤条件
if($From!="slist"){	
	$SearchRows="";
	$monthResult = mysql_query("SELECT S.Date FROM $DataIn.cw2_hksheet S WHERE 1 $SearchRows group by DATE_FORMAT(S.Date,'%Y-%m') order by S.Date DESC",$link_id);
	if($monthRow = mysql_fetch_array($monthResult)) {
		echo"<select name='chooseMonth' id='chooseMonth' onchange='document.form1.submit()'>";
		do{
			$dateValue=date("Y-m",strtotime($monthRow["Date"]));
			$dateText=date("Y年m月",strtotime($monthRow["Date"]));
			$chooseMonth=$chooseMonth==""?$dateValue:$chooseMonth;
			if($chooseMonth==$dateValue){
				echo"<option value='$dateValue' selected>$dateText</option>";
				$SearchRows.=" and DATE_FORMAT(S.Date,'%Y-%m')='$dateValue'";
				}
			else{
				echo"<option value='$dateValue'>$dateText</option>";					
				}
			}while($monthRow = mysql_fetch_array($monthResult));
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
$mySql="SELECT S.Id,S.CompanyId,S.Amount,S.Remark,S.Date,S.Estate,S.Locks,S.Operator,P.Forshort,C.Symbol,S.Attached
 	FROM $DataIn.cw2_hksheet S 
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
	LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
	WHERE 1 and S.Estate=2 $SearchRows order by S.Date DESC";
//echo "$mySql"; 	
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Forshort=$myRow["Forshort"];
		$Remark=$myRow["Remark"]==""?"&nbsp":$myRow["Remark"];
		$Symbol=$myRow["Symbol"];
		$Amount=$myRow["Amount"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Date=$myRow["Date"];
		$Locks=$myRow["Locks"];		
 		$Estate=$myRow["Estate"];
        $Attached=$myRow["Attached"];
        if($Attached==1){
	             $Dir=anmaIn("download/cwhk/",$SinkOrder,$motherSTR);
			     $Attached="H".$Id.".jpg";
			     $Attached=anmaIn($Attached,$SinkOrder,$motherSTR);
			     $Attached="<span onClick='OpenOrLoad(\"$Dir\",\"$Attached\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			   }
		else{
			    $Attached="-";
			  }

	   $Estate="<div align='center' class='yellowB' title='请款中...'>×.</div>";
		$ValueArray=array(
			array(0=>$Forshort,1=>"align='center'"),		
			array(0=>$Remark,3=>"..."),
			array(0=>$Amount,1=>"align='center'"),
			array(0=>$Symbol,1=>"align='center'"),
			array(0=>$Attached,1=>"align='center'"),
			array(0=>$Estate,1=>"align='center'"),
			array(0=>$Operator,1=>"align='center'"),
			array(0=>$Date,1=>"align='center'")
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