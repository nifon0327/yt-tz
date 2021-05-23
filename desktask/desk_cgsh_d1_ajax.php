<?php   
/*电信-yang 20120801
已更新
*/
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//参数拆分
//参数拆分
$TempArray=explode("|",$TempId);
$CompanyId=$TempArray[0];
$predivNum=$TempArray[1];
$theDay=$TempArray[2];

$tableWidth=970;
$TableId=$predivNum;
echo"<table width='$tableWidth' cellspacing='1' border='1' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr bgcolor='#99FF99'>
		<td width='40' align='center'>批次</td>
		<td width='80' align='center'>送货单</td>
		<td width='50' align='center'>采购</td>
		<td width='40' align='center'>序号</td>
		<td width='50' align='center'>配件ID</td>
		<td width='333' align='center'>配件名称</td>
		<td width='40' align='center'>图档</td>
		<td width='90' align='center'>需求单号</td>
		<td width='55' align='center'>单价</td>
		<td width='45' align='center'>单位</td>
		<td width='50' align='center'>采购<br>数量</td>
		<td width='50' align='center'>本次<br>收货</td>
		<td width='72' align='center'>采购金额</td>
		</tr>";
//订单列表
$mySql="SELECT M.BillNumber,M.Date,M.Remark,
		S.Mid,S.StockId,S.StuffId,S.Qty,G.Price,U.Name AS UnitName,
		D.StuffCname,D.Picture,D.Gfile,D.Gstate,
		A.Name,
		G.FactualQty+G.AddQty AS cgQty
FROM $DataIn.ck1_rksheet S
LEFT JOIN $DataIn.ck1_rkmain M ON S.Mid=M.Id 
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit
LEFT JOIN $DataPublic.staffmain A ON A.Number=M.BuyerId 
LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId
WHERE 1 AND M.CompanyId='$CompanyId' AND DATE_FORMAT(M.Date,'%Y-%m-%d')='$theDay' ORDER BY M.Date DESC,M.Id DESC";
$mainResult = mysql_query($mySql." $PageSTR",$link_id);
if($mainRows = mysql_fetch_array($mainResult)){
	$tbDefalut=0;
	$midDefault="";
	$m=1;
	$i=1;
	$j=1;
	do{
		$Mid=$mainRows["Mid"];
		$Date=$mainRows["Date"];
		$BillNumber=$mainRows["BillNumber"];
		$Remark=$mainRows["Remark"]==""?"":"title='$Remark'";
		$FilePath1="../download/deliverybill/$BillNumber.jpg";
		if(file_exists($FilePath1)){
			$BillNumber="<a href='$FilePath1' target='_blank'>$BillNumber</a>";
			}
		$Name=$mainRows["Name"];
		//明细资料
		$StuffId=$mainRows["StuffId"];		
		if($StuffId!=""){
			$StuffCname=$mainRows["StuffCname"];
			$Qty=$mainRows["Qty"];				//入库数量
			$cgQty=$mainRows["cgQty"];			//采购数量
			$Price=$mainRows["Price"];
			$UnitName=$mainRows["UnitName"]==""?"&nbsp;":$mainRows["UnitName"];
			$StockId=$mainRows["StockId"];
			$Picture=$mainRows["Picture"];
			$rkTemp=mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.ck1_rksheet WHERE StockId='$StockId' order by Id",$link_id);
			$rkQty=mysql_result($rkTemp,0,"Qty");
			$rkQty=$rkQty==""?0:$rkQty;			//入库总数
			$Amount=sprintf("%.2f",$rkQty*$Price);//入库金额
			$rkBgColor=$rkQty==$cgQty?"class='greenB'":"class='redB'";
			$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
			include "../model/subprogram/stuffimg_model.php";
			$Sid=anmaIn($StockId,$SinkOrder,$motherSTR);
			$Gfile=$mainRows["Gfile"];
			$Gstate=$mainRows["Gstate"];
			include "../model/subprogram/stuffimg_Gfile.php";	//图档显示	
			////////////////////////////////////////////////////
			if($tbDefalut==0 && $midDefault==""){//首行
				//并行列
				echo"<tr bgcolor='#BBFFBB'>";
				echo"<td scope='col' width='40' align='center'>$m</td>";						//日期
				echo"<td scope='col' width='80' align='center' $Remark>$BillNumber</td>";	//送货单			
				echo"<td scope='col' width='50' align='center'>$Name</td>";						//采购				
				echo"<td width='' colspan='10'>";	
				$midDefault=$Mid;
				}
			if($midDefault!="" && $midDefault==$Mid){//同属于一个主ID，则依然输出明细表格
				echo"<table cellspacing='1' border='1' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>";
				echo"<tr>";
				echo"<td width='40' align='center'>$j</td>";			//序号
				echo"<td width='50' align='center'>$StuffId</td>";	//配件ID
				echo"<td width='333'>$StuffCname</td>";	//配件
				echo"<td width='40' align='center'>$Gfile</td>";
				echo"<td width='90' align='center'><a href='ck_rk_list.php?Sid=$Sid' target='_blank'>$StockId</div></td>";//需求流水号
				echo"<td width='55' align='right'>$Price</td>";		//单价
				echo"<td width='45' align='center'>$UnitName</td>";
				echo"<td width='50' align='right'>$cgQty</td>";		//需求数量
				echo"<td width='50' align='right'><div $rkBgColor>$Qty</div></td>";		//入库数量
				echo"<td width='71' align='right'>$Amount&nbsp;</td>";	//金额
				echo"</tr></table>";
				$i++;
				$j++;
				}
			else{
				//新行开始
				echo"</td></tr>";//结束上一个表格
				$m++;
				//并行列
				echo"<tr bgcolor='#BBFFBB'>";
				echo"<td scope='col' width='40' align='center'>$m</td>";						//日期
				echo"<td scope='col' width='80' align='center' $Remark>$BillNumber</td>";	//送货单			
				echo"<td scope='col' width='50' align='center'>$Name</td>";						//采购				
				echo"<td width='' colspan='10'>";
				$midDefault=$Mid;
				echo"<table cellspacing='1' border='1' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>";
				echo"<tr>";
				echo"<td width='40' align='center'>$j</td>";			//序号
				echo"<td width='50' align='center'>$StuffId</td>";	//配件ID
				echo"<td width='333'>$StuffCname</td>";	//配件
				echo"<td width='40' align='center'>$Gfile</td>";
				echo"<td width='90' align='center'><a href='ck_rk_list.php?Sid=$Sid' target='_blank'>$StockId</div></td>";//需求流水号
				echo"<td width='55' align='right'>$Price</td>";		//单价
				echo"<td width='45' align='center'>$UnitName</td>";
				echo"<td width='50' align='right'>$cgQty</td>";		//需求数量
				echo"<td width='50' align='right'><div $rkBgColor>$Qty</div></td>";		//入库数量
				echo"<td width='71' align='right'>$Amount&nbsp;</td>";	//金额
				echo"</tr></table>";
				$i++;
				$j++;
				}
			}
		
		}while($mainRows = mysql_fetch_array($mainResult));
	echo"</table>";
	}
else{
	echo"<tr><td height='30' colspan='6'  bgcolor=#D0FFD0>没有资料,请检查.</td></tr></table>";
	}
?>
