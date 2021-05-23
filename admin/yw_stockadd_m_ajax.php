<?php   
//电信-EWEN 
   	$i=1; 

   	if($mStockId>0){
	   	$SearchRowsA=" AND S.mStockId ='$mStockId' AND S.Level >1";
	   	
   	}else{
	   	$SearchRowsA=" AND S.POrderId ='$POrderId' AND S.Level =1";
   	}
	$m3Result = mysql_query("SELECT 
		S.Id,S.Level,S.POrderId,S.mStockId,S.StuffId,S.Relation,S.Estate,S.Locks,S.Date,S.Operator,B.CompanyId,B.BuyerId,
		A.StuffCname,A.Price,U.Name AS UnitName,M.Name,P.Forshort,A.Gfile,A.Picture,A.Gremark
		FROM $DataIn.cg1_addstuff S
		LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
		LEFT JOIN $DataIn.bps B ON B.StuffId = S.StuffId
		LEFT JOIN $DataIn.stufftype T ON T.TypeId=A.TypeId
		LEFT JOIN $DataIn.stuffunit U ON U.Id=A.Unit
		LEFT JOIN $DataIn.staffmain M ON M.Number=B.BuyerId
		LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId  
		WHERE 1  $SearchRowsA  and S.Estate=1 ORDER BY S.Id",$link_id);	
	if ($m3Rows = mysql_fetch_array($m3Result)) {
        echo"<table width='945' cellspacing='1' border='1' align='left' style='margin:20px 0px 20px 60px;'><tr bgcolor='#CCCCCC'>
			<td height='25' width='30' align='center'>序号</td>
			<td width='70' align='center'>配件ID</td>
			<td width='320' align='center'>异动配件名称</td>	
			<td width='90' align='center'>采购</td>
			<td width='100' align='center'>供应商</td>	
						
			<td width='50' align='center'>单价</td>
			<td width='50' align='center'>单位</td>
			<td width='60' align='center'>对应关系</td>
			<td width='80' align='center'>状态</td>
			<td width='80' align='center'>更新日期</td>
			<td width='80' align='center'>操作员</td></tr>";
		do{
			$StuffId=$m3Rows["StuffId"];
			$StuffCname=$m3Rows["StuffCname"];
			$Picture=$m3Rows["Picture"];
			$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);		
			//检查是否有图片
			include "../model/subprogram/stuffimg_model.php";
			include"../model/subprogram/stuff_Property.php";//配件属性   
			$Price=$m3Rows["Price"];
			$UnitName=$m3Rows["UnitName"]==""?"&nbsp;":$m3Rows["UnitName"];
			$Relation=$m3Rows["Relation"];
			$Name=$m3Rows["Name"];
			$Forshort=$m3Rows["Forshort"];
			$Locks=$m3Rows["Locks"];
			$Estate=$m3Rows["Estate"];
			$Date=$m3Rows["Date"];
			$Operator=$m3Rows["Operator"];
			include "../model/subprogram/staffname.php";   
			$Estate="<div class='yellowB'>请主管审核</div>";
		    
		   
			echo"<tr bgcolor='#CDCD00'><td  align='center'  >$i</td>";
			echo"<td  align='center'>$StuffId</td>";
			echo"<td  >$StuffCname</td>";
			echo"<td  align='center'>$Name</td>";
			echo"<td  align='center'>$Forshort</td>";
			echo"<td  align='center'>$Price</td>";
			echo"<td  align='center'>$UnitName</td>";
			echo"<td  align='right'>$Relation</td>";
			echo"<td  align='center'>$Estate</td>";
			echo"<td  align='center'>$Date</td>";
			echo"<td  align='center'>$Operator</td>";
			echo"</tr>";
			$i++;
			}while ($m3Rows = mysql_fetch_array($m3Result));
			 echo"</table>";
		}

?>