<?php 
//电信-zxq 2012-08-01
/*
$DataPublic.currencydata
$DataPublic.adminitype
$DataIn.hzqkmain
$DataIn.hzqksheet
二合一已更新
*/
//步骤1
include "../model/subprogram/s1_model_1.php";
//步骤2：需处理
$ColsNumber=12;
$tableMenuS=500;
$Th_Col="选项|40|序号|40|请款人|70|请款日期|100|金额|80|货币|40|说明|400|分类|100";
$Page_Size = 100;							//每页默认记录数量
$Pagination=$Pagination==""?1:$Pagination;	
//非必选,过滤条								
$Parameter.=",Bid,$Bid";
switch($Action){
	case "2"://来自新增订单
		//if($From!=slist){//$CompanyIdSTR=" and P.CompanyId=$Bid";}
		//$CompanyIdSTR.=" AND P.Estate=1 AND P.ProductId IN(SELECT ProductId FROM $DataIn.pands GROUP BY ProductId ORDER BY ProductId)";

	break;
	}   
//已传入参数：目的查询页面，来源页面，可选记录数，动作，类别uType
//$uTypeSTR=$uType==""?"":"and P.TypeId=$uType";
include "../model/subprogram/s1_model_3.php";			

//步骤4：需处理-条件选项
if($From!="slist"){

	$SearchRows="";
	$monthResult = mysql_query("SELECT S.Date FROM $DataIn.hzqksheet S WHERE 1 $SearchRows and S.Date>='2010-05-01' group by DATE_FORMAT(S.Date,'%Y-%m') order by S.Date DESC",$link_id);
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
include "../model/subprogram/s1_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$Searchfee="(T.TypeId='610' or T.TypeId='624' or T.TypeId='621' or T.TypeId='615' or T.TypeId='675')";//交际费,其他总务费用,薪资/奖金,税款
$mySql="SELECT S.Id,S.Mid,S.Content,S.Operator,S.Amount,S.Date,S.Operator,T.Name AS Type,C.Symbol AS Currency
 	FROM $DataIn.hzqksheet S 
	LEFT JOIN $DataPublic.adminitype T ON S.TypeId=T.TypeId
	LEFT JOIN $DataPublic.currencydata C ON C.Id=S.Currency
	WHERE 1 $SearchRows and $Searchfee  $sSearch
	order by S.Date DESC";
//echo ""	
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Mid=$myRow["Mid"];
		$Date=$myRow["Date"];
		$Amount=$myRow["Amount"];
		$Currency=$myRow["Currency"];		
		$Content=$myRow["Content"];
		$Type=$myRow["Type"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";	
		switch($Action){
		
		case"2":
			$Bdata=$Id."^^".$Operator."^^".$Date."^^".$Amount."^^".$Content."^^".$Type;	
			break;
		
		}
				
		$ValueArray=array(
		    array(0=>$Operator,
					 1=>"align='center'"),
			array(0=>$Date,
					 1=>"align='center'"),
			array(0=>$Amount,
					 1=>"align='center'"),
			array(0=>$Currency,
					 1=>"align='center'"),
			array(0=>$Content,					
					 3=>"..."),
			array(0=>$Type,					 
					 3=>"..."),
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

<script language="JavaScript" type="text/JavaScript">
function zhtj(obj){
	document.form1.submit();
}
</script>