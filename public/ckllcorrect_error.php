<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$Log_Item="领料记录";		
$Log_Funtion="保存";
$Operator=$Login_P_Number;
$DateTime=date("Y-m-d H:i:s");
$ColsNumber=7;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 领料更正");
$funFrom="stuffreport";
$nowWebPage=$funFrom."_read";
//$Th_Col="操作|40|序号|40|配件Id|50|配件名称|210|参考买价|60|初始库存|60|在库|60|可用库存|60|订单总数|60|采购总数|60|入库总数|60|领料总数|60|备品入库|60|报废总数|60|退换总数|60|补仓总数|60";
//$Th_Col="操作|30|40|序号|领料日期|70|备注|40|领料人|45|配件ID|50|配件名称|200|需领料数|55|本次领料|55|采购单流水号|90|所属产品名称|200|业务单流水号|90|所属订单PO|70";
$Th_Col="序号|40|处理结果|400|配件ID|50|配件名称|200|本次领料|55|采购单流水号|90|所属产品名称|300|业务单流水号|90|";

$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 500;
$ActioToS="";

//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
//echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);

//$LockSql=" LOCK TABLES $DataIn.ck5_llmain M  WRITE,$DataIn.ck5_llmain WRITE,$DataIn.ck5_llsheet S WRITE,$DataIn.ck5_llsheet WRITE,$DataIn.ck9_stocksheet K WRITE,$DataIn.ck9_stocksheet WRITE";$LockRes=@mysql_query($LockSql);

//1、领了料没业务订单号的，或没有采购流水号
$mySql="select * from (";
/*
$mySql.="select '1' as sign,D.StuffId,D.StuffCname,G.OrderQty,S.Qty,S.StockId as StockId,P.cName,Y.PorderId as PorderId
FROM $DataIn.ck5_llsheet S
LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=G.POrderId
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
WHERE Y.PorderId is NULL or G.StockID is NULL  ";
*/
//2、出了货但没领料的!
$mySql.="   
select '2' as sign,D.StuffId,D.StuffCname,G.OrderQty,S.Qty,G.StockId as StockId,P.cName,C.PorderId as PorderId
FROM $DataIn.ch1_shipsheet C
LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=C.POrderId
LEFT JOIN $DataIn.ck5_llsheet S ON S.StockId=G.StockId
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
LEFT JOIN $DataIn.productdata P ON P.ProductId=C.ProductId
WHERE C.POrderId=$PorderId  AND S.StockId is NULL AND G.OrderQty>0 AND T.mainType<2";

/*
//3.出了货，但领料数量 不相等的,不等于采购订单号,这个有可能业务下错单，后来又重新更改了,但更改前已领料了,只用来参考。
$mySql.="  UNION ALL 
select '3' as Sign,S.StockId as StockId,C.PorderId as PorderId
FROM (select sum(Qty) as Qty, StockId from$DataIn.ck5_llsheet GROUP BY StockId) S
LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId
LEFT JOIN $DataIn.ch1_shipsheet C  ON C.POrderId=G.POrderId
WHERE (S.StockId is not NULL) AND (C.POrderId is Not NULL ) AND  S.Qty!=G.OrderQty    ";
*/

$mySql.=") A order by sign,PorderId desc,StockId ";
//echo "$mySql";

