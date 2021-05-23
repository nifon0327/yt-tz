<?php   
//电信-EWEN 
   	$i=1; 
    $margin_left=$margin_left==""?"60px":$margin_left;
	$OrderResult = mysql_query("SELECT S.Id,S.sPorderId,S.StockId,A.StuffId,S.ActionId,S.scFrom,S.Qty,S.scQty,
	 S.Estate,S.Date,S.Operator,D.StuffCname,D.Picture,O.Name AS ActionName,W.Name AS WorkShop     
		FROM $DataIn.cg1_semifinished A
		INNER JOIN $DataIn.yw1_scsheet S  ON A.StockId=S.StockId  
		INNER JOIN $DataIn.stuffdata D ON D.StuffId=A.mStuffId 
		INNER JOIN $DataIn.workorderaction O ON O.ActionId=S.ActionId 
	    LEFT  JOIN $DataIn.workshopdata W ON W.Id=S.WorkShopId 
		WHERE A.mStockId='$mStockId' ORDER BY StockId,Id",$link_id);	

	if ($OrderRows = mysql_fetch_array($OrderResult)) {
	       $TableId="ListOrderTB".$mStockId;
	       $StockId=$OrderRows["StockId"];
	       
	      if($Keys & mUPDATE){
	        $SetButton="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"../admin/semifinished_scsheet_update\",\"$StockId\")' src='../images/edit.png' title='工单设置' width='15' height='15'>";
	      }else{
		     $SetButton=""; 
	      }
	      
	     
              
		
        echo"<table id='$TableId' cellspacing='1' border='1' align='left' style='margin-left:$margin_left;'><tr bgcolor='#CCCCCC'>
			<td colspan='2'  height='20' align='center'>$SetButton</td>
			<td width='90' align='center'>工单流水号</td>
			<td width='410' align='center'>半成品名称</td>
			<td width='60' align='center'>加工类型</td>
			<td width='100' align='center'>生产单位</td>					
			<td width='80' align='center'>工单数量</td>
			<td width='80' align='center'>生产数量</td>
			<td width='80' align='center'>备料状态</td>
			<td width='80' align='center'>日期</td>
			<td width='80' align='center'>操作员</td></tr>";
		do{
			$OnclickStr="";
			$sPorderId=$OrderRows["sPorderId"];
			$thisId=$OrderRows["Id"];
			$Qty=$OrderRows["Qty"];
			$scFrom=$OrderRows["scFrom"];
			$scFrom=$scFrom==1?"未备料":"<div class='greenB'>已备料</div>";
			$Estate=$OrderRows["Estate"];
            $StuffId=$OrderRows["StuffId"];
			$Date=$OrderRows["Date"];
			$StuffCname=$OrderRows["StuffCname"];
            $Picture=$OrderRows["Picture"];
            //检查是否有图片
	       include "../model/subprogram/stuffimg_model.php";
           include"../model/subprogram/stuff_Property.php";//配件属性  
		    
		    $Operator=$OrderRows["Operator"];
		    include "../model/subprogram/staffname.php";
		
		    $ActionName=$OrderRows["ActionName"];
		    $WorkShop=$OrderRows["WorkShop"];
		    
		       //检查是否锁定 
			$lockcolor=''; $lockState=1;
			$lock="<div title='工单未锁定' > <img src='../images/unlock.png' width='15' height='15'> </div>";
			$CheckSignSql=mysql_query("SELECT Id,Remark FROM $DataIn.yw1_sclock WHERE sPOrderId ='$sPorderId' AND Locks=0 LIMIT 1",$link_id);
			if($CheckSignRow=mysql_fetch_array($CheckSignSql)){
			   $lockRemark=$CheckSignRow["Remark"];
				$lock="<div style='background-color:#FF0000' title='原因:$lockRemark'> <img src='../images/lock.png' width='15' height='15'></div>";
				$lockState=0;
			  }
              $OnclickStr="onclick='updatescLock(\"$TableId\",$i,$sPorderId,$lockState)' style='CURSOR: pointer;'";
              
			echo"<tr bgcolor='$theDefaultColor'>
            <td  align='center' width='40' $OnclickStr>$lock</td>
			<td bgcolor='$Sbgcolor' align='center' width='15'>$i</td>";//订单状态 
			echo"<td  align='center'>$sPorderId</td>";
			echo"<td  >$StuffCname</td>";
			echo"<td  align='center'>$ActionName</td>";
			echo"<td  align='center'>$WorkShop</td>";
			echo"<td  align='right'>$Qty</td>";
			echo"<td  align='right'>$scQty</td>";
			echo"<td  align='center'>$scFrom</td>";
			echo"<td  align='center'>$Date</td>";
			echo"<td  align='center'>$Operator</td>";
			echo"</tr>";
            //echo $showTable;
			$i++;
			}while ($OrderRows = mysql_fetch_array($OrderResult));
			 echo"</table>";
		}

?>