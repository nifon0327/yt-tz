<?php

 $ProcessSql="SELECT D.ProcessId,D.ProcessName,A.Relation,D.Price,D.Picture,D.BassLoss
 FROM $DataIn.cg1_processsheet A  
 LEFT JOIN $DataIn.process_data D ON D.ProcessId=A.ProcessId   
 LEFT JOIN $DataIn.process_type PT ON PT.gxTypeId=D.gxTypeId
 WHERE A.StockId='$mStockId'  ORDER BY PT.SortId";
//echo $ProcessSql;
$ProcessResult=mysql_query($ProcessSql,$link_id);
$i=1;
$MaxQty =  $OrderQty;
if ($ProcessRow = mysql_fetch_array($ProcessResult)) {
	echo"<table  cellspacing='1' border='1' align='center' style='table-layout:fixed;word-break:break-all; word-wrap:break-word;margin-left:30px;margin-top:20px;margin-bottom:20px;' ><tr bgcolor='#CCCCCC'>
			         <td width='40'  align='center'>序号</td>
	                <td width='60'  align='center'>工序Id</td>
			        <td width='280' align='center'>工序名称</td>
	                <td width='40'  align='center'>图档</td>
			        <td width='70' align='center'>对应关系</td>
	                <td width='70' align='center'>损耗比率</td>
	                <td width='70' align='center'>允许损耗数</td>
	                <td width='70' align='center'>最低登记数</td>
	                <td width='70' align='center'>需求数量</td>
	                <td width='70' align='center'>完成数量</td></tr>"; 
	$d=anmaIn("download/process/",$SinkOrder,$motherSTR);
	$LowQty=0;
	do{
		$ProcessId=$ProcessRow["ProcessId"];
		$ProcessName=$ProcessRow["ProcessName"];
		$Relation=$ProcessRow["Relation"];
        $Price=$ProcessRow["Price"]==""?"&nbsp;":$ProcessRow["Price"];
        $Picture=$ProcessRow["Picture"];
        $ProcessQty=ceil($OrderQty*$Relation);
        include "subprogram/process_Gfile.php";	//图档显示
  
		echo"<tr bgcolor=#EAEAEA>";
		echo"<td align='center'>$i</td>";	      //序号
		echo"<td align='center'>$ProcessId</td>"; //工序ID
        echo"<td>$ProcessName</td>";	          //工序名称
		echo"<td align='center'>$Gfile</td>";	  //工序图档
		echo"<td align='center'>$Relation</td>";  //对应关系
        $POrderId=substr($StockId,0,12);
        $CheckthisGxQty=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(C.Qty),0) AS gxQty FROM $DataIn.sc1_gxtj C WHERE C.POrderId='$POrderId' AND C.StockId='$StockId' AND C.ProcessId='$ProcessId'",$link_id));
        $thisGxQty=$CheckthisGxQty["gxQty"]==""?0:$CheckthisGxQty["gxQty"];
        $BassLoss=$ProcessRow["BassLoss"];
        $BassQty=floor($BassLoss*$MaxQty);
        $BassLoss=($BassLoss*100)."%";
        if($LowQty==0){
            $LowQty=$MaxQty-$BassQty;
        }
        else{
            $LowQty=$LowQty-$BassQty;
        }
        if($thisGxQty>=$LowQty)$thisGxQty="<span class='greenB'>$thisGxQty</span>";
        else{
            $thisGxQty="<span class='yellowB'>$thisGxQty</span>";
       }
        echo"<td align='center'>$BassLoss</td>";	 //损耗比率
        echo"<td align='center'>$BassQty</td>";	 //损耗量
        echo"<td align='center'>$LowQty</td>";	 //最低登记量
        echo"<td align='center'>$ProcessQty</td>";	 //需求数量
        echo"<td align='center'>$thisGxQty</td>";	 //完成数量
		echo"</tr>";
		$i++;
 	   }while ($ProcessRow = mysql_fetch_array($ProcessResult));
 	   echo"</table>";
	}
?>