
<?php   
/*电信---yang 20120801
$DataIn.trade_object
$DataPublic.currencydata
$DataIn.trade_object
$DataIn.producttype
$DataIn.pands
$DataIn.productdata
$DataIn.stuffdata
$DataIn.bps
$DataIn.taskuserdata
分开已更新
*/
//步骤1
include "../model/modelhead.php";



//$tableMenuS=550;
$tableMenuS=650;
ChangeWtitle("$SubCompany 车间生产中登记数量报告");
$funFrom="screport";
$From=$From==""?"read":$From;

$ColsNumber=11;				
//$Th_Col="配件|40|ID|30|PO|80|中文名|230|Product Code|180|检讨|30|Unit|35|Qty|50|生管备注|165|期限|40|打印|50|业务单流水号|80|已生产|50|删除登记|100";
$Th_Col="配件|40|ID|30|PO|80|中文名|230|Product Code|180|检讨|30|Unit|35|Qty|50|期限|40|打印|50|业务单流水号|80|已生产|50|删除登记|100";

//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="1";				//功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";

//$SearchRows=" AND A.TypeId='$TypeId' AND S.scFrom=2 AND S.Estate>0";//生产分类里的ID
//步骤4：需处理-可选条件下拉框
if($From!="slist"){
	$SearchRows="  AND (( S.scFrom=2 AND S.Estate=1) OR (S.scFrom=0 AND S.Estate=1)) ";	//$SearchRows=" AND S.scFrom=2 AND S.Estate>0 ";	
	echo "<select name='TypeId' id='TypeId' onchange='ResetPage(this.name)'>";   //统计类别
	$result = mysql_query("SELECT TypeId,TypeName  FROM $DataIn.stufftype WHERE mainType=3 AND Estate=1",$link_id);
	if($myrow = mysql_fetch_array($result)){
		do{
			$theTypeId=$myrow["TypeId"];
			$theTypeName=$myrow["TypeName"];
			$TypeId=$TypeId==""?$theTypeId:$TypeId;
			if($TypeId==$theTypeId){
				echo"<option value='$theTypeId' selected>$theTypeName</option>";
				$SearchRows.=" AND A.TypeId=".$theTypeId;
				}
			 else{
			 	echo"<option value='$theTypeId'>$theTypeName</option>";
				}
			}while($myrow = mysql_fetch_array($result));
		}
	echo"</select>";
	
	
	echo "<select name='CompanyId' id='CompanyId' onchange='ResetPage(this.name)'>";
	
	$result = mysql_query("SELECT M.CompanyId,C.Forshort 
		FROM $DataIn.yw1_ordermain M 
		LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber 
		LEFT JOIN $DataIn.trade_object C ON M.CompanyId=C.CompanyId 
		LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
		LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId 
		LEFT JOIN $DataIn.stuffdata A ON A.StuffId=G.StuffId 
		WHERE 1 $SearchRows GROUP BY M.CompanyId order by M.CompanyId",$link_id);
	
	
	if($myrow = mysql_fetch_array($result)){
		$i=1;
		do{
			$theCompanyId=$myrow["CompanyId"];
			$theForshort=$myrow["Forshort"];
			$CompanyId=$CompanyId==""?$theCompanyId:$CompanyId;
			if($CompanyId==$theCompanyId){
				echo"<option value='$theCompanyId' selected>$i 、$theForshort</option>";
				$SearchRows.=" AND M.CompanyId='$theCompanyId'";
				}
			 else{
			 	echo"<option value='$theCompanyId'>$i 、$theForshort</option>";
				}
			$i++;	
			}while($myrow = mysql_fetch_array($result));
		}
	echo"</select>";
	
	echo "<select name='ProductTypeId' id='ProductTypeId' onchange='ResetPage(this.name)'>";
	$result = mysql_query("SELECT P.TypeId,T.TypeName
		FROM $DataIn.yw1_ordermain M 
		LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber 
		LEFT JOIN $DataIn.trade_object C ON M.CompanyId=C.CompanyId 
		LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
		LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
		LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId 
		LEFT JOIN $DataIn.stuffdata A ON A.StuffId=G.StuffId 
		WHERE 1 $SearchRows GROUP BY P.TypeId ORDER BY T.mainType,T.TypeId",$link_id);
	//echo "<option value='' selected>全部</option>";
	while ($myrow = mysql_fetch_array($result)){
			$theTypeId=$myrow["TypeId"];
			$TypeName=$myrow["TypeName"];
			if($ProductTypeId==$theTypeId){
				echo "<option value='$theTypeId' selected>$TypeName</option>";
				$SearchRows.=" AND P.TypeId='$theTypeId'";
				}
			else{
				echo "<option value='$theTypeId'>$TypeName</option>";
				}			} 
		echo"</select>&nbsp;";
	}
	
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";


//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
echo"<div id='Jp' style='position:absolute; left:341px; top:229px; width:480px; height:50px;z-index:1;visibility:hidden;' tabIndex=0><input name='ActionTableId' type='hidden' id='ActionTableId'><input name='ActionRowId' type='hidden' id='ActionRowId'><input name='ObjId' type='hidden' id='ObjId'>
		<div class='out'>
			<div class='in' id='infoShow'>
			</div>
		</div>
	</div>";


$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql= "SELECT M.CompanyId,S.OrderPO,M.OrderDate,S.Id,S.POrderId,S.ProductId,S.Qty,S.Price,S.DeliveryDate,S.ShipType,S.scFrom,S.Estate,S.Locks,P.cName,P.eCode,P.TestStandard,P.pRemark,U.Name AS Unit
	FROM $DataIn.yw1_ordermain M
	LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
	LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
	LEFT JOIN $DataPublic.productunit U ON U.Id=P.Unit
	LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id
	LEFT JOIN $DataIn.yw2_orderexpress E ON E.POrderId=S.POrderId
	LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
	LEFT JOIN $DataIn.stuffdata A ON A.StuffId=G.StuffId
	WHERE 1  $SearchRows ORDER BY M.OrderDate";
//echo "$mySql";	
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$czSign=1;//操作标记
		$AskDay="";
		$thisBuyRMB=0;
		$OrderSignColor="bgColor='#FFFFFF'";
		//$theDefaultColor=$DefaultBgColor;
		$OrderPO=toSpace($myRow["OrderPO"]);
			
		$Id=$myRow["Id"];
		$ProductId=$myRow["ProductId"];
		$cName=$myRow["cName"];
		$eCode=toSpace($myRow["eCode"]);
		$TestStandard=$myRow["TestStandard"];
		include "../model/subprogram/product_teststandard.php";
		$Unit=$myRow["Unit"];
		$Qty=$myRow["Qty"];
		$Price=$myRow["Price"];
		$sgRemark=$myRow["sgRemark"]==""?"&nbsp;":$myRow["sgRemark"];
		$OrderDate=$myRow["OrderDate"];
		//如果超过30天
		$AskDay=AskDay($OrderDate);
		$BackImg=$AskDay==""?"":"background='../images/cj$AskDay'";
			
		$OrderDate=CountDays($OrderDate,0);
		$POrderId=$myRow["POrderId"];
		$scFrom=$myRow["scFrom"];
		$Estate=$myRow["Estate"];
		$LockRemark=$Estate==4?"已生成出货单.":"";
		$Locks=$myRow["Locks"];
			
		$sumQty=$sumQty+$Qty;
	
		//订单状态色：有未下采购单，则为白色
		$checkColor=mysql_query("SELECT G.Id FROM $DataIn.cg1_stocksheet G 
			LEFT JOIN $DataIn.stuffdata D ON G.StuffId=D.StuffId
			LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
			WHERE 1 AND T.mainType<2 AND G.Mid='0' and (G.FactualQty>'0' OR G.AddQty>'0' ) and G.PorderId='$POrderId' LIMIT 1",$link_id);
		if($checkColorRow = mysql_fetch_array($checkColor)){
			$OrderSignColor="bgColor='#FFFFFF'";//有未下需求单
			$czSign=0;//不能操作
			}
		else{//已全部下单	
			$OrderSignColor="bgColor='#FFCC00'";	//设默认绿色
			//生产数量与工序数量不等时，黄色
			////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//工序总数
			$CheckgxQty=mysql_fetch_array(mysql_query("SELECT SUM(G.OrderQty) AS gxQty 
				FROM $DataIn.cg1_stocksheet G
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
				LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
				WHERE G.POrderId='$POrderId' AND T.mainType=3",$link_id));
			$gxQty=$CheckgxQty["gxQty"];
			//已完成的工序数量
			$CheckscQty=mysql_fetch_array(mysql_query("SELECT SUM(C.Qty) AS scQty FROM $DataIn.sc1_cjtj C 
			LEFT JOIN $DataIn.stufftype T ON C.TypeId=T.TypeId
			WHERE C.POrderId='$POrderId' AND T.Estate=1 ",$link_id));
			$scQty=$CheckscQty["scQty"];
	
			if($gxQty==$scQty){//生产完毕
				$OrderSignColor="bgColor='#339900'";
				$czSign=0;//不能操作
				}
				////////////////////////////////////////////////////////////////
			}
		$ColbgColor="";
		//加急订单
		$checkExpress=mysql_query("SELECT Type FROM $DataIn.yw2_orderexpress WHERE POrderId='$POrderId' ORDER BY Id",$link_id);
		if($checkExpressRow = mysql_fetch_array($checkExpress)){
			do{
				$Type=$checkExpressRow["Type"];
				switch($Type){
					case 1:$ColbgColor="bgcolor='#0066FF'";break;	//自有产品标识
					case 2:$ColbgColor="bgcolor='#FF00'";break;		//未确定产品
					case 7:$theDefaultColor="#FFA6D2";break;		//加急
					}
				}while ($checkExpressRow = mysql_fetch_array($checkExpress));
			}
		//动态读取配件资料
		//$showPurchaseorder="[ + ]";
		$ListRow="<tr bgcolor='#D9D9D9' id='ListRow$i' style='display:none'><td class='A0111' height='30' colspan='$Count'><br><div id='ShowDiv$i'>&nbsp;</div><br></td></tr>";
		//此工序总数
		$CheckStuffQty=mysql_fetch_array(mysql_query("SELECT ifnull(SUM(G.OrderQty),0) AS sQty 
		FROM $DataIn.cg1_stocksheet G
		LEFT JOIN $DataIn.stuffdata A ON A.StuffId=G.StuffId
		WHERE G.POrderId='$POrderId' AND A.TypeId='$TypeId'",$link_id));
		$SumGXQty=$CheckStuffQty["sQty"];
		//已完成的工序数量
		$CheckCfQty=mysql_fetch_array(mysql_query("SELECT ifnull(SUM(C.Qty),0) AS cfQty FROM $DataIn.sc1_cjtj C WHERE C.POrderId='$POrderId' AND C.TypeId='$TypeId'",$link_id));
		$OverPQty=$CheckCfQty["cfQty"];
		
		//已生产数字显示方式
		switch($OverPQty){
			case 0:$OverPQty="&nbsp;";break;
			default://生产数量非0
				if($SumGXQty==$OverPQty){//生产完成
					$OverPQty="<div class='greenB'>$OverPQty</div>";$czSign=0;//不能操作
					}
				else{
					if($SumGXQty>$OverPQty){//未完成
						$OverPQty="<div class='yellowB'>$OverPQty</div>";
						}
					else{//多完成
						$OverPQty="<div class='redB'>$OverPQty</div>";
						}
					}
				break;
				}
		//操作权限:如果权限=31 则可以操作,否则不能操作
		$UpdateIMG="&nbsp;";$UpdateClick="&nbsp;";
		$PrintIMG="&nbsp;";$PrintClick="&nbsp;";
		if($czSign==1){//可以操作
			if($SubAction==31){//有权限
				//$UpdateIMG="<img src='../images/register.png' width='30' height='30'";$UpdateClick="onclick='RegisterQty($POrderId,$TypeId)'";
				$PrintIMG="<img src='../images/printer.png' width='30' height='30'";$PrintClick="onclick='PrintTasks($POrderId)'";
				}
			else{//无权限
				if($SubAction==1){
					//$UpdateIMG="<img src='../images/registerNo.png' width='30' height='30'";
					$PrintIMG="<img src='../images/printerNo.png' width='30' height='30'";
					}
				}
			}
		$SId="$POrderId|$TypeId|$scFrom";
		$UpdateIMG="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"screport_update\",\"$SId\")' src='../images/register.png' alt='更新产量' width='13' height='13'>";
		
		//$UpdateIMG="<img src='../images/register.png' width='30' height='30'";$UpdateClick="onclick='RegisterQty($POrderId,$TypeId)'";
	
	
		if($Estate!=1){//生产完毕
			$UpdateIMG="";
			$UpdateClick="bgcolor='#339900'";
			}
		
			$ValueArray=array(
				array(0=>$OrderPO),
				array(0=>$TestStandard,	2=>"onmousedown='window.event.cancelBubble=true;'"),
				array(0=>$eCode,		3=>"..."),
				array(0=>$CaseReport,	1=>"align='center'"),
				array(0=>$Unit,			1=>"align='center'"),
				array(0=>$Qty,          1=>"align='right'"),
				array(0=>$OrderDate,	1=>"align='center'"),
				array(0=>$PrintIMG,		1=>"align='center'"),
				array(0=>$POrderId,		1=>"align='center'"),
				array(0=>$OverPQty,		1=>"align='center'"),
				array(0=>$UpdateIMG,	1=>"align='center'")
				);
	
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
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

