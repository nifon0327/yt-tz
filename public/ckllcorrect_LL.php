<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=8;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 领料分析");
$funFrom="stuffreport";
$nowWebPage=$funFrom."_read";
//$Th_Col="操作|40|序号|40|配件Id|50|配件名称|210|参考买价|60|初始库存|60|在库|60|可用库存|60|订单总数|60|采购总数|60|入库总数|60|领料总数|60|备品入库|60|报废总数|60|退换总数|60|补仓总数|60";
//$Th_Col="操作|30|40|序号|领料日期|70|备注|40|领料人|45|配件ID|50|配件名称|200|需领料数|55|本次领料|55|采购单流水号|90|所属产品名称|200|业务单流水号|90|所属订单PO|70";
$Th_Col="序号|40|处理结果|200|配件ID|50|配件名称|200|需领料数|55|本次领料|55|采购单流水号|90|所属产品名称|300|业务单流水号|90";

$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 500;
$ActioToS="";

//步骤3：
include "../model/subprogram/read_model_3.php";
 //echo "<input type='button' name='Desk_Old_Ver' id='Desk_Old_Ver' value='修正出了货但没领料问题'  onclick='ToOld();' /> ";
//步骤4：需处理-条件选项
//echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);

//1、领了料没业务订单号的，或没有采购流水号



//3.出了货，但领料数量 不相等的,不等于采购订单号,这个有可能业务下错单，后来又重新更改了,但更改前已领料了,只用来参考。
$mySql="select '3' as sign,D.StuffId,D.StuffCname,G.OrderQty,S.Id,S.Qty,S.StockId as StockId,P.cName,C.PorderId as PorderId
FROM $DataIn.ck5_llsheet  S
LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
LEFT JOIN $DataIn.ch1_shipsheet C  ON C.POrderId=G.POrderId
LEFT JOIN $DataIn.productdata P ON P.ProductId=C.ProductId
WHERE  S.StockId='$StockId' AND (C.POrderId is Not NULL ) order by  S.ID   ";


//echo "$mySql";
$LastStockId="";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
$rowscount=mysql_num_rows($result); //获取当前记录数
$row=1; //表示当前记录数
//$PQty=-1; //表示应该领料数;
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		//sign,D.StuffId,D.StuffCname,G.OrderQty,S.Qty,G.StockId as StockId,P.cName,C.PorderId as PorderId
		$sign=$myRow["sign"];
		$StuffId=$myRow["StuffId"]==""?"&nbsp;":$myRow["StuffId"];		
		$StuffCname=$myRow["StuffCname"]==""?"&nbsp;":$myRow["StuffCname"];
		$OrderQty=$myRow["OrderQty"]==""?"&nbsp;":$myRow["OrderQty"];
		$Id=$myRow["Id"];
		$Qty=$myRow["Qty"]==""?"&nbsp;":$myRow["Qty"];
		$StockId=$myRow["StockId"]==""?"&nbsp;":$myRow["StockId"];
		
		if(is_null($PQty)){
			//echo "A:$row:PQty:$PQty <br> Qty:$Qty <br>";
			$PQty=$OrderQty;  //应该领料数
		}
		$LLog="";
		$upSign=0;
		//echo "$row:PQty:$PQty <br> Qty:$Qty";
		if($row==$rowscount ){     //说明是最后一条记录,或当前只有一条记录时，则要更新些记录
			if($PQty>0){   //把当前领料的记录还没完成;
				$upSign=1;  
			}
			else{
				$upSign=2;   // 删除当前领料记录
			}
		}
		else{			
			if($PQty>0 ){   //说明需要处理当前记录，更新当前LL记录
				if($PQty>=$Qty) {   //大于等于领料记录，则不用处理当前记录，减少应领料记录
					$PQty=$PQty-$Qty; //说明
					$upSign=0;
				}
				else{   //要更新当前领料记录，并更新库存
					$upSign=1;  
				}
			}
			else{
				$upSign=2;  // 删除当前领料记录
			}
		}
		
		switch($upSign){
			case 0: //对当前领料记录不用操作
					$LLog="不用更新!";
					break;
			case 1: //更新当前领料记录、库存
					$returnqty=$Qty-$PQty;  //要补回仓库，1、负数时，减少库存，2、正数时，增加库存
					$LLog="领料更新!";	
					//更新在库:
					$signUpSql="UPDATE $DataIn.ck5_llsheet  S
					LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=S.StuffId
					SET S.Qty=$PQty,K.tStockQty=K.tStockQty+($returnqty) 
					where S.Id=$Id";
					//echo "$signUpSql <br>";
					$signUpResult = mysql_query($signUpSql);
					if($signUpResult && mysql_affected_rows()>0){
						$KLog="更新库存成功!";						
						}
					else{
						$KLog="更新库存失败!";
						}	
					$PQty=0; //说明全部更新完毕
					$Qty=$PQty;
					break;
			case 2: //更新当前领料记、库存
					$returnqty=$Qty;  //要补回仓库
					$LLog="领料删除!";	
					//更新在库:
					$signUpSql="UPDATE $DataIn.ck5_llsheet  S
					LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=S.StuffId
					SET S.Qty=0,K.tStockQty=K.tStockQty+$returnqty 
					where S.Id=$Id";
					//echo "$signUpSql <br>";
					$signUpResult = mysql_query($signUpSql);
					if($signUpResult && mysql_affected_rows()>0){
						$KLog="更新库存成功!";						
						}
					else{
						$KLog="更新库存失败!";
						}	
					$PQty=0; //说明全部更新完毕
					$Qty=0;
					break;					
		}
		
		
		
		$cName=$myRow["cName"]==""?"&nbsp;":$myRow["cName"];		
		$PorderId=$myRow["PorderId"]==""?"&nbsp;":$myRow["PorderId"];
        $Opration="&nbsp;";
		
			$error=" $LLog : $KLog ";
			$curPorderId=$PorderId;	
			$Log=$error."$signUpSql";

			$ChooseOut="N";
			$ValueArray=array(
				array(0=>$error,
						 1=>" $errorcolor align='Left'"),			  
				array(0=>$StuffId,
						 1=>"align='Left'"),
				array(0=>$StuffCname,
						 3=>"..."),
				array(0=>$OrderQty,
						 1=>"align='right'"),
				array(0=>$Qty,					
						 1=>"align='right'"),
				array(0=>$StockId,
						 1=>"align='Left'"),
				array(0=>$cName,
						 1=>"align='Left'"),
				array(0=>$PorderId,
						 1=>"align='Left'"),
				
				);
			$checkidValue=$Id;
			include "../model/subprogram/read_model_6.php";
			
			$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
			$IN_res=@mysql_query($IN_recode);
		
		$row=$row+1; //下一条记录	
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';

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