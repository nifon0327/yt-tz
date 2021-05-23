<?php   
include "../model/modelhead.php";
$ColsNumber=13;
$tableMenuS=600;
$From=$From==""?"m3":$From;
ChangeWtitle("$SubCompany 采购类配件需求单增加待审核列表");
$funFrom="yw_order";
$LevelSign=$LevelSign==""?1:$LevelSign;
if($LevelSign==1){
	$Th_Col="序号|40|订单流水号|100|产品名称|320|选项|60|序号|30|采购|45|供应商|80|配件ID|50|配件名称|350|历史订单|50|单价|50|单位|45|对应关系|60|状态|40|更新日期|70|更新人|60";	
}else{
	$Th_Col="序号|40|采购流水号|100|半成品名称|320|选项|60|序号|30|采购|45|供应商|80|配件ID|50|配件名称|350|历史订单|50|单价|50|单位|45|对应关系|60|状态|40|更新日期|70|更新人|60";
}

//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="15,17";
//步骤3：
$nowWebPage=$funFrom."_m3";
include "../model/subprogram/read_model_3.php";
if($From!="slist"){	//非查询：过滤采购、结付方式、供应商、月份
     
     $LevelSignStr="LevelSign".$LevelSign;
     $$LevelSignStr="selected";
	 echo "<select name='LevelSign' id='LevelSign' onchange='document.form1.submit();'>";
	 echo "<option value='1' $LevelSign1>产品</option>";
	 echo "<option value='2' $LevelSign2>半成品</option>";
	 echo "</select>";
	 
	 if($LevelSign==1){
		 
		 $SearchRows.=" AND S.Level=1";
	 }else if ($LevelSign==2){
	 
		 $SearchRows.=" AND S.Level>1";
	 } 
}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
//步骤4：
include "../model/subprogram/read_model_5.php";
//步骤5：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);

if($LevelSign ==1){
	$mySql="SELECT S.Level,S.POrderId,S.mStockId
			FROM $DataIn.cg1_addstuff S
			WHERE 1 $SearchRows  and S.Estate=1 GROUP BY S.POrderId";
}else{	
	$mySql="SELECT S.Level,S.POrderId,S.mStockId
			FROM $DataIn.cg1_addstuff S
			WHERE 1 $SearchRows  and S.Estate=1 GROUP BY S.mStockId";
}

