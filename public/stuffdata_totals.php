<?php 
include "../model/modelhead.php";
echo"<html><head><META content='MSHTML 6.00.2900.2722' name=GENERATOR>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<link rel='stylesheet' href='../model/css/read_line.css'>
<link rel='stylesheet' href='../model/css/sharing.css'>
<link rel='stylesheet' href='../model/keyright.css'>
<script src='../model/pagefun_Sc.js' type=text/javascript></script>
<script src='../model/checkform.js' type=text/javascript></script>
<script language='javascript' type='text/javascript' src='../model/DatePicker/WdatePicker.js'></script></head>";
//步骤2：需处理
$Keys=31;
$ColsNumber=10;
$tableMenuS=1000;
$sumCols="18,19";			//求和列,需处理
ChangeWtitle("$SubCompany 配件库存列表");
$funFrom="stuffdata";
$From=$From==""?"forbidden":$From;
$Th_Col="选项|55|序号|40|配件Id|50|配件名称|280|历史<br>订单|40|状态|30|上月结余数量|80|上月结余金额|80|本月入库数量|80|本月入库金额|80|本月领料数量|80|本月领料金额|80|本月库存数量|80|本月库存金额|80";
$Pagination=$Pagination==""?0:$Pagination;
//$Page_Size = 200;
//$ActioToS="1";//,151
$nowWebPage=$funFrom."_totals";
include "../model/subprogram/read_model_3.php";

//if($From!="slist"){
	$SearchRows="";
	$SearchRowsA="";
	$DefaultMonth="2008-01-01";
	$NewMonth=date("Y-m");
	$Months=intval(abs((date("Y")-2008)*12+date("m")));
	for($i=$Months-1;$i>=0;$i--){
		$dateValue=date("Y-m",strtotime("$i month", strtotime($DefaultMonth))); 
		$chooseMonth=$chooseMonth==""?$dateValue:$chooseMonth;
			if($chooseMonth==$dateValue){
				$optionStr.="<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows.=" AND DATE_FORMAT(M.Date ,'%Y-%m')='$dateValue' ";
				$SearchRowsA=" AND DATE_FORMAT(M.Date ,'%Y-%m')<'$dateValue' ";
				
				$FrontMonth=date("Y-m",strtotime("-1 month", strtotime($dateValue)));
				$SearchRowsB=" AND DATE_FORMAT(M.Date ,'%Y-%m')>='$FrontMonth' ";
				}
			else{
				$optionStr.="<option value='$dateValue'>$dateValue</option>";					
				}
		}
      echo"<select name='chooseMonth' id='chooseMonth' onchange='document.form1.submit()'>$optionStr</select>&nbsp;";
//	}
  
//}	
  echo "<input name='AcceptText' type='hidden' id='AcceptText' value='$upFlag'>";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理

$NowYear=date("Y");
$NowMonth=date("m");
$Nowtoday=date("Y-m-d");
$i=1;$ls=0;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);


