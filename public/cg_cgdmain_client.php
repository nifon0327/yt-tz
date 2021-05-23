<?php 
include "../model/modelhead.php";
$ColsNumber=22;
$tableMenuS=600;
ChangeWtitle("$SubCompany 客供配件列表");
$funFrom="cg_cgdmain";
$From=$From==""?"client":$From;
$sumCols="10,11,12,15,16,17,18,19,20,21";			//求和列,需处理
$Th_Col="选项|50|序号|30|配件ID|40|配件名称|250|图档|30|历史<br>订单|40|QC图|40|品检<br>报告|40|认证|40|开发|40|需求数|45|增购数|45|实购数|45|单价|40|单位|45|金额|60|收货数|45|领料数|45|欠数|45|退货|45|补仓|45|交货日期|90|采购流水号|100|供应商|80";

//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 200;							//每页默认记录数量,13
$ActioToS="1";
$nowWebPage=$funFrom."_client";
include "../model/subprogram/read_model_3.php";
$SearchRowsA=$Weeks>0?"  AND YEARWEEK(S.DeliveryDate,1)='$Weeks'": "  AND S.DeliveryDate='0000-00-00'";
$SearchRows=$Weeks>0?"   AND YEARWEEK(S.DeliveryDate,1)='$Weeks'": "  AND S.DeliveryDate='0000-00-00'";
	//供应商
	$providerSql= mysql_query("SELECT  S.CompanyId,V.Forshort,V.Letter 
	FROM  $DataIn.cg1_stocksheet  S 
	LEFT JOIN $DataIn.trade_object V ON V.CompanyId=S.CompanyId
	LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
   LEFT JOIN  $DataIn.stuffproperty  OP  ON OP.StuffId=D.StuffId AND OP.Property=2
	WHERE 1 $SearchRows  AND V.Estate=1 AND S.Mid=0 AND S.rkSign>0  AND IFNULL(OP.Property,0)=2  GROUP BY V.CompanyId ORDER BY V.Letter",$link_id);
	if($providerRow = mysql_fetch_array($providerSql)){
		echo "<select name='CompanyId' id='CompanyId' onchange='document.form1.submit()'>";
				echo"<option value=''>全部</option>";
		do{
			$Letter=$providerRow["Letter"];
			$Forshort=$providerRow["Forshort"];
			$Forshort=$Letter.'-'.$Forshort;
			$thisCompanyId=$providerRow["CompanyId"];	
			if($CompanyId==$thisCompanyId){
				echo"<option value='$thisCompanyId' selected>$Forshort </option>";
				$SearchRows.=" and V.CompanyId='$thisCompanyId'";
				}
			else{
				echo"<option value='$thisCompanyId'>$Forshort</option>";
				}
			}while ($providerRow = mysql_fetch_array($providerSql));
		echo"</select>&nbsp;";
}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql="SELECT S.Id,S.Mid,S.StockId,S.POrderId,S.StuffId,S.Price,S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,S.CompanyId,S.BuyerId,S.DeliveryDate,
S.StockRemark,S.AddRemark,S.Estate,S.Locks,S.rkSign,Y.OrderPO,Y.Qty as PQty,Y.PackRemark,Y.sgRemark,Y.ShipType,PI.Leadtime,A.StuffCname,A.Gfile,A.Gstate,
A.Gremark,A.Picture,A.TypeId,A.DevelopState,C.Forshort AS Client,P.cName,P.TestStandard,P.ProductId,U.Name AS UnitName,V.Forshort   
FROM (
       SELECT A.* FROM( 
            SELECT B.StockId,B.Qty,SUM(IFNULL(B.rkQty,0)+IFNULL(B.SendQty,0)) as rkQty
									   FROM (
									    SELECT S.StockId,(S.FactualQty+S.AddQty) AS Qty,SUM(IFNULL(R.Qty,0)) AS rkQty,0 AS SendQty  
									          FROM $DataIn.cg1_stocksheet S 
									         LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
									         LEFT JOIN  $DataIn.stuffproperty  OP  ON OP.StuffId=D.StuffId AND OP.Property=2  
									          LEFT JOIN $DataIn.ck1_rksheet R ON R.StockId=S.StockId
									         WHERE  S.Mid=0 $SearchRowsA  AND IFNULL(OP.Property,0)=2  GROUP BY S.StockId 
									        UNION ALL 
									          SELECT  G.StockId,0 AS Qty,0 AS rkQty,SUM(G.Qty) AS SendQty 
									          FROM $DataIn.gys_shsheet G 
                                              LEFT JOIN $DataIn.cg1_stocksheet S ON S.StockId=G.StockId
									         LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
									         LEFT JOIN  $DataIn.stuffproperty  OP  ON OP.StuffId=D.StuffId AND OP.Property=2
									          WHERE  G.SendSign=0 AND G.Estate>0 AND  S.StockId>0   AND S.Mid=0 AND IFNULL(OP.Property,0)=2  $SearchRowsA  GROUP BY G.StockId
									   )B  GROUP BY B.StockId  
		)A WHERE A.Qty>A.rkQty 
) K  								
LEFT JOIN $DataIn.cg1_stocksheet S ON S.StockId=K.StockId  
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId
LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=Y.Id
LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
LEFT JOIN $DataIn.trade_object V ON V.CompanyId=S.CompanyId 
LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
LEFT JOIN $DataPublic.stuffunit U ON U.Id=A.Unit
WHERE 1 $SearchRows  $SearchEstate  AND  S.Mid=0  GROUP BY S.StockId ORDER BY S.Mid DESC,S.POrderId";
//if ($Login_P_Number==10868)echo $mySql;
$mainResult = mysql_query($mySql." $PageSTR",$link_id);
if($mainRows = mysql_fetch_array($mainResult)){
	$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
	do{
		    $m=1;
		    $Id=$mainRows["Id"];
		    $StuffId=$mainRows["StuffId"];
            $StuffCname=$mainRows["StuffCname"];
			$OrderQty=$mainRows["OrderQty"];
			$FactualQty=$mainRows["FactualQty"];
			$AddQty=$mainRows["AddQty"];
			$Qty=$FactualQty+$AddQty;
			$Price=$mainRows["Price"];
			$Amount=sprintf("%.2f",$Qty*$Price);		
			$StockId=$mainRows["StockId"];
			$Estate=$mainRows["Estate"];
			$UnitName=$mainRows["UnitName"]==""?"&nbsp;":$mainRows["UnitName"];
			$Locks=$mainRows["Locks"];
			$BuyerId=$mainRows["BuyerId"];
			$CompanyId=$mainRows["CompanyId"];
			$OrderPO=$mainRows["OrderPO"];
			$POrderId=$mainRows["POrderId"];
			//$tdBGCOLOR=$mainRows["POrderId"]==""?"bgcolor='#FFCC99'":"";
			$tdBGCOLOR=$POrderId==""?"bgcolor='#FFCC99'":"";
			$PQty=$mainRows["PQty"];
			$PackRemark=$mainRows["PackRemark"];
			$sgRemark=$mainRows["sgRemark"];		
			$ShipType=$mainRows["ShipType"];
			$Leadtime=$mainRows["Leadtime"];
			
			//检查订单的采购单是否有设交期:
			/*
			 $CheckCgResult=mysql_query("SELECT DeliveryDate FROM  $DataIn.cg1_stocksheet  
			   WHERE POrderId='$POrderId'  AND Mid>0  AND DeliveryDate!='0000-00-00' LIMIT 1",$link_id);
			 if($CheckCgRows = mysql_fetch_array($CheckCgResult)){ 
			      $cg_DeliveryDate=$CheckCgRows["DeliveryDate"];
			     $DeliveryDateSql = "UPDATE $DataIn.cg1_stocksheet SET DeliveryDate='$cg_DeliveryDate' WHERE StockId='$StockId' AND Estate='0'";
		         $DeliveryDateResult = mysql_query($DeliveryDateSql);
			}
			*/
			$Forshort=$mainRows["Forshort"];
			$TypeId=$mainRows["TypeId"];
			$Gremark=$mainRows["Gremark"];
			$Gfile=$mainRows["Gfile"];
			$Gstate=$mainRows["Gstate"];
			include "../model/subprogram/stuffimg_Gfile.php";	//图档显示
			//检查是否有图片
                        
             //配件QC检验标准图
             include "../model/subprogram/stuffimg_qcfile.php";
            //配件品检报告qualityReport
            include "../model/subprogram/stuff_get_qualityreport.php";
			//REACH 法规图
		   include "../model/subprogram/stuffreach_file.php";
                         
			$Picture=$mainRows["Picture"];
			include "../model/subprogram/stuffimg_model.php";
			include"../model/subprogram/stuff_Property.php";//配件属性
			
			$OrderQtyInfo="<a href='cg_historyorder.php?StuffId=$StuffId&Id=$Id' target='_blank'>查看</a>";
			//供应商结付货币的汇率
			$Rate=1;
			$currency_Temp = mysql_query("SELECT C.Rate FROM $DataPublic.currencydata C
			                              LEFT JOIN $DataIn.trade_object P  ON P.Currency=C.Id 
			                              WHERE P.CompanyId='$CompanyId' ORDER BY C.Id LIMIT 1",$link_id);
			if($RowTemp = mysql_fetch_array($currency_Temp)){
				$Rate=$RowTemp["Rate"];//汇率
				}
			
			$rmbAmount=sprintf("%.2f",$Amount*$Rate);
			///仓库情况////////////////////////////////////////
			
			//收货情况				
			$rkTemp=mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.ck1_rksheet WHERE StockId='$StockId' order by Id",$link_id);
			$rkQty=mysql_result($rkTemp,0,"Qty");
			$rkQty=$rkQty==""?0:$rkQty;
			//领料情况
			$llTemp=mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.ck5_llsheet WHERE StockId='$StockId' order by Id",$link_id); 
			$llQty=mysql_result($llTemp,0,"Qty");
			$llQty=$llQty==""?0:$llQty;
			$llBgColor="";
			if($tdBGCOLOR==""){
				if($llQty==$OrderQty){
					$llBgColor="class='greenB'";
					}
				else{
					$llBgColor="class='yellowB'";
					}
				}
			else{
				$llBgColor="class='greenB'";
				}
		//退换数量
		$UnionSTR7=mysql_query("SELECT SUM(Qty) AS thQty FROM $DataIn.ck2_thsheet WHERE StuffId='$StuffId'",$link_id);
		$thQty=mysql_result($UnionSTR7,0,"thQty");
		$thQty=$thQty==""?0:$thQty;
		
		$LockRemark="";
		//补仓数量
		$UnionSTR8=mysql_query("SELECT SUM(Qty) AS bcQty FROM $DataIn.ck3_bcsheet WHERE StuffId='$StuffId'",$link_id);
		$bcQty=mysql_result($UnionSTR8,0,"bcQty");
		$bcQty=$bcQty==""?0:$bcQty;
        if($bcQty<$thQty) {
			if ($isEstate==1) {  //如果是请款，则不用锁定
				//$LockRemark="未补完货!";	
			}
			$bcQty="<span class='redB'>$bcQty</span>";
		}
		else {
			if($bcQty>0) {
				$bcQty="<span class='greenB'>$bcQty</span>";	
			}
		}
        if($thQty>0)$thQty="<a href='ck_th_read.php?tempStuffId=$StuffId' target='_blank'><span style='color:#000'>$thQty</span></a>";

//尾数
			$Mantissa=$Qty-$rkQty;$Sid=anmaIn($StockId,$SinkOrder,$motherSTR);
			if($Mantissa<=0){
				$BGcolor="class='greenB'";$StockIdShow="<a href='ck_rk_list.php?Sid=$Sid' target='_blank'>$StockId</a>";
				if($Mantissa<0){
					$BGcolor="class='redB'";
					//$Mantissa="错误";
					$Mantissa="<div class='redB' title='错误(入库数量>采购数量)'>错误</div>";
					}
					$rkSign=$mainRows["rkSign"];
					if ($rkSign>0){
					      //更改入库标记
						  $uprkSignSql="UPDATE $DataIn.cg1_stocksheet  SET rkSign=0 WHERE StockId='$StockId'";
						  $UprkResult=mysql_query($uprkSignSql);
						  echo "<div class='redB'>入库标志更新:该采购单已全部入库</div>";
					}
				}
			else{
				$StockIdShow=$StockId;
				if($Mantissa==$Qty){					
					$BGcolor="class='redB'";
					
					}
				else{
					//$LockRemark="已收货，锁定操作";
					 
					$BGcolor="class='yellowB'";$StockIdShow="<a href='ck_rk_list.php?Sid=$Sid' target='_blank'>$StockId</a>";
					}
				/*	
				if ($isEstate==1) {  //如果是请款，则不用锁定
					$LockRemark="未收完货!";	
				}	
				*/
			}
			
			//默认单价
		$priceRes=mysql_query("SELECT S.Price FROM $DataIn.stuffdata S WHERE S.StuffId='$StuffId'",$link_id);
		if($priceRow=mysql_fetch_array($priceRes)){
			$DefaultPrice=$priceRow["Price"];
		}
		if($DefaultPrice!=$Price){
			$Price="<div class='redB'>$Price</div>";
			$PriceTitle="Title=\"默认单价：$DefaultPrice\"";
		}
			$cName=$mainRows["cName"];
			$Client=$mainRows["Client"];
			include "../model/subprogram/cg_cgd_jj.php";
			//交货日期颜色
			$OnclickStr="";
			$DeliveryDate=$mainRows["DeliveryDate"];

			if($Login_P_Number==10002 || $Login_P_Number==10341 || $Login_P_Number==10008 || $Login_P_Number==10868 || $Login_P_Number==10871){
				//$OnclickStr="onclick='updateJq($i,$StockId)' style='CURSOR: pointer;'";
				$OnclickStr="onclick='set_weekdate(this,$StockId)' style='CURSOR: pointer;'";
				
				}

include "../model/subprogram/CG_DeliveryDate.php";
			//原交货日期
			$CheckOldDate=mysql_query("SELECT YEARWEEK(DeliveryDate,1) AS Week FROM $DataIn.cg1_DeliveryDate WHERE StockId='$StockId' AND DeliveryDate!='$DeliveryDate' ORDER BY Id DESC LIMIT 1",$link_id);
			if($oldDateRow = mysql_fetch_array($CheckOldDate)){
			       $oldDeliveryDate="Week " . substr($oldDateRow["Week"],4,2);
			       $dateSignImage="<div style='float:left;margin:0px 5px 0px 5px'><img src='../images/icon_abnormal.gif'  width='20' height='20' title='原交期:". $oldDeliveryDate . " ' style='vertical-align:middle;'/></div>";
			}
			else{
				   $dateSignImage="";
			}
		
	   $DevelopWeekState=1; 	
	   $DevelopState=$mainRows["DevelopState"];
		include "../model/subprogram/stuff_developstate.php";		
			
            /*加入同一单的配件  // add by zx 2011-08-04 */
			$showPurchaseorder="<img onClick='ShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$POrderId\",$i,\"\");' name='showtable$i' src='../images/showtable.gif' 
			title='显示或隐藏配件采购明细资料.' width='13' height='13' style='CURSOR: pointer'>";
			$XtableWidth=$tableWidth-160;
			$XtableWidth=0;
			//$XsubTableWidth=$subTableWidth-160;
			$ProductId=$mainRows["ProductId"];
			$TestStandard=$mainRows["TestStandard"];
		include "../admin/Productimage/getProductImage.php";
			$StuffListTB="
				<table width='$XtableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
				<tr bgcolor='#B7B7B7'>
				<td class='A0111' ><br> &nbsp;PO：$OrderPO&nbsp;<span class='redB'>业务单流水号：$POrderId </span>($Client : $TestStandard)&nbsp;<span class='redB'>数量：$PQty </span>&nbsp;订单备注：$PackRemark <span class='redB'>出货方式：$ShipType</span> 生管备注：$sgRemark <span class='redB'>PI交期：$Leadtime</span></td>
				</tr>
				
				<tr bgcolor='#B7B7B7'>
				<td class='A0111' height='30' align='left'><br><div id='showStuffTB$i' width='$XsubTableWidth'>&nbsp;</div><br></td></tr></table>";

		$ValueArray=array(
			array(0=>$StuffId,		1=>"align='center'"),
			array(0=>$StuffCname),
			array(0=>$Gfile,		1=>"align='center'"),
			array(0=>$OrderQtyInfo, 1=>"align='center'"),
			array(0=>$QCImage, 	1=>"align='center'"),
			array(0=>$qualityReport, 	1=>"align='center'"),
			array(0=>$ReachImage, 	1=>"align='center'"),
			array(0=>$DevelopState, 	1=>"align='center'"),
			array(0=>$FactualQty,		1=>"align='right'"),
			array(0=>$AddQty,		1=>"align='right'"),
			array(0=>$Qty, 	1=>"align='right'"),
			array(0=>$Price, 		1=>"align='right'"),
			array(0=>$UnitName, 			1=>"align='right'"),
			array(0=>$Amount,	 	1=>"align='right' $PriceTitle"),
			array(0=>$rkQty,	 	1=>"align='right'"),
			array(0=>$llQty, 		1=>"align='right'"),
			array(0=>"<div $BGcolor>$Mantissa</div>", 		1=>"align='center'"),
			array(0=>$thQty, 		1=>"align='right'"),
			array(0=>$bcQty, 		1=>"align='right'"),
			array(0=>$dateSignImage.$DeliveryDateShow, 		1=>"align='center'", 2=>"$OnclickStr"),
            array(0=>"<div title='$Client : $cName'>$StockIdShow</div>"),
            array(0=>$Forshort,	1=>"align='center' ")
			);
		include "../model/subprogram/read_model_6.php";
		echo $StuffListTB;

		}while($mainRows = mysql_fetch_array($mainResult));	
	}
else{
	noRowInfo($tableWidth);
	}
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
<script src='../model/weekdate.js' type=text/javascript></script>
<script>
var weekdate=new WeekDate();
function set_weekdate(el,StockId){
	  var saveFun=function(){
			     if (weekdate.Value>0){
					       var tempWeeks=weekdate.Value.toString();
					       tempWeeks="Week "+tempWeeks.substr(4, 2);
						   var tempDeliveryDate=weekdate.getWedday("-");
						   myurl="purchaseorder_updated.php?StockId="+StockId+"&DeliveryDate="+tempDeliveryDate+"&ActionId=jq";
						  // alert(myurl);return;
					     var ajax=InitAjax(); 
						 ajax.open("GET",myurl,true);
						 ajax.onreadystatechange =function(){
						     if(ajax.readyState==4){
								     el.innerHTML=tempWeeks;
							   }
					    }
						ajax.send(null);   
				}
		};
	   weekdate.show(el,1,saveFun,"");
}

</script>