//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
$DefaultBgColor=$theDefaultColor;
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$theDefaultColor=$DefaultBgColor;
		$Level=$myRow["Level"];
		$POrderId=$myRow["POrderId"];
		$mStockId=$myRow["mStockId"];

		if($Level==1 && $LevelSign==1){
			$Mid= $POrderId;
			$CheckNameRow  = mysql_fetch_array(mysql_query("SELECT P.cName FROM $DataIn.yw1_ordersheet Y 
			LEFT JOIN $DataIn.productdata P ON Y.ProductId=P.ProductId
			WHERE POrderId='$POrderId'",$link_id));
			$ChineseName = $CheckNameRow["cName"];
			
			$Stuff_Temp=mysql_query("select count(*) from $DataIn.cg1_addstuff where POrderId='$POrderId' AND Level = '$Level' AND Estate=1 ",$link_id);
			$Stuff_myrow = mysql_fetch_array($Stuff_Temp);
			$numrows=$Stuff_myrow[0]+1;
			
		}else{
			$Mid= $mStockId;
			$CheckNameRow  = mysql_fetch_array(mysql_query("SELECT D.StuffCname,SC.ActionId FROM $DataIn.cg1_stocksheet G  
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId = G.StuffId 
			LEFT JOIN $DataIn.yw1_scsheet SC ON SC.mStockId = G.StockId 
			WHERE G.StockId='$mStockId' GROUP BY G.StockId",$link_id));
			$ChineseName = $CheckNameRow["StuffCname"];
			$OrderAction= $CheckNameRow["ActionId"];
			$Stuff_Temp=mysql_query("select count(*) from $DataIn.cg1_addstuff where mStockId='$mStockId' AND Level = '$Level' AND Estate=1 ",$link_id);
			$Stuff_myrow = mysql_fetch_array($Stuff_Temp);
			$numrows=$Stuff_myrow[0]+1;
		}
		
		echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr onClick='ClickUpCheck($i)' id ='ListTable$i'>";
		echo"<td rowspan='$numrows' scope='col' height='25' width='$Field[$m]' class='A0111' align='center'>$j</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col' align='center'>$Mid</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col'>$ChineseName</td>";
		$m=$m+2;
				

		if($numrows>0){
		     if($Level==1 && $LevelSign==1){
		          $StuffSql =  "SELECT 
					S.Id,S.StuffId,S.Relation,S.Estate,S.Locks,S.Date,S.Operator,B.CompanyId,B.BuyerId,
					A.StuffCname,A.Price,U.Name AS UnitName,M.Name,P.Forshort,A.Gfile,A.Picture,A.Gremark
					FROM $DataIn.cg1_addstuff S
					LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
					LEFT JOIN $DataIn.bps B ON B.StuffId = S.StuffId
					LEFT JOIN $DataIn.stufftype T ON T.TypeId=A.TypeId
					LEFT JOIN $DataIn.stuffunit U ON U.Id=A.Unit
					LEFT JOIN $DataIn.staffmain M ON M.Number=B.BuyerId
					LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId
					WHERE 1 AND S.POrderId='$POrderId' AND S.Level = '$Level'  and S.Estate=1 ORDER BY S.StuffId DESC";
		      }else{
			      
			      $StuffSql =  "SELECT 
					S.Id,S.StuffId,S.Relation,S.Estate,S.Locks,S.Date,S.Operator,B.CompanyId,B.BuyerId,
					A.StuffCname,A.Price,U.Name AS UnitName,M.Name,P.Forshort,A.Gfile,A.Picture,A.Gremark
					FROM $DataIn.cg1_addstuff S
					LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
					LEFT JOIN $DataIn.bps B ON B.StuffId = S.StuffId
					LEFT JOIN $DataIn.stufftype T ON T.TypeId=A.TypeId
					LEFT JOIN $DataIn.stuffunit U ON U.Id=A.Unit
					LEFT JOIN $DataIn.staffmain M ON M.Number=B.BuyerId
					LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId
					WHERE 1 AND S.mStockId='$mStockId' AND S.Level = '$Level'  and S.Estate=1 ORDER BY S.StuffId DESC";
		      }
		
		    $StuffResult = mysql_query($StuffSql,$link_id);
			if($StuffRow=mysql_fetch_array($StuffResult)) {
			    $k=1;
				do{	
		            $n=$m;
		            $Id=$StuffRow["Id"];
			        $StuffId=$StuffRow["StuffId"];
					$StuffCname=$StuffRow["StuffCname"];
					$Picture=$StuffRow["Picture"];
					$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);		
					//检查是否有图片
					include "../model/subprogram/stuffimg_model.php";
					include"../model/subprogram/stuff_Property.php";//配件属性   
			        $OrderQtyInfo="<a href='../public/cg_historyorder.php?StuffId=$StuffId' target='_blank'>查看</a>"; 
			                
					$Price=$StuffRow["Price"];
					$UnitName=$StuffRow["UnitName"]==""?"&nbsp;":$StuffRow["UnitName"];
					$Relation=$StuffRow["Relation"];
					$Name=$StuffRow["Name"]==""?"&nbsp;":$StuffRow["Name"];
					$Forshort=$StuffRow["Forshort"]==""?"&nbsp;":$StuffRow["Forshort"];
					$Locks=$StuffRow["Locks"];
					$Estate=$StuffRow["Estate"];
					$Date=$StuffRow["Date"];
					$Operator=$StuffRow["Operator"];
					include "../model/subprogram/staffname.php";   
					$Estate=$Estate==0?"<div class='greenB'>√</div>":"<div class='yellowB'>√.</div>";
					
					$colspan = 13;
					if($Level==1 && $LevelSign==1){
						    
							$showPurchaseorder="<img onClick='ShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$POrderId\",$i,\"\");' name='showtable$i' src='../images/showtable.gif' 
							title='显示或隐藏配件采购明细资料.' width='13' height='13' style='CURSOR: pointer'>";
						
							$StuffListTB="<tr id='StuffList$i' style='display:none;background:#83b6b9;'><td colspan='$colspan'><div id='showStuffTB$i' width='720'></div></td></tr>"; 
					    }else{
						     $ShowId=$mStockId.$i;
					         $ShowBomImageId= "Bom_StuffImage_" . $ShowId;
					         $ShowBomTableId= "Bom_StuffTable_" . $ShowId;
					         $ShowBomDivId  = "Bom_StuffDiv_"  . $ShowId;
							 switch($OrderAction){
					           case 104:
					               $ajaxFile="slicebom_ajax";
						           $ajaxDir="pt";
					              break;
					              
					           case 102:
					              $ajaxFile="pt_order_ajax";
					              $ajaxDir="pt";
					              break;
					              
					           default:
					              $ajaxFile="semifinishedbom_ajax";
					              $ajaxDir="admin"; 
					              break;  
					        }
					$showPurchaseorder = "<img onClick='ShowDropTable($ShowBomTableId,$ShowBomImageId,$ShowBomDivId,\"$ajaxFile\",\"$mStockId|$ShowId|1\",\"$ajaxDir\");'  src='../images/showtable.gif' 
					title='显示或隐藏' width='13' height='13' style='CURSOR: pointer' bgcolor='$theDefaultColor' name='$ShowBomImageId'>";
				   $StuffListTB="<tr id='$ShowBomTableId' style='display:none;background:#83b6b9;'><td colspan='$colspan'><div id='$ShowBomDivId' width='720'></div></td></tr>"; 
					}
					

		            if($k>1){echo"<tr  bgcolor='$theDefaultColor' id ='ListTable$i' onClick='ClickUpCheck($i)'>";}
                    echo"<td  class='A0101' align='center' width='$Field[$n]' ><input name='checkid[]' type='checkbox' id='checkid$i' value='$Id'>&nbsp;&nbsp;$showPurchaseorder</td>";
                    $n=$n+2;
                    echo "<td class='A0101' width='$Field[$n]' align='center' >$k</td>";
					$n=$n+2;
					echo"<td class='A0101' width='$Field[$n]' align='center' >$Name</td>";
					$n=$n+2;
					echo"<td class='A0101' width='$Field[$n]' align='center' >$Forshort</td>";
					$n=$n+2;
					echo"<td class='A0101' width='$Field[$n]' align='center' >$StuffId</td>";
					$n=$n+2;
					echo"<td class='A0101' width='$Field[$n]' >$StuffCname</td>";
					$n=$n+2;
					echo"<td class='A0101' width='$Field[$n]' align='center'>$OrderQtyInfo</td>";
					$n=$n+2;
					echo"<td class='A0101' width='$Field[$n]' align='center'>$Price</td>";
					$n=$n+2;
					echo"<td class='A0101' width='$Field[$n]' align='center'>$UnitName</td>";
					$n=$n+2;					
					echo"<td class='A0101' width='$Field[$n]' align='center'>$Relation</td>";
					$n=$n+2;
					echo"<td class='A0101' width='$Field[$n]' align='center'>$Estate</td>";
					$n=$n+2;
					echo"<td class='A0101' width='$Field[$n]' align='center'>$Date</td>";
					$n=$n+2;
					echo"<td class='A0101' width='' align='center'>$Operator</td>";
					echo"</tr>";
                    echo $StuffListTB;
					$k++;
					$i++;
					} while ($StuffRow = mysql_fetch_array($StuffResult));
					echo "</table>";
				}
		  }
		$j++;
		}while ($myRow = mysql_fetch_array($myResult));
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
<script>
function ClickUpCheck(index){
    var checkid="checkid"+index;
    var listTable="ListTable"+index;
    var e=document.getElementById(checkid);
    if (e.checked){
        e.checked=false;
        document.getElementById(listTable).style.background="";
    }else{
        e.checked=true;
        document.getElementById(listTable).style.background="#FFCC99";
    }
}
</script>