$mySql="SELECT B.StuffId,IFNULL(B.cgQty,0) AS cgQty,IFNULL(B.cgAmount,0) AS cgAmount,IFNULL(B.llQty,0) AS llQty,IFNULL(B.llAmount,0) AS llAmount,S.Id,S.StuffCname,S.Estate,S.Picture,S.Price,K.dStockQty    
FROM (
		 SELECT A.StuffId,SUM(A.cgQty) AS cgQty,SUM(A.cgAmount) AS cgAmount,SUM(A.llQty) AS llQty,SUM(A.llAmount) AS llAmount
				FROM (
				SELECT S.StuffId,0 AS cgQty,0 AS cgAmount,0 AS llQty,0 AS llAmount
				FROM  $DataIn.stuffdata S  
				LEFT JOIN $DataIn.ck9_stocksheet T ON T.StuffId=S.StuffId  
				WHERE T.tStockQty>0 OR T.dStockQty>0  
				GROUP BY S.StuffId
		UNION ALL
		       SELECT L.StuffId,0 AS cgQty,0 AS cgAmount,0 AS llQty,0 AS llAmount
				FROM $DataIn.ck5_llmain M  
				LEFT JOIN $DataIn.ck5_llsheet L ON M.Id=L.Mid 
				WHERE 1 $SearchRowsB  GROUP BY L.StuffId
		UNION ALL
		       SELECT L.StuffId,0 AS cgQty,0 AS cgAmount,0 AS llQty,0 AS llAmount
				FROM $DataIn.ck1_rkmain M  
				LEFT JOIN $DataIn.ck1_rksheet L ON M.Id=L.Mid 
				WHERE 1 $SearchRowsB  GROUP BY L.StuffId	
	   UNION ALL
		       SELECT M.StuffId,0 AS cgQty,0 AS cgAmount,0 AS llQty,0 AS llAmount
				FROM $DataIn.ck8_bfsheet M  
				WHERE 1 $SearchRowsB  GROUP BY M.StuffId 
		UNION ALL
		       SELECT M.StuffId,0 AS cgQty,0 AS cgAmount,0 AS llQty,0 AS llAmount
				FROM $DataIn.ck7_bprk M  
				WHERE 1 $SearchRowsB  GROUP BY M.StuffId 		
		UNION ALL
				SELECT R.StuffId,SUM(R.Qty) AS cgQty,SUM(R.Qty*IFNULL(S.Price,0)) AS cgAmount,0 AS llQty,0 AS llAmount
				FROM  $DataIn.ck1_rkmain M 
				LEFT JOIN $DataIn.ck1_rksheet R ON M.Id=R.Mid 
				LEFT JOIN $DataIn.cg1_stocksheet S ON S.StockId=R.StockId  
				WHERE 1  $SearchRows 
				GROUP BY R.StuffId
		
		UNION ALL 
				SELECT L.StuffId,0 AS cgQty,0 AS cgAmount,SUM(L.Qty) AS llQty,SUM(L.Qty*IFNULL(S.Price,0)) AS llAmount
				FROM $DataIn.ck5_llmain M  
				LEFT JOIN $DataIn.ck5_llsheet L ON M.Id=L.Mid 
				LEFT JOIN $DataIn.cg1_stocksheet S ON S.StockId=L.StockId 
				WHERE 1 $SearchRows  GROUP BY L.StuffId
	    UNION ALL 
				SELECT M.StuffId,0 AS cgQty,0 AS cgAmount,SUM(M.Qty) AS llQty,SUM(M.Qty*S.Price) AS llAmount 
				FROM ck8_bfsheet  M  
				LEFT JOIN $DataIn.stuffdata S ON S.StuffId=M.StuffId  
				WHERE 1 $SearchRows  and M.Estate=0 GROUP BY M.StuffId
	    UNION ALL 
				SELECT M.StuffId,SUM(M.Qty) AS cgQty,SUM(M.Qty*S.Price) AS cgAmount,0 AS llQty,0 AS llAmount
				FROM ck7_bprk  M  
				LEFT JOIN $DataIn.stuffdata S ON S.StuffId=M.StuffId  
				WHERE 1 $SearchRows  GROUP BY M.StuffId
		)A GROUP BY A.StuffId 
)B 
LEFT JOIN $DataIn.stuffdata S ON S.StuffId=B.StuffId 
LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=S.StuffId   
WHERE 1 ORDER BY cgAmount DESC,cgQty DESC,llQty DESC";
if ($Login_P_Number==10868) echo $mySql;
$sum_cgQty=0;$sum_llQty=0;$sum_cgAmount=0;$sum_llAmount=0;
$sum_tStockQty=0;$sum_tStockAmount=0;$sum_tStockQty0=0;$sum_tStockAmount0=0;$Counts0=0;$Counts=0;