$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		//sign,D.StuffId,D.StuffCname,G.OrderQty,S.Qty,G.StockId as StockId,P.cName,C.PorderId as PorderId
		$sign=$myRow["sign"];
		$StuffId=$myRow["StuffId"];		
		$StuffCname=$myRow["StuffCname"]==""?"&nbsp;":$myRow["StuffCname"];
		$OrderQty=$myRow["OrderQty"]==""?"&nbsp;":$myRow["OrderQty"];
		$Qty=$myRow["Qty"]==""?"&nbsp;":$myRow["Qty"];
		$StockId=$myRow["StockId"];
		$cName=$myRow["cName"]==""?"&nbsp;":$myRow["cName"];		
		$PorderId=$myRow["PorderId"];
		//$curPorderId=$curPorderId==""?"$PorderId":$curPorderId;  //当第一条记录时
		//锁定表，以免重复写

		
		//生成主领料单,一个采购单生成一个，以免出错
		if($curPorderId!=$PorderId){ //不同一个PorderId则要新插入一个领料单
		    $llMid=0; //清空，以没生成时还用原来的成是失误
			$llinRecode="INSERT INTO $DataIn.ck5_llmain  (Id,Materieler,Remark,Locks,Date,Operator) VALUES (NULL,'0','','0','$DateTime','$Operator')";
			//echo "$llinRecode <br>"; // $llMid=1; //模拟测试  
			$llAction=@mysql_query($llinRecode);
			$llMid=mysql_insert_id();
		}
		if($llMid!=0 && $llMid!=""){
			$PLog="生成主领料单($llMid)成功!";
			//领料明细
			$llInSql=$DataIn !== 'ac' ? "INSERT INTO $DataIn.ck5_llsheet 
			SELECT NULL,'$llMid',G.StockId,G.StuffId,G.OrderQty,'0' 
			FROM $DataIn.cg1_stocksheet G 
			LEFT JOIN $DataIn.ch1_shipsheet Y ON Y.POrderId=G.POrderId 
			LEFT JOIN $DataIn.ck5_llsheet S ON S.StockId=G.StockId
			WHERE  S.StockId is NULL AND Y.POrderId=$PorderId AND G.StockId=$StockId " : 
			                             "INSERT INTO $DataIn.ck5_llsheet 
			SELECT NULL,'$llMid',G.StockId,G.StuffId,G.OrderQty,'0', 1,0,'$Operator','$DateTime','$Operator','$DateTime','$DateTime','$Operator'
			FROM $DataIn.cg1_stocksheet G 
			LEFT JOIN $DataIn.ch1_shipsheet Y ON Y.POrderId=G.POrderId 
			LEFT JOIN $DataIn.ck5_llsheet S ON S.StockId=G.StockId
			WHERE  S.StockId is NULL AND Y.POrderId=$PorderId AND G.StockId=$StockId ";   //一个一个配件加进去!!!
			//echo "$llInSql <br>";
			$llInAction=@mysql_query($llInSql);
			if($llInAction && mysql_affected_rows()>0){
				$LLog="领料成功!";	
				//更新在库:需统计后更新
				$signUpSql="UPDATE (SELECT G.StuffId,G.OrderQty AS OrderQty 
				FROM $DataIn.cg1_stocksheet G  where G.StockId=$StockId) A
				LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=A.StuffId
				SET K.tStockQty=K.tStockQty-A.OrderQty";
				//echo "$signUpSql <br>";
				$signUpResult = mysql_query($signUpSql);
				if($signUpResult && mysql_affected_rows()>0){
					$KLog="更新库存成功!";						
					}
				else{
					$KLog="更新库存失败!";
					}				
				
				}
			else{
				$LLog="领料失败!";
				}
	

			}
		else{
			$PLog="生成主领料单失败!";
			
			}
		
		$error="$PLog : $LLog : $KLog ";
		$curPorderId=$PorderId;	
		$Log=$error."ch1_shipsheet表PorderId: $PorderId "."StuffId: $StuffId "."OrderQty:$OrderQty";

		
		$myOpration="";
		$ChooseOut="N";
		$ValueArray=array(
			array(0=>$error,
					 1=>"align='Left'"),			  
			array(0=>$StuffId,
					 1=>"align='Left'"),
			array(0=>$StuffCname,
					 3=>"..."),
			array(0=>$OrderQty,
					 1=>"align='right'"),
			array(0=>$StockId,
					 1=>"align='Left'"),
			array(0=>$cName,
					 1=>"align='Left'"),
			array(0=>$PorderId,
					 1=>"align='Left'"),
			
			);
		$checkidValue=$Id;
		
		$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
		$IN_res=@mysql_query($IN_recode);

		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';

//$unLockSql="UNLOCK TABLES";$unLockRes=@mysql_query($unLockSql);

List_Title($Th_Col,"0",0);
pBottom($i-1,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
echo "<input type='button' name='Desk_Old_Ver' id='Desk_Old_Ver' value='返回领料分析'  onclick='ToOld();' /> ";
?>




<script language="javascript" type="text/JavaScript">
function ToOld()
{
	window.open("ckllreport_error.php","_self");

}
</script>