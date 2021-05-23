<?php 
//电信-zxq 2012-08-01
/*
$DataIn.cw2_fkdjsheet
$DataIn.trade_object
$DataPublic.currencydata
二合一已更新
*/
//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
include "../model/subprogram/read_model_3.php";
//过滤条件

if($From!="slist"){
	$SearchRows=" and S.Estate IN (0,3)";
   $Did=$Did==""?0:$Did;
    $tempStr="Did".$Did;
   $$tempStr="selected";
	echo"<select name='Did' id='Did' onchange='ResetPage(this.name)'>
	<option value='0' $Did0>未抵付</option><option value='1' $Did1>已抵付</option></select>&nbsp;";		
 if($Did>0){
    $SearchRows.=" AND S.Did>0";
}
else{
    $SearchRows.=" AND S.Did=0";
}
	//月份
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
	else{
		//无月份记录
		//$SearchRows.=" and M.Month='无效'";
		}
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
//结付的银行
//include "../model/selectbank1.php";
echo"$CencalSstr";
//步骤4：
include "../model/subprogram/read_model_5.php";
//步骤5：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT S.Id,S.CompanyId,S.Amount,S.Remark,S.Date,S.Estate,S.Locks,S.Operator,P.Forshort,C.Symbol,S.Attached,S.Did
 	FROM $DataIn.cw2_hksheet S 
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
	LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
	WHERE 1 $SearchRows order by S.Date DESC";	
//echo $mySql;
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

		$Estate="<div align='center' class='greenB' title='审核通过'>√</div>";
		 $Did=$myRow["Did"];
         if($Did>0){
                   $LockRemark="已抵付，锁定操作";
                   $DidStr="<span class='greenB'>已抵付</span>";
              }
         else  $DidStr="<span class='redB'>未抵付</span>";
		//财务强制锁:非未处理皆锁定
		$ValueArray=array(
			array(0=>$Forshort,1=>"align='center'"),		
			array(0=>$Remark,3=>"..."),
			array(0=>$Amount,1=>"align='center'"),
			array(0=>$Symbol,1=>"align='center'"),
			array(0=>$Attached,1=>"align='center'"),
			array(0=>$DidStr,1=>"align='center'"),
			array(0=>$Estate,1=>"align='center'"),
			array(0=>$Operator,1=>"align='center'"),
			array(0=>$Date,1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));	}
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
