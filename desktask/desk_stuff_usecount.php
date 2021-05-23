<?php 
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=13;
$tableMenuS=500;
ChangeWtitle("$SubCompany 配件采购/入库/出库统计");
$funFrom="cg_stuffqty";
$From=$From==""?"read":$From;
$sumCols="5,6,7,8,9,10,11,12,13,14";			//求和列,需处理
$Th_Col="选项|60|序号|30|配件ID|45|配件名称|320|单位|40|上月库存<br>结余总数|80|上月库存<br>结余金额|80|本月<br>采购总数|80|本月<br>采购金额|80|本月<br>入库数量|80|本月<br>入库金额|80|本月<br>领料数量|80|本月<br>领料金额|80|本月库存<br>结余总数|80|本月库存<br>结余金额|80";
//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 300;							//每页默认记录数量
$ActioToS="1";
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-可选条件下拉框
if($From!="slist"){	//非查询：过滤采购、结付方式、供应商、月份

	echo"<select name='ChooseMonth' id='ChooseMonth' onchange='document.form1.submit()'>";
	$DefaultMonth="2015-01-01";
	$NewMonth=date("Y-m");
	$Months=intval(abs((date("Y")-2015)*12+date("m")));
	for($i=$Months-1;$i>=0; $i--){
		$dateValue=date("Y-m",strtotime("$i month", strtotime($DefaultMonth))); 
		$ChooseMonth=$ChooseMonth==""?$dateValue:$ChooseMonth;
		if($ChooseMonth==$dateValue){
			echo"<option value='$dateValue' selected>$dateValue</option>";
			$SearchRowsA = " AND DATE_FORMAT(M.Date,'%Y-%m')='$dateValue'";
			$SearchRowsB = " AND DATE_FORMAT(R.created,'%Y-%m')='$dateValue'";
			$SearchRowsC = " AND DATE_FORMAT(L.created,'%Y-%m')='$dateValue'";
			
			$SearchRowsD = " AND DATE_FORMAT(R.created,'%Y-%m')<'$dateValue'";
			$SearchRowsE = " AND DATE_FORMAT(L.created,'%Y-%m')<'$dateValue'";
			$SearchRowsD2 = " AND DATE_FORMAT(R.created,'%Y-%m')<='$dateValue'";
			$SearchRowsE2 = " AND DATE_FORMAT(L.created,'%Y-%m')<='$dateValue'";
			}
		else{
			echo"<option value='$dateValue'>$dateValue</option>";					
		  }
	}
	echo "</select>";		
	
	$typeSql = mysql_query("SELECT  T.TypeId,T.TypeName,T.Letter
	FROM  $DataIn.stuffdata D 
	LEFT JOIN $DataIn.stuffunit U ON U.Id=D.Unit
	LEFT JOIN $DataIn.stufftype T ON T.TypeId = D.TypeId
	LEFT JOIN $DataIn.stuffmaintype MT ON MT.Id = T.mainType
	WHERE MT.blSign>0  
	AND ( EXISTS (SELECT G.StuffId FROM $DataIn.cg1_stocksheet G 
              LEFT JOIN $DataIn.cg1_stockmain M ON M.Id = G.Mid 
              WHERE  G.StuffId = D.StuffId AND G.Mid>0 AND G.blSign='1' $SearchRowsA GROUP BY G.StuffId) OR  
      EXISTS (SELECT R.StuffId FROM $DataIn.ck1_rksheet R WHERE  R.StuffId = D.StuffId $SearchRowsD2 GROUP BY R.StuffId ) OR 
      EXISTS (SELECT L.StuffId FROM $DataIn.ck5_llsheet L WHERE  L.StuffId = D.StuffId $SearchRowsE2 GROUP BY L.StuffId ) 
     ) GROUP BY T.TypeId  ORDER BY T.Letter",$link_id);
	if($typeRow = mysql_fetch_array($typeSql)){
		 echo "<select name='TypeId' id='TypeId' onchange='document.form1.submit()'>";
		 echo"<option value='' selected>全部</option>";
		do{
			$TypeName=$typeRow["TypeName"];
			$Letter=$typeRow["Letter"];
			$TypeName = $Letter."-".$TypeName;
			$thisTypeId=$typeRow["TypeId"];
			if($TypeId==$thisTypeId){
				echo"<option value='$thisTypeId' selected>$TypeName </option>";
				$SearchRows.=" and D.TypeId='$thisTypeId'";
				}
			else{
				echo"<option value='$thisTypeId'>$TypeName</option>";
				}
			}while ($typeRow = mysql_fetch_array($typeSql));
		   echo"</select>&nbsp;";
		}
	
	
	 
}
	

echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr";

//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$recordsNums = 0;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT D.StuffId,D.StuffCname,D.Picture,U.Name AS UnitName,T.TypeName,MT.TypeColor,D.Price
FROM  $DataIn.stuffdata D 
LEFT JOIN $DataIn.stuffunit U ON U.Id=D.Unit
LEFT JOIN $DataIn.stufftype T ON T.TypeId = D.TypeId
LEFT JOIN $DataIn.stuffmaintype MT ON MT.Id = T.mainType
LEFT JOIN $DataIn.stuffproperty P ON D.StuffId =P.StuffId AND P.Property=10
WHERE MT.blSign>0  AND P.StuffId  IS NULL $SearchRows
AND ( EXISTS (SELECT G.StuffId FROM $DataIn.cg1_stocksheet G 
              LEFT JOIN $DataIn.cg1_stockmain M ON M.Id = G.Mid 
              WHERE  G.StuffId = D.StuffId  AND G.Mid>0 AND G.blSign='1' $SearchRowsA GROUP BY G.StuffId ) OR  
      EXISTS (SELECT R.StuffId FROM $DataIn.ck1_rksheet R WHERE  R.StuffId = D.StuffId $SearchRowsD2 GROUP BY R.StuffId ) OR 
      EXISTS (SELECT L.StuffId FROM $DataIn.ck5_llsheet L WHERE  L.StuffId = D.StuffId $SearchRowsE2 GROUP BY L.StuffId ) 
     )";

$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$StuffId=$myRow["StuffId"];
		$StuffCname=$myRow["StuffCname"];
		$Picture=$myRow["Picture"];
		include "../model/subprogram/stuffimg_model.php";
        include"../model/subprogram/stuff_Property.php";//配件属性
		$UnitName=$myRow["UnitName"];
		$TypeColor=$myRow["TypeColor"];
		$Price=$myRow["Price"];

        //采购数量
        $cgRow=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(G.AddQty+G.FactualQty),0) AS Qty,
        IFNULL(SUM((G.AddQty+G.FactualQty)*G.Price*C.Rate),0) AS cgAmount
		FROM $DataIn.cg1_stocksheet G
		LEFT JOIN $DataIn.cg1_stockmain M ON M.Id = G.Mid 
		LEFT JOIN $DataIn.stuffdata D ON D.StuffId = G.StuffId
		LEFT JOIN $DataIn.trade_object O ON O.CompanyId = G.CompanyId 
		LEFT JOIN $DataIn.currencydata C ON C.Id = O.Currency
		WHERE 1 $SearchRows $SearchRowsA AND G.blSign='1' AND (G.FactualQty+G.AddQty)>0 AND G.Mid>0 AND  G.StuffId='$StuffId' ",$link_id));
		$cgQty=$cgRow["Qty"];
		$cgAmount=sprintf("%.2f", $cgRow["cgAmount"]);
		
        //入库数量
        $rkRow=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(R.Qty),0) AS Qty,SUM(R.Qty*R.Price) AS rkAmount
		FROM $DataIn.ck1_rksheet R 
		LEFT JOIN $DataIn.stuffdata D ON D.StuffId = R.StuffId
		WHERE  1 $SearchRows $SearchRowsB AND R.Type !=4 AND R.StuffId='$StuffId'  ",$link_id));
		$rkQty=$rkRow["Qty"];
		$rkAmount=sprintf("%.2f", $rkRow["rkAmount"]);
        
        
        //领料数量
        $llRow=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(L.Qty),0) AS Qty,SUM(L.Qty*L.Price) AS llAmount
		FROM $DataIn.ck5_llsheet L 
		LEFT JOIN $DataIn.stuffdata D ON D.StuffId = L.StuffId
		WHERE  1 $SearchRows $SearchRowsC AND L.Type!=5 AND L.StuffId='$StuffId' ",$link_id));
		$llQty=$llRow["Qty"];
		$llAmount=sprintf("%.2f", $llRow["llAmount"]);
		
		 //入库数量
        $LastrkRow=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(R.Qty),0) AS Qty,SUM(R.Qty*R.Price) AS rkAmount
		FROM $DataIn.ck1_rksheet R 
		LEFT JOIN $DataIn.stuffdata D ON D.StuffId = R.StuffId
		WHERE  1 $SearchRows $SearchRowsD AND R.Type !=4 AND R.StuffId='$StuffId'  ",$link_id));
		$LastrkQty=$LastrkRow["Qty"];
		$LastrkAmount=sprintf("%.2f", $LastrkRow["rkAmount"]);
        
        
        //领料数量
        $LastllRow=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(L.Qty),0) AS Qty,SUM(L.Qty*L.Price) AS llAmount
		FROM $DataIn.ck5_llsheet L 
		LEFT JOIN $DataIn.stuffdata D ON D.StuffId = L.StuffId
		WHERE  1 $SearchRows $SearchRowsE AND L.Type!=5 AND L.StuffId='$StuffId' ",$link_id));
		$LastllQty=$LastllRow["Qty"];
		$LastllAmount=sprintf("%.2f", $LastllRow["llAmount"]);
		
		$LastQty = sprintf("%.1f", $LastrkQty - $LastllQty);
		$LastAmount = $LastQty>0?sprintf("%.2f",$LastrkAmount  - $LastllAmount):0;

        $stockQty =  sprintf("%.1f",$LastQty +$rkQty - $llQty);
       $stockAmount = $stockQty>0? sprintf("%.2f", $LastAmount +$rkAmount -$llAmount ):0;
       
       if ($LastQty==0 && $cgQty==0 && $rkQty==0 && $llQty==0 && $stockQty==0) continue;
       
       $LastAmount   = $LastAmount>0?$LastAmount:0;
       $stockAmount = $stockAmount>0?$stockAmount:0;
       
       $SumLastQty+= $LastQty;     $SumLastAmount+= $LastAmount;
       $SumcgQty+= $cgQty;          $SumcgAmount+= $cgAmount;
       $SumllQty+= $llQty;               $SumllAmount+= $llAmount;
       $SumrkQty+= $rkQty;            $SumrkAmount+= $rkAmount;
       $SumstockQty+= $stockQty; $SumstockAmount+= $stockAmount;
       
        $ValueArray=array(
			array(0=>$StuffId,		1=>"align='center'"),
			array(0=>$StuffCname),
			array(0=>$UnitName,	 	1=>"align='center'"),
			array(0=>$LastQty,		1=>"align='right'"),
			array(0=>$LastAmount,     1=>"align='right'"),
			array(0=>$cgQty,		1=>"align='right'"),
			array(0=>$cgAmount,     1=>"align='right'"),
			array(0=>$rkQty, 	    1=>"align='right'"),
			array(0=>$rkAmount, 	1=>"align='right'"),
			array(0=>$llQty, 	    1=>"align='right'"),
			array(0=>$llAmount, 	1=>"align='right'"),
			array(0=>$stockQty, 	    1=>"align='right'"),
			array(0=>$stockAmount, 	1=>"align='right'")
			);
		$checkidValue=$StuffId;
		include "../model/subprogram/read_model_6.php";
		
		$recordNums++;
		}while ($myRow = mysql_fetch_array($myResult));
		
		    $m=1;
			$ValueArray=array(
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>$SumLastQty,		1=>"align='right'"),
				array(0=>$SumLastAmount,     1=>"align='right'"),
				array(0=>$SumcgQty,		1=>"align='right'"),
				array(0=>$SumcgAmount,     1=>"align='right'"),
				array(0=>$SumrkQty, 	    1=>"align='right'"),
				array(0=>$SumrkAmount, 	1=>"align='right'"),
				array(0=>$SumllQty, 	    1=>"align='right'"),
				array(0=>$SumllAmount, 	1=>"align='right'"),
				array(0=>$SumstockQty, 	    1=>"align='right'"),
				array(0=>$SumstockAmount, 	1=>"align='right'")
				);
			$ShowtotalRemark="合计";
			$isTotal=1;
			include "../model/subprogram/read_model_total.php";		
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