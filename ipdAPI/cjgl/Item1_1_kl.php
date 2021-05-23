<?php 
//更新OK
$Th_Col="序号|30|PO|70|中文名|200|订单数量|60|NO.|30|配件名称|200|需备数/<br>已备数|55|单位|30|刀模|60|任务数量|55|完成数量|55|未完成量|55|登记|65";
$Field=explode("|",$Th_Col);
$Count=count($Field);
$wField=$Field;
$widthArray=array();
for ($i=0;$i<$Count;$i++){
	$i=$i+1;
	$widthArray[]=$wField[$i];
	$tableWidth+=$wField[$i];
	}
$SearchRows=" AND Y.scFrom IN (0,2) AND Y.Estate>0";
$ClientList="";
$ClientResult= mysql_query("SELECT M.CompanyId,C.Forshort 
FROM $DataIn.yw1_ordermain M 
LEFT JOIN $DataIn.yw1_ordersheet Y ON M.OrderNumber=Y.OrderNumber 
LEFT JOIN $DataIn.trade_object C ON M.CompanyId=C.CompanyId 
LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId 
WHERE 1 $SearchRows 
GROUP BY M.CompanyId order by M.CompanyId",$link_id);
if ($ClientRow = mysql_fetch_array($ClientResult)){
	$ClientList="<select name='CompanyId' id='CompanyId' style='width:150px' onChange='ResetPage(1,1)'>";
	$i=1;
	do{
		$theCompanyId=$ClientRow["CompanyId"];
		$theForshort=$ClientRow["Forshort"];
		$CompanyId=$CompanyId==""?$theCompanyId:$CompanyId;
		if($CompanyId==$theCompanyId){
			$ClientList.="<option value='$theCompanyId' selected>$i 、$theForshort</option>";
			$SearchRows.=" AND M.CompanyId='$theCompanyId'";
			$nowInfo="当前:".$ItemRemark." - ".$theForshort;
			}
		else{
			$ClientList.="<option value='$theCompanyId'>$i 、$theForshort</option>";
			}
		$i++;
		}while($ClientRow = mysql_fetch_array($ClientResult));
		$ClientList.="</select>";
	}
//分类
$TypeResult= mysql_query("SELECT P.TypeId,T.TypeName
FROM $DataIn.yw1_ordermain M 
LEFT JOIN $DataIn.yw1_ordersheet Y ON M.OrderNumber=Y.OrderNumber 
LEFT JOIN $DataIn.trade_object C ON M.CompanyId=C.CompanyId 
LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId 
LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
WHERE 1 $SearchRows AND P.ProductId IN (SELECT ProductId FROM $DataIn.cut_bom GROUP BY ProductId)
GROUP BY P.TypeId ORDER BY T.SortId",$link_id);
	if ($TypeRow = mysql_fetch_array($TypeResult)){
		$TypeList="<select name='ProductTypeId' id='ProductTypeId' onchange='ResetPage(1,1)'>";
		do{
			$theTypeId=$TypeRow["TypeId"];
			$TypeName=$TypeRow["TypeName"];
			$ProductTypeId=$ProductTypeId==""?$theTypeId:$ProductTypeId;
			if($ProductTypeId==$theTypeId){
				$TypeList.="<option value='$theTypeId' selected>$TypeName</option>";
				$SearchRows.=" AND P.TypeId='$theTypeId'";
				}
			else{
				$TypeList.="<option value='$theTypeId'>$TypeName</option>";
				}
			}while($TypeRow = mysql_fetch_array($TypeResult));
		$TypeList.="</select>&nbsp;";
		}
  

//步骤5：
echo"<table width='$tableWidth' border='0' cellspacing='0'  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr bgcolor='#D9D9D9'>
	<td colspan='8' height='40px' class='A1010'>$ClientList $TypeList </td><td colspan='5' align='right' class='A1001'><input name='NowInfo' type='text' id='NowInfo' value='$nowInfo' class='text' disabled></td></tr></table>";
echo "<table width='$tableWidth' border='0' cellspacing='0'  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr>";
	//输出表格标题
	for($i=0;$i<$Count;$i=$i+2){
		$Class_Temp=$i==0?"A1111":"A1101";
		$j=$i;
		$k=$j+1;
		echo"<td width='$Field[$k]' Class='$Class_Temp' height='25px'><div align='center'>$Field[$j]</div></td>";
		}
	echo"</tr></table>";
$DefaultBgColor=$theDefaultColor;
$mySql= "SELECT Y.Id,Y.OrderPO,Y.POrderId,Y.Qty,P.ProductId,P.cName,P.eCode,P.TestStandard  
FROM $DataIn.yw1_ordersheet Y 
LEFT JOIN $DataIn.productdata P ON Y.ProductId=P.ProductId
LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=Y.OrderNumber  
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId  
WHERE 1 $SearchRows  AND P.ProductId IN (SELECT ProductId FROM $DataIn.cut_bom Group by ProductId)
GROUP BY Y.POrderId ORDER BY M.OrderDate DESC";
//echo $mySql;
$cutedColor="style='color:#093';";
$nocutColor="style='color:#F00';";
$myResult = mysql_query($mySql,$link_id);
if($myRow = mysql_fetch_array($myResult)){
$i=1;$j=1;$s=1;
$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);	
	do{
		$m=1;
		$ProductId=$myRow["ProductId"];
        $POrderId=$myRow["POrderId"];
        $OrderPO=$myRow["OrderPO"];
        $Qty=$myRow["Qty"];
		$cName=$myRow["cName"];
		$eCode=$myRow["eCode"]==""?"&nbsp;":$myRow["eCode"];
		$TestStandard=$myRow["TestStandard"];
		include "../admin/Productimage/getPOrderImage.php";
		include "../admin/subprogram/getCuttingImage.php";
		
		//读取配件数
		$PO_Temp=mysql_query("select count(*) from $DataIn.cut_bom where ProductId=$ProductId  AND Diecut!='' AND Cutrelation>0 ",$link_id);
		$PO_myrow = mysql_fetch_array($PO_Temp);
		$numrows=$PO_myrow[0];
		//$Qty=$Qty/$numrows;  //订单数量
		$rowHeight=25;
        if ($numrows==1) $rowHeight=25;
		echo"<table id='ListTable$j' width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
		echo"<td rowspan='$numrows' scope='col' height='$rowHeight' width='$Field[$m]' class='A0111' align='center'>$j</td>";
		$m=$m+2;
        echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col' align='center'>$OrderPO</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col' align='center'>$TestStandard</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col' align='center'>$Qty</td>";
		$m=$m+2;
		if($numrows>0){
			//从配件表和配件关系表中提取配件数据	  
			$StuffResult = mysql_query("SELECT D.StuffCname,D.StuffId,D.Picture,U.Name AS Unit 
				         FROM $DataIn.cut_bom A
						 LEFT JOIN $DataIn.stuffdata D ON D.StuffId=A.StuffId 
                         LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit 
						 WHERE A.ProductId='$ProductId' Group by A.StuffId ORDER BY A.StuffId",$link_id);
			$k=1;
			if($StuffMyrow=mysql_fetch_array($StuffResult)) {//如果设定了产品配件关系
			  do{	
				$n=$m;
				$StuffId=$StuffMyrow["StuffId"];
				$Picture=$StuffMyrow["Picture"];
				$StuffCname=$StuffMyrow["StuffCname"];
                $Unit=$StuffMyrow["Unit"]==""?"&nbsp;":$StuffMyrow["Unit"];
				include "../model/subprogram/stuffimg_model.php";
				$Cut_Temp=mysql_query("select count(*) from $DataIn.cut_bom 
					           WHERE ProductId=$ProductId AND StuffId='$StuffId'",$link_id);
				$Cut_myrow = mysql_fetch_array($Cut_Temp);
	            $Cutnumrows=$Cut_myrow[0];
				$StockResult=mysql_query("SELECT G.OrderQty,G.StockId,A.Relation
				FROM $DataIn.cg1_stocksheet G 
				LEFT JOIN $DataIn.yw1_ordersheet Y  ON Y.POrderId=G.POrderId
				LEFT JOIN $DataIn.pands  A ON A.StuffId=G.StuffId AND A.ProductId=Y.ProductId
				WHERE G.POrderId='$POrderId' AND G.StuffId='$StuffId'",$link_id);
				//echo $POrderId.":".$StuffId;
				if($StockRow=mysql_fetch_array($StockResult)){
				 $StockId=$StockRow["StockId"];
				 $OrderQty=$StockRow["OrderQty"];
				 $Relation=$StockRow["Relation"];
				 //echo $Relation."<br>";
				 $RelationArray=explode("/",$Relation);
				 if($RelationArray[1]==0)$EachQty=$RelationArray[0];
				 else $EachQty=$RelationArray[1];
				}
				//备料数量
				$llSql=mysql_query("SELECT SUM(L.Qty) AS llQty FROM $DataIn.ck5_llsheet L WHERE StockId='$StockId'",$link_id);
				$llQty=mysql_result($llSql,0,"llQty");
				$llQty=$llQty==""?0:$llQty;
				$TempllQty=$llQty;
				$llQty=$OrderQty."/".$llQty;
				//配件名称
				echo"<td class='A0101' width='$Field[$n]' rowspan='$Cutnumrows' align='center'>$k</td>";
				$n=$n+2;
				echo"<td class='A0101' width='$Field[$n]' rowspan='$Cutnumrows'>$StuffCname</td>";
				$n=$n+2;
				echo"<td class='A0101' width='$Field[$n]' align='center' rowspan='$Cutnumrows'>$llQty</td>";
				$n=$n+2;
                echo"<td class='A0101' width='$Field[$n]' align='center' rowspan='$Cutnumrows'>$Unit</td>";
				$n=$n+2;
				$cutResult=mysql_query("SELECT A.Id,A.Diecut,A.Cutrelation,C.CutName,C.Picture
					          FROM $DataIn.cut_bom A  
							  LEFT JOIN $DataIn.cut_data C ON C.Id=A.Diecut
							  WHERE A.ProductId='$ProductId' AND A.StuffId='$StuffId'",$link_id);
					  $r=1;
					  if($cutRow=mysql_fetch_array($cutResult)){//切割关系
					      $dc=anmaIn("download/cut_data/",$SinkOrder,$motherSTR);
					     do{ 
						    $h=$n;
                            $cutId=$cutRow["Id"];
							$CutName=$cutRow["CutName"];					
					        $Diecut=$cutRow["Diecut"];
							$Cutrelation=$cutRow["Cutrelation"];
							$Picture=$cutRow["Picture"];
		                    $fn=anmaIn("C".$Diecut.".jpg",$SinkOrder,$motherSTR);
		                    if($Picture==1){
		                     $CutName="<a href=\"../admin/openorload.php?d=$dc&f=$fn&Type=&Action=6\"target=\"download\">$CutName</a>";}
                            //读取完成数量 $cutedQty
                            include "../admin/subprogram/getPorderIdcutQty.php";
							//能登记的数量=领料数量*每单位能切割出的数量*对应关系-已经登记的数量
                            $cutQty=$Qty*$Cutrelation;
                            $nocutQty=$cutQty-$cutedQty;  
							$DengQty=$EachQty*$TempllQty*$Cutrelation-$cutedQty;    
					        $cutedQty=$cutedQty==0?"&nbsp;":$cutedQty;
					        $nocutQty=$nocutQty==0?"&nbsp;":$nocutQty;
							//只显示未完成切割任务	
						    if($r>1){echo"<tr>";}
					        echo"<td class='A0101' width='$Field[$h]' align='center'>$CutName</td>";
					        $h=$h+2;
					        echo"<td class='A0101' width='$Field[$h]' align='center'>$cutQty</td>";
					        $h=$h+2;
					        echo"<td class='A0101' width='$Field[$h]' id='cutedQty$s' align='center' $cutedColor>$cutedQty</td>";
					        $h=$h+2;
					        echo"<td class='A0101' width='$Field[$h]' id='nocutQty$s' align='center' $nocutColor>$nocutQty</td>";                     
					        $h=$h+2;
					    if ($nocutQty>0){
						 echo"<td class='A0101' width='$Field[$h]'  align='center'><input name='addQty[]' type='text' id='addQty$j$k$r' size='4' value='' onchange='addQtyFun(this,$j,$s,$POrderId,$cutId,$Cutrelation,$DengQty)'> </td>";
					         }
						 else{
						 echo "<td class='A0101' width='$Field[$h]' bgcolor='#96FF2D' align='center'>&nbsp;</td>";
					         }
					    $r++;
						$s++;
						echo"</tr>";
					  }while($cutRow=mysql_fetch_array($cutResult));
				    }
				   $k++;$i++;
				} while ($StuffMyrow = mysql_fetch_array($StuffResult));
			   if ($k>1) {echo "</table>";$j++;}
			 }//if($StuffMyrow=mysql_fetch_array($StuffResult)) {//如果设定了产品配件关系
			}//结束存在配件表
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	echo"<table width='$tableWidth' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr><td colspan='10' align='center' height='30' class='A0111'><div class='redB' style='background-color: #ffffff'>没有记录!</div></td></tr></table>";
	}
?>