<?php   
//电信-EWEN 
   	$i=1; 
	$OrderResult = mysql_query("SELECT S.Id,S.sPorderId,Y.Id AS YId,Y.ProductId,S.scFrom,S.Qty,S.scQty,S.Estate,S.Date,S.Operator,
	P.cName,P.TestStandard,A.Name AS ActionName,W.Name AS WorkShop,getCanStock(S.sPorderId,3) AS blSgin 
		FROM $DataIn.yw1_scsheet S
		INNER JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.PorderId  
		INNER JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
	    INNER JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
	    INNER JOIN $DataIn.workorderaction A ON A.ActionId=S.ActionId 
	    LEFT  JOIN $DataIn.workshopdata W ON W.Id=S.WorkShopId  
		WHERE S.POrderId='$POrderId' AND S.ActionId=" . $APP_CONFIG['PACKAGE_ACTIONID'] . " ORDER BY S.Id",$link_id);	
	if ($OrderRows = mysql_fetch_array($OrderResult)) {
	       $YId=$OrderRows["YId"];
	       $TableId="ListScOrderTB".$YId;
	      if($Keys & mUPDATE){
	        $SetButton="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"yw_order_scSet\",\"$YId\")' src='../images/edit.png' title='工单设置' width='15' height='15'>";
	      }else{
		     $SetButton=""; 
	      }
		
        echo"<table width='945' cellspacing='1' border='0' align='left' style='margin-left:60px;'><tr bgcolor='#CCCCCC'>
			<td colspan='2'  height='20' align='center'>$SetButton</td>
			<td width='80' align='center'>工单流水号</td>
			<td width='410' align='center'>产品名称</td>	
			<td width='60' align='center'>加工类型</td>
			<td width='100' align='center'>生产单位</td>				
			<td width='80' align='center'>工单数量</td>
			<td width='80' align='center'>已生产数</td>
			<td width='80' align='center'>备料状态</td>
			<td width='80' align='center'>日期</td>
			<td width='80' align='center'>操作员</td></tr>";
		do{
			$OnclickStr="";
			$sPorderId=$OrderRows["sPorderId"];
			$thisId=$OrderRows["Id"];
			$Qty=$OrderRows["Qty"];
			$scQty=$OrderRows["scQty"];
			$scQty=$scQty==$Qty?"<span class='greenB'>$scQty</span>":"<span class='yellowB'>$scQty</span>";
			$scFrom=$OrderRows["scFrom"];
			$blSgin=$OrderRows["blSgin"];
			$blSgin=$blSgin==3?"<div class='greenB'>已备料</div>":"未备料";
			$Estate=$OrderRows["Estate"];
			$blSgin=($scFrom==0 || $Estate==0)?"<div class='greenB'>已生产</div>":$blSgin;
            $ProductId=$OrderRows["ProductId"];
			$Date=$OrderRows["Date"];
			$cName=$OrderRows["cName"];
            $TestStandard=$OrderRows["TestStandard"];
		    include "../admin/Productimage/getPOrderImage.php";
		    
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
            
            $TableCellId=$TableId . '_' . $i;
            $OnclickStr="onclick='updatescLock(\"$TableCellId\",$sPorderId,$lockState)' style='CURSOR: pointer;'";


             $checkIdSql = "SELECT sPOrderId,splitQty,wsId,Estate FROM $DataIn.yw1_ordersplit WHERE POrderId ='$POrderId' AND Estate>0 ";
             $checkIdResult = mysql_query($checkIdSql,$link_id);
             $checkIdRow =mysql_fetch_array($checkIdResult);
         
             $_sPOrderId= $checkIdRow["sPOrderId"];
             $_sQty= $checkIdRow["splitQty"];
             $_wsId= $checkIdRow["wsId"];
             $_estate = $checkIdRow["Estate"];
             $splitQtyStr="";
             if($_sPOrderId!="" && $_sQty!=""){
                 $splitQtyStr ="工单拆分后的数量：$_sQty,请通知主管审核!";
		         $theDefaultColor = "#CDCD00";
             }  
			echo"<tr bgcolor='$theDefaultColor'>
            <td id='$TableCellId'  align='center' width='40' $OnclickStr>$lock</td>
			<td bgcolor='$Sbgcolor' align='center' width='15' >$i</td>";//订单状态 
			echo"<td  align='center'>$sPorderId</td>";
			echo"<td  >$cName</td>";
			echo"<td  align='center'>$ActionName</td>";
			echo"<td  align='center'>$WorkShop</td>";
			echo"<td  align='right' title='$splitQtyStr' >$Qty</td>";
			echo"<td  align='right'>$scQty</td>";
			echo"<td  align='center'>$blSgin</td>";
			echo"<td  align='center'>$Date</td>";
				echo"<td  align='center'>$Operator</td>";
			echo"</tr>";
			$i++;
			}while ($OrderRows = mysql_fetch_array($OrderResult));
			 echo"</table><br>";
		}

?>