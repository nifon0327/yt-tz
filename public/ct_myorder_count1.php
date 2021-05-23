<?php 
//电信-EWEN
include "../model/modelhead.php";

$Th_Col="序号|40|餐厅|80|菜式名称|200|价格|60|点餐数量|60|点餐金额|60|点餐人|55|备注|100";
$ChooseOut="N";
include "../model/subprogram/read_model_3.php";
$SearchRows="";
  $monthResult = mysql_query("SELECT Date FROM $DataPublic.ct_myorder  group by DATE_FORMAT(Date,'%Y-%m-%d') order by Date DESC",$link_id);
	if($monthResult && $monthRow = mysql_fetch_array($monthResult)) {
		echo"<select name='chooseDate' id='chooseDate' onchange='document.form1.submit()'>";
		do{
			 $dateValue=date("Y-m-d",strtotime($monthRow["Date"]));
			$dateText=date("Y年m月d日",strtotime($monthRow["Date"]));
			$chooseDate=$chooseDate==""?$dateValue:$chooseDate;
			if($chooseDate==$dateValue){
				echo"<option value='$dateValue' selected>$dateText</option>";
				$SearchRows.=" and DATE_FORMAT(A.Date,'%Y-%m-%d')='$dateValue'";
				}
			else{
				echo"<option value='$dateValue'>$dateText</option>";					
				}
			}while($monthRow = mysql_fetch_array($monthResult));
		echo"</select>&nbsp;";
	}
		
	$checkTypeSql=mysql_query("SELECT A.Operator,M.Name FROM $DataPublic.ct_myorder  A  LEFT JOIN $DataPublic.staffmain M ON M.Number=A.Operator WHERE 1 $SearchRows  GROUP BY A.Operator ORDER BY A.Operator",$link_id);
	if($checkTypeRow=mysql_fetch_array($checkTypeSql)){
		echo"<select name=mOperator id=mOperator onchange='ResetPage(this.name)'><option value='' selected>--全部--</option>";
		do{
			$Id=$checkTypeRow["Operator"];
			$Name=$checkTypeRow["Name"];
			if($Id==$mOperator){
				echo"<option value='$Id' selected>$Name</option>";
				$SearchRows.=" AND A.Operator='$Id'";
				}
			else{
				echo"<option value='$Id'>$Name</option>";
				}
			}while($checkTypeRow=mysql_fetch_array($checkTypeSql));
		echo"</select>&nbsp;";
		  echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='ct_myorder_count.php' target='_self'>点餐统计表</a>";
		}
	

//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT A.Id,A.Price,A.Qty,A.Amount,A.Estate,A.Locks,A.Remark,A.Date,A.Operator,B.Name AS MenuName,C.Name AS CTName 
FROM $DataPublic.ct_myorder A
LEFT JOIN $DataPublic.ct_menu B ON B.Id=A.MenuId
LEFT JOIN $DataPublic.ct_data C ON C.Id=A.CtId 
WHERE 1 $SearchRows  ORDER BY C.Id,A.MenuId,A.Id DESC";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
  $sumQty=0;
  $sumAmount=0;
	do {
		$m=1;
		$Id=$myRow["Id"];
		$CTName=$myRow["CTName"];	
		$MenuName=$myRow["MenuName"];	
		$Price=$myRow["Price"];	
		$Qty=$myRow["Qty"];	
		$sumQty+=$Qty;
		$Amount=$myRow["Amount"];	
		$sumAmount+=$Amount;
		$Operator=$myRow["Operator"];
		$Remark=$myRow["Remark"];
		include "../model/subprogram/staffname.php";
		
		$Date=$myRow["Date"];
		$ValueArray=array(
			array(0=>$CTName),
			array(0=>$MenuName),
			array(0=>$Price,1=>"align='right'"),
			array(0=>$Qty,1=>"align='right'"),
			array(0=>$Amount,1=>"align='right'"),
			array(0=>$Operator,1=>"align='center'"),
			array(0=>$Remark."&nbsp;",1=>"align='left'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
		$m=1;
		$ValueArray=array(
			array(0=>"&nbsp;"),
			array(0=>"&nbsp;"),
			array(0=>"合计",1=>"align='right'"),
			array(0=>$sumQty,1=>"align='right'"),
			array(0=>sprintf("%.2f",$sumAmount),1=>"align='right'"),
			array(0=>"&nbsp;"),
			array(0=>"&nbsp;")
			);
		include "../model/subprogram/read_model_6.php";
	}
else{
	noRowInfo($tableWidth);
  	}

?>