//if ($Login_P_Number==10868) mysql_query("DELETE  FROM $DataIn.ck9_totals_copy",$link_id);
$myResult = mysql_query($mySql,$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);		
	
	do{
		$m=1;
		$Id=$myRow["Id"];
		$StuffId=$myRow["StuffId"];
		$StuffCname=$myRow["StuffCname"];
		$dStockQty=$myRow["dStockQty"];
		
		$Price=$myRow["Price"];
		//$Price=$myRow["Price"];
		
		$Picture=$myRow["Picture"];
		include "../model/subprogram/stuffimg_model.php";	//检查是否有图片
        include"../model/subprogram/stuff_Property.php";//配件属性
        if ($ComboxSheetSign==1 || $ReferenceSign==1) {//去除主配件及参考类
             $ls++; continue;
         }
		$Estate=$myRow["Estate"];
		switch($Estate){
			case 0:
				$Estate="<div class='redB'>×</div>";
				break;
			case 1:
				$Estate="<div class='greenB'>√</div>";
				break;
			case 2://配件名称审核中
				$Estate="<div class='yellowB' title='配件名称审核中'>√.</div>";
				break;
			}
		
		//入库数量
		$cgQtyResult=mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS cgQty,SUM(S.Qty*G.Price) AS cgAmount  
		FROM $DataIn.ck1_rkmain M
		LEFT JOIN $DataIn.ck1_rksheet S  ON M.Id=S.Mid 
		LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId 
		WHERE 1 $SearchRowsA  AND S.StuffId='$StuffId' ",$link_id));

		$cg_TotalQty0=$cgQtyResult["cgQty"];
		$cg_TotalAmount0=$cgQtyResult["cgAmount"];
		
		$rkQtyResult=mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS rkQty,SUM(S.Qty*G.Price) AS rkAmount  
		FROM $DataIn.ck1_rksheet S 
		LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId 
		WHERE S.Mid=0  AND S.StuffId='$StuffId' ",$link_id));
		$cg_TotalQty0+=$rkQtyResult["rkQty"];
		$cg_TotalAmount0+=$rkQtyResult["rkAmount"];
		
		 //领料数量
		$llQtyResult=mysql_fetch_array(mysql_query("SELECT SUM(L.Qty) AS llQty,SUM(L.Qty*G.Price) AS llAmount  
		FROM $DataIn.ck5_llmain M  
		LEFT JOIN $DataIn.ck5_llsheet L ON M.Id=L.Mid 
		LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=L.StockId  
		WHERE 1 $SearchRowsA AND L.StuffId='$StuffId'  ",$link_id)); 
        $ll_TotalQty0=$llQtyResult["llQty"]==""?0:$llQtyResult["llQty"];
        $ll_TotalAmount0=$llQtyResult["llAmount"];
        
        $llQtyResult2=mysql_fetch_array(mysql_query("SELECT SUM(L.Qty) AS llQty,SUM(L.Qty*G.Price) AS llAmount  
		FROM $DataIn.ck5_llmain M  
		LEFT JOIN $DataIn.ck5_llsheet L ON M.Id=L.Mid 
		LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=L.StockId  
		WHERE L.Mid=0 AND L.Estate=0 AND L.StuffId='$StuffId'  ",$link_id)); 
		$nomllQty=$llQtyResult2["llQty"]==""?0:$llQtyResult2["llQty"];
		$nomllAmount=$llQtyResult2["llAmount"];
		
        $ll_TotalQty0+=$nomainllQty;
        $ll_TotalAmount0+=$nomainllQty;
        
        //报废数量
        $bfQtyResult=mysql_fetch_array(mysql_query("SELECT SUM(M.Qty) AS bfQty  
		FROM $DataIn.ck8_bfsheet M   
		WHERE 1 $SearchRowsA AND M.StuffId='$StuffId' and M.Estate=0 ",$link_id)); 
        $bf_TotalQty0=$bfQtyResult["bfQty"]==""?0:$bfQtyResult["bfQty"];
        
        //备品转入数量
        $bpQtyResult=mysql_fetch_array(mysql_query("SELECT SUM(M.Qty) AS bpQty  
		FROM $DataIn.ck7_bprk M   
		WHERE 1 $SearchRowsA AND M.StuffId='$StuffId'",$link_id)); 
        $bp_TotalQty0=$bpQtyResult["bpQty"]==""?0:$bpQtyResult["bpQty"];
        
        $tStockQty0=$dStockQty+$cg_TotalQty0+$bp_TotalQty0-$ll_TotalQty0-$bf_TotalQty0;
        
        //$tStockQty0=$tStockQty0<0?0:$tStockQty0;
        $tStockAmount0=$cg_TotalAmount0+$bp_TotalQty0*$Price-$ll_TotalAmount0-$bf_TotalQty0*$Price;
        $tStockAmount0=$tStockAmount0<0?0:$tStockAmount0;
        if ($tStockQty0>0) $Counts0++;
        
		$cgQty=$myRow["cgQty"];
		$llQty=$myRow["llQty"];
        
        $cgAmount=round($myRow["cgAmount"],2);
		$llAmount=round($myRow["llAmount"],2);
		
		if ($tStockQty0==0 and $cgQty==0 and $llQty==0) {
		      $ls++;
		      continue;
		}
	
		$sum_tStockQty0+=$tStockQty0;
        $sum_tStockAmount0+=$tStockAmount0;
		
		/*
		//本月报废数量
        $bfQtyResult=mysql_fetch_array(mysql_query("SELECT SUM(M.Qty) AS bfQty  
		FROM $DataIn.ck8_bfsheet M   
		WHERE 1 $SearchRows AND M.StuffId='$StuffId'",$link_id)); 
        $bf_TotalQty=$bfQtyResult["bfQty"]==""?0:$bfQtyResult["bfQty"];
        
        //本月备品转入数量
        $bpQtyResult=mysql_fetch_array(mysql_query("SELECT SUM(M.Qty) AS bpQty  
		FROM $DataIn.ck7_bprk M   
		WHERE 1 $SearchRows AND M.StuffId='$StuffId'",$link_id)); 
        $bp_TotalQty=$bpQtyResult["bpQty"]==""?0:$bpQtyResult["bpQty"];
        
		$tStockQty=$tStockQty0+$cgQty+$bp_TotalQty-$llQty-$bf_TotalQty;
		$tStockQty=$tStockQty<0?0:$tStockQty;
        $tStockAmount=$tStockAmount0+$cgAmount-$llAmount+($bp_TotalQty-$bf_TotalQty)*$Price;
        $tStockAmount=$tStockAmount<0?0:$tStockAmount;
        */
        $tStockQty=$tStockQty0+$cgQty-$llQty;
		//$tStockQty=$tStockQty<0?0:$tStockQty;
        $tStockAmount=$tStockAmount0+$cgAmount-$llAmount;
        //if ($StuffId==94777) echo "$tStockQty=$tStockQty0+$cgQty-$llQty";

        $tStockAmount=$tStockAmount<0?0:$tStockAmount;
        if ($tStockQty>0) $Counts++;
        //$cgQty+=$bp_TotalQty;
        //$llQty+=$bf_TotalQty;
        
        $sum_tStockQty+=$tStockQty;
        $sum_tStockAmount+=$tStockAmount;
		
		$sum_cgQty+=$cgQty;$sum_llQty+=$llQty;
		$sum_cgAmount+=$cgAmount;$sum_llAmount+=$llAmount;
		
		
		$cgQty=$cgQty==0?"&nbsp;":number_format($cgQty);
		$llQty=$llQty==0?"&nbsp;":number_format($llQty);
		$tStockQty=$tStockQty==0?"&nbsp;":number_format($tStockQty);
		$tStockQty0=$tStockQty0==0?"&nbsp;":number_format($tStockQty0);
        
        $cgAmount=$cgAmount==0?"&nbsp;":number_format($cgAmount,2);
		$llAmount=$llAmount==0?"&nbsp;":number_format($llAmount,2);
		$tStockAmount=$tStockAmount==0?"&nbsp;":number_format($tStockAmount,2);
		$tStockAmount0=$tStockAmount0==0?"&nbsp;":number_format($tStockAmount0,2);
		
        $OrderQtyInfo="<a href='cg_historyorder.php?StuffId=$StuffId&Id=' target='_blank'>查看</a>";
        
        if ($Login_P_Number==10868){
           $insertSql="INSERT INTO $DataIn.ck9_totals(Month,StuffId,RkQty,LlQty,tStockQty,FtMonth,FtQty) Values('$chooseMonth',$StuffId,'$cgQty','$llQty','$tStockQty','$FrontMonth','$tStockQty0')";
           $insertResult=mysql_query($insertSql,$link_id);
        }
        
        
        
        $theParam="StuffId=$StuffId" . "&Month=$chooseMonth";
		$cgQty=	"<a href='stuffdata_totals_list.php?$theParam&Sign=1' target='_blank'>$cgQty</a>";
		$llQty=	"<a href='stuffdata_totals_list.php?$theParam&Sign=2' target='_blank'>$llQty</a>";
		
		$ValueArray=array(
			array(0=>$StuffId, 		1=>"align='center'"),
			array(0=>$StuffCname),
            array(0=>$OrderQtyInfo, 1=>"align='center'"),
            array(0=>$Estate, 		1=>"align='center'"),
            array(0=>$tStockQty0, 	    1=>"align='right'"),
			array(0=>$tStockAmount0, 	1=>"align='right'"),
			array(0=>$cgQty, 	    1=>"align='right'"),
			array(0=>$cgAmount, 	1=>"align='right'"),
			array(0=>$llQty, 	    1=>"align='right'"),
			array(0=>$llAmount, 	1=>"align='right'"),
            array(0=>$tStockQty, 		1=>"align='right'"),
            array(0=>$tStockAmount, 		1=>"align='right'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		echo $StuffListTB;
		}while ($myRow = mysql_fetch_array($myResult));
		
		
	}
else{
	noRowInfo($tableWidth);
  	}
$m=1; 	
$ValueArray=array(
		array(0=>"&nbsp;"	),
		array(0=>"&nbsp;"	),
		array(0=>"&nbsp;"	),
		array(0=>"&nbsp;"	),
		array(0=>"<div title='$Counts0'>".number_format($sum_tStockQty0)."</div>", 1=>"align='right'"),
		array(0=>"<div >".number_format($sum_tStockAmount0,2)."</div>", 1=>"align='right'"),
		array(0=>"<div >".number_format($sum_cgQty)."</div>", 1=>"align='right'"),
		array(0=>"<div >".number_format($sum_cgAmount,2)."</div>", 1=>"align='right'"),
		array(0=>"<div >".number_format($sum_llQty)."</div>", 	1=>"align='right'"),
		array(0=>"<div >".number_format($sum_llAmount,2)."</div>", 1=>"align='right'"),
		array(0=>"<div title='$Counts'>".number_format($sum_tStockQty)."</div>", 1=>"align='right'"),
		array(0=>"<div >".number_format($sum_tStockAmount,2)."</div>", 1=>"align='right'"),
		);
		
$ShowtotalRemark="合计($chooseMonth)";
$isTotal=1;
include "../model/subprogram/read_model_total.php";			
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult)-$ls;
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>

<script language="JavaScript" type="text/JavaScript">
<!--
function checkChange(obj){
	var e=document.getElementById("checkAccept");
    if (e.checked){
	  //document.getElementById("AcceptText").value="";
	  document.location.replace("../Admin/stuffdata_read.php");
	}
}
</script>