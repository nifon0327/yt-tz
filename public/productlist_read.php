<?php 
//步骤1 分开已更新电信---yang 20120801
include "../model/modelhead.php";
include "../model/subprogram/sys_parameters.php";
?>
<script>
function ViewChart(Pid,OpenType){
	document.form1.action="productdata_chart.php?Pid="+Pid+"&Type="+OpenType;
	document.form1.target="_blank";
	document.form1.submit();		
	document.form1.target="_self";
	document.form1.action="";
	}
</script>
<?php 
//步骤2：需处理
$ColsNumber=21;
$tableMenuS=700;
ChangeWtitle("$SubCompany 产品列表");
$funFrom="productlist";
$From=$From==""?"read":$From;
$ChooseOut="N";
//特殊权限 121
$TResult = mysql_query("SELECT Id FROM $DataIn.taskuserdata WHERE ItemId=144 and UserId=$Login_P_Number LIMIT 1",$link_id);
if($TRow = mysql_fetch_array($TResult)){
	$Th_Col="序号|35|客户|70|产品ID|50|中文名|250|Product Code|160|单品重<br>(g)|50|成品重<br>(g)|40|检讨|40|Price|60|货币<br>符号|30|利润|100|装箱<br>单位|40|已出数量<br>(下单次数)|60|退货<br>数量|50|最后出货<br>日期|50|产品<br>备注|40|背卡<br>条码|30|PE袋<br>条码|30|外箱<br>标签|30|所属分类|100|高清|35|报价规则|350";
	$myTask=1;
	}
else{
	$Th_Col="序号|35|客户|70|产品ID|50|中文名|250|Product Code|160|单品重<br>(g)|50|成品重<br>(g)|40|检讨|40|Price|60|货币<br>符号|30|装箱<br>单位|40|已出数量<br>(下单次数)|60|退货<br>数量|50|最后出货<br>日期|50|产品<br>备注|40|背卡<br>条码|30|PE袋<br>条码|30|外箱<br>标签|30|所属分类|100|高清|35|报价规则|350";
	$myTask=0;
	}
include "../model/subprogram/business_authority.php";//看客户权限
//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 300;							//每页默认记录数量
if ($ProfitType!="") $Pagination=0;
$ActioToS="1,38";
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-可选条件下拉框
if($From!="slist"){
	$SearchRows="";
	$cSignStr=$Login_cSign==""?"":" AND M.cSign=$Login_cSign ";
	echo "<select name='CompanyId' id='CompanyId' onchange='ResetPage(this.name)'>";
	$result = mysql_query("SELECT M.CompanyId,M.Forshort FROM $DataIn.trade_object M
	WHERE M.Estate=1 $cSignStr $ClientStr ORDER BY M.Id",$link_id);

	if($myrow = mysql_fetch_array($result)){
		do{
			$theCompanyId=$myrow["CompanyId"];
			$theForshort=$myrow["Forshort"];
			$CompanyId=$CompanyId==""?$theCompanyId:$CompanyId;
			if($CompanyId==$theCompanyId){
				echo"<option value='$theCompanyId' selected>$theForshort</option>";
				$SearchRows=" AND P.CompanyId=".$theCompanyId;
				}
			 else{
			 	echo"<option value='$theCompanyId'>$theForshort</option>";
				}
			}while($myrow = mysql_fetch_array($result));
		}
	echo"</select>
	<select name='ProductType' id='ProductType' onchange='ResetPage(this.name)'>";
	$result = mysql_query("SELECT T.TypeId,T.Letter,T.TypeName,C.Color  FROM $DataIn.producttype T 
	LEFT JOIN $DataIn.productmaintype C ON C.Id=T.mainType
	WHERE T.Estate=1 and 
	T.TypeId IN (SELECT TypeId FROM $DataIn.productdata WHERE CompanyId=$CompanyId GROUP BY TypeId) order by T.mainType",$link_id);
	echo "<option value='' selected>全部</option>";
	while ($myrow = mysql_fetch_array($result)){
			$TypeId=$myrow["TypeId"];
			$Color=$myrow["Color"]==""?"#000000":$myrow["Color"];
			if ($ProductType==$TypeId){
				echo "<option value='$TypeId' style= 'color: $Color;font-weight: bold' selected>$myrow[Letter]-$myrow[TypeName]</option>";
				}
			else{
				echo "<option value='$TypeId' style= 'color: $Color;font-weight: bold'>$myrow[Letter]-$myrow[TypeName]</option>";
				}
			} 
	echo"</select>&nbsp;";
	
	$TempProfitSTR="ProfitTypeStr".strval($ProfitType); 
	$$TempProfitSTR="selected";
	echo"<select name='ProfitType' id='ProfitType' onchange='ResetPage(this.name)'>";
		echo"<option value='' $ProfitTypeStr>全部净利</option>
		<option value='1' style= 'color:#FF00CC;' $ProfitTypeStr1>0以下</option>
		<option value='2' style= 'color:#FF0000;' $ProfitTypeStr2>0-7%</option>
		<option value='3' style= 'color:#FF6633;' $ProfitTypeStr3>8-15%</option>
		<option value='4' style= 'color:#009900;' $ProfitTypeStr4>16%以上</option>
		<option value='5' style= 'color:#BB0000;' $ProfitTypeStr5>未设定</option>
	</select>&nbsp;";
	
	$CompanyIdSTR=" and P.CompanyId=".$CompanyId;
	$TypeIdSTR=$ProductType==""?"":" and P.TypeId=".$ProductType;
	$SearchRows=$CompanyIdSTR.$TypeIdSTR;
	$TempProfitSTR="LastMStr".strval($LastM); 
	$$TempProfitSTR="selected";
	echo"<select name='LastM' id='LastM' onchange='ResetPage(this.name)'>";
		echo"<option value='' $LastMStr>全部出货日期</option>
		<option value='1' style= 'color:#090;' $LastMStr1>半年内</option>
		<option value='2' style= 'color:#f60;' $LastMStr2>半年至1年</option>
		<option value='3' style= 'color:#f00;' $LastMStr3>1年以上</option>
	</select>&nbsp;";
	switch($LastM){
		case 1://<6
			$ShipMonthStr=" AND (E.Months<6 OR E.Months IS NULL)";
		break;
		case 2://6<=  <12
			$ShipMonthStr=" AND E.Months>5 AND E.Months<12 AND E.Months IS NOT NULL";
		break;
		case 3://>=12
			$ShipMonthStr=" AND E.Months>11 AND E.Months IS NOT NULL";
		break;
		default://全部
		$ShipMonthStr="";
		break;
		}
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";

//步骤5：
include "../model/subprogram/read_model_5.php";
include "../model/subprogram/CurrencyList.php";

//步骤6：需处理数据记录处理
$i=1;$KillRecord=0;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$dirforstuff=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
$mySql= "SELECT P.Id,P.ProductId,P.cName,P.eCode,P.Price,P.Unit,P.CompanyId,P.Description,P.Remark,P.bjRemark,P.pRemark,P.Weight,P.MainWeight,
	P.TestStandard,P.Img_H,P.Date,P.PackingUnit,P.Estate,P.Locks,P.Code,P.Operator,
	C.Forshort,T.TypeName,C.Currency,E.Months,E.LastMonth
	FROM $DataIn.productdata P
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
	LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId
	LEFT JOIN (
			   SELECT DATE_FORMAT(MAX(M.Date),'%Y-%m') AS LastMonth,TIMESTAMPDIFF(MONTH,MAX(M.Date),now()) AS Months,S.ProductId FROM $DataIn.ch1_shipmain M 
	LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=M.Id 
    WHERE 1 GROUP BY S.ProductId ORDER BY M.Date DESC
					) E ON E.ProductId=P.ProductId
	where 1  AND P.Estate=1 $SearchRows  $ShipMonthStr  order by P.Estate DESC,P.Id DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
//echo $mySql;
//if($myRow = mysql_fetch_array($myResult)){
if (mysql_num_rows($myResult)>0){
	$d=anmaIn("download/productfile/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		$Months=$myRow["Months"];
		$LastMonth=$myRow["LastMonth"];
		$TempInfo="";$TempInfo2="";
		$Id=$myRow["Id"];		
		$Client=$myRow["Forshort"];
		$ProductId=$myRow["ProductId"];
		$cName=$myRow["cName"];
		$eCode=$myRow["eCode"]==""?"&nbsp;":$myRow["eCode"];
		$Weight=$myRow["Weight"];
		$MainWeight=$myRow["MainWeight"]==0?"&nbsp;":$myRow["MainWeight"];
		$Remark=trim($myRow["Remark"])==""?"&nbsp;":"<img src='../images/remark.gif' alt='$myRow[Remark]' width='18' height='18'>";
		$bjRemark=trim($myRow["bjRemark"])==""?"&nbsp;":$myRow["bjRemark"];
		$pRemark=trim($myRow["pRemark"])==""?"&nbsp;":"<img src='../images/remark.gif' alt='$myRow[pRemark]' width='18' height='18'>";
		$Description=$myRow["Description"]==""?"&nbsp;":"<img src='../images/remark.gif' alt='$myRow[Description]' width='18' height='18'>";
		$Price=$myRow["Price"];
		$Currency=$myRow["Currency"];
		$TestStandard=$myRow["TestStandard"];
		include "../admin/Productimage/getProductImage.php";

		
		$Img_H=$myRow["Img_H"];  //add by zx 20120207 高清
		if($Img_H>0){
			$I_FilePath="download/teststandard/";
			$I_Field="T".$ProductId."_".'H'.".zip";
			//echo "$Field";
			$I_Field=anmaIn($I_Field,$SinkOrder,$motherSTR);
			$I_td=anmaIn("$I_FilePath",$SinkOrder,$motherSTR);
			//$Img="<span onClick='OpenOrLoad(\"$Dir\",\"$Img\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			//$uploadInfo="<img onClick='OpenOrLoad(\"$td\",\"$Field\")' src='../images/down.gif' alt='$Gremark' width='18' height='18'>";
			//$Img_H="<a href='' onClick='OpenOrLoad(\"$I_td\",\"$I_Field\")' >H</a>";
			$Img_H="<a href=\"../admin/openorload.php?d=$I_td&f=$I_Field&Type=&Action=6\" target=\"download\">H</a>";
			//$Img_H="&nbsp;";

			}
		else{
			$Img_H="&nbsp;";
			
			}	
			
		$Code=$myRow["Code"]==""?"&nbsp;":"<img src='../images/remark.gif' alt='$myRow[Code]' width='18' height='18'>";
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$PackingUnit=$myRow["PackingUnit"];
		if  ($PackingUnit){
	       	$uResult = mysql_query("SELECT Name FROM $DataPublic.packingunit WHERE Id=$PackingUnit order by Id Limit 1",$link_id);
		   if($uRow = mysql_fetch_array($uResult)){
			       $PackingUnit=$uRow["Name"];
			}			
		}
		$Unit=$myRow["Unit"];
		$Date=$myRow["Date"];
		$Locks=$myRow["Locks"];
		//操作员姓名
		include "../model/subprogram/staffname.php";
		$TypeName=$myRow["TypeName"];
		if ($Currency){
			$currency_Temp = mysql_query("SELECT Rate,Symbol FROM $DataPublic.currencydata  WHERE Id=$Currency",$link_id);
			if($RowTemp = mysql_fetch_array($currency_Temp)){
				$Rate=$RowTemp["Rate"];//汇率
				$Symbol=$RowTemp["Symbol"];//货币符号
				}
		}
		else{
			$Rate=1;$Symbol="";
		}
		$saleRMB=sprintf("%.2f",$Price*$Rate);//产品销售RMB价格
		$GfileStr="";
		//注意：外购按供应商货币符号区分，USD的就纳入外购
		$cbResult = mysql_query("
		SELECT D.Price,C.Rate,A.Relation,P.Currency
			FROM $DataIn.pands A
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId=A.StuffId
			LEFT JOIN $DataIn.bps B ON B.StuffID=D.StuffId
			LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId
			LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
			WHERE A.ProductId='$ProductId'
		",$link_id);
		if($cbRow=mysql_fetch_array($cbResult)) {//如果设定了产品配件关系
			$BuyRmbSum=0;	//配件全部成本
			$BuyHzSum=0;	//非美元行政成本
			do{	
				$stuffPrice=$cbRow["Price"];		//配件价格
				$Currency=$cbRow["Currency"];		//货币ID
				$gRate=$cbRow["Rate"];				//货币汇率
				$Relation=$cbRow["Relation"];		//对应数量
				
				$OppositeQTY=explode("/",$Relation);
				$thisRMB=$OppositeQTY[1]!=""?sprintf("%.4f",$gRate*$stuffPrice*$OppositeQTY[0]/$OppositeQTY[1]):sprintf("%.4f",$gRate*$stuffPrice*$OppositeQTY[0]);	//此配件的成本
				$BuyRmbSum+=$thisRMB;				//配件成本累加
				$BuyHzSum=$Currency==1?($BuyHzSum+$thisRMB):$BuyHzSum;				//自购成本累加
				}while($cbRow=mysql_fetch_array($cbResult));
				
			$profitRMB=sprintf("%.2f",$saleRMB-$BuyRmbSum-$BuyHzSum*$HzRate);	//利润
			if ($saleRMB!=0)  $profitRMBPC=sprintf("%.0f",($profitRMB*100/$saleRMB));				//净利百分比
		
		//净利分类			
		$ViewSign=0;
		if($profitRMBPC>15){
			$ViewSign=$ProfitType==4?1:0;
			$profitRMB="<a href='pands_profit.php?From=task&Cid=$ProductId' target='_blank'><span class='greenB'>$profitRMB($profitRMBPC%)</sapn></a>";
			}
		else{
			if($profitRMBPC>7){
				$ViewSign=$ProfitType==3?1:0;
				$profitRMB="<a href='pands_profit.php?From=task&Cid=$ProductId' target='_blank'><span class='yellowB'>$profitRMB($profitRMBPC%)</sapn></a>";
				}
			else{
				if($profitRMB<0){
					$ViewSign=$ProfitType==1?1:0;
					$profitRMB="<a href='pands_profit.php?From=task&Cid=$ProductId' target='_blank'><span class='purpleB'>$profitRMB($profitRMBPC%)</sapn></a>";
					}
				else{
					$ViewSign=$ProfitType==2?1:0;
					$profitRMB="<a href='pands_profit.php?From=task&Cid=$ProductId' target='_blank'><span class='redB'>$profitRMB($profitRMBPC%)</sapn></a>";
					}
				}
			}
		  }
      else{
		    $ViewSign=$ProfitType==5?1:0;
			$profitRMB="<div class='redB'>未设定</div>";
		}  
		if ($ProfitType=="") $ViewSign=1;
		/*	$profitRMB=$profitRMBPC>15?"<span class='greenB'>$profitRMB($profitRMBPC%)</sapn>":($profitRMBPC>7?"<span class='yellowB'>$profitRMB($profitRMBPC%)</sapn>":($profitRMB<0?"<span class='purpleB'>$profitRMB($profitRMBPC%)</sapn>":"<span class='redB'>$profitRMB($profitRMBPC%)</sapn>"));
			$profitRMB="<a href='pands_profit.php?From=task&Cid=$ProductId' target='_blank'>$profitRMB</a>";
			}
		else{
			$profitRMB="<div class='redB'>未设定</div>";
			}
	   */
	  if ($ViewSign==1){
		//订单总数
		$checkAllQty= mysql_query("SELECT SUM(ALLQTY) AS ALLQTY,count(*) AS Orders FROM( 
									SELECT SUM(S.Qty) AS AllQty FROM $DataIn.yw1_ordersheet S
									LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
									WHERE P.eCode LIKE (SELECT eCode FROM $DataIn.productdata WHERE ProductId='$ProductId') GROUP BY OrderPO
									)A",$link_id);
		$AllQtySum=toSpace(mysql_result($checkAllQty,0,"AllQty"));//下单总数量
		$Orders=mysql_result($checkAllQty,0,"Orders");					//下单总次数
		//已出货数量
		$checkShipQty= mysql_query("SELECT SUM(Qty) AS ShipQty,COUNT(*) AS ShipCount FROM $DataIn.ch1_shipsheet WHERE ProductId='$ProductId'",$link_id);
		if (mysql_num_rows($checkShipQty)>0){
			$ShipQtySum=toSpace(mysql_result($checkShipQty,0,"ShipQty"));
		    $Ships=mysql_result($checkShipQty,0,"ShipCount");
		}
	
		//准时交货次数
		/*
		$checkShipQty= mysql_query("
								   SELECT count(*) AS Ships 
								   FROM $DataIn.ch1_shipsheet 
								   LEFT JOIN $DataIn.
								   WHERE ProductId='$ProductId'",$link_id);
		*/
		
		//最后出货日期
		if($Months!=NULL){
			if($Months<6){//6个月内绿色
				$LastShipMonth="<div class='greenB'>".$LastMonth."</div>";
				}
			else{
				if($Months<12){//6－12个月：橙色
					$LastShipMonth="<div class='yellowB'>".$LastMonth."</div>";
					}
				else{//红色
					$LastShipMonth="<div class='redB'>".$LastMonth."</div>";
					}
				}
			
			}
		else{//没有出过货
			$LastShipMonth="&nbsp;";
			}
		//百分比
		$TempInfo="style='CURSOR: pointer;' onclick='ViewChart($ProductId,1)'";
		if ($AllQtySum!=0)
		{
			$TempPC=($ShipQtySum/$AllQtySum)*100;
		}
		
		$TempPC=$TempPC>=1?(round($TempPC)."%"):(sprintf("%.2f",$TempPC)."%");
		if($AllQtySum>0){
			$TempInfo.="title='订单总数:$AllQtySum,已出数量占:$TempPC'";
			}
			
		
			
		$GfileStr=$GfileStr==""?"&nbsp;":$GfileStr;
		$Weight=zerotospace($Weight);
		//退货数量
		$checkReturnedQty= mysql_query("SELECT SUM(Qty) AS ReturnedQty FROM $DataIn.product_returned WHERE eCode='$eCode'",$link_id);
		$ReturnedQty=toSpace(mysql_result($checkReturnedQty,0,"ReturnedQty"));
		
		
		if($ReturnedQty>0 && $ShipQtySum>0){
			//退货百分比
			$ReturnedPercent=sprintf("%.1f",(($ReturnedQty/$ShipQtySum)*1000));
			if($ReturnedPercent>=5){
				$ReturnedQty="<span class=\"redB\">".$ReturnedQty."</span>";
				}
			else{
					if($ReturnedPercent>=2){
						$ReturnedQty="<span class=\"yellowB\">".$ReturnedQty."</span>";
						}
					else{
						$ReturnedQty="<span class=\"greenB\">".$ReturnedQty."</span>";
						}
					}
			$ReturnedP=
			$TempInfo2="style='CURSOR: pointer;' onclick='ViewChart($ProductId,2)' title=\"退货率：$ReturnedPercent ‰\"";
			
			}
		else{
			$ReturnedQty="&nbsp;";
			$TempInfo2="";
			}
			$ShipQtySum="<span class='yellowB'>".$ShipQtySum."</span>";
		//高清图片检查
		if ($ProductId!=0){
				$checkImgSQL=mysql_query("SELECT Picture FROM $DataIn.productimg WHERE ProductId=$ProductId",$link_id);
				if($checkImgRow=mysql_fetch_array($checkImgSQL)){
					$Picture=$checkImgRow["Picture"];
					$f=anmaIn($Picture,$SinkOrder,$motherSTR);
						$ProductId="<a href='openorload.php?d=$d&f=$f&Type=product'>$ProductId</a>";
					}	
		}
	
		//出货数量和下单次数
		if($Orders>0){
		if($Orders<2){
			$ShipQtySum=$ShipQtySum."<span class=\"redB\">($Orders)</span>";
			}
		else{
			if($Orders>4){
				$ShipQtySum=$ShipQtySum."<span class=\"greenB\">($Orders)</span>";
				}
			else{
				$ShipQtySum=$ShipQtySum."<span class=\"yellowB\">($Orders)</span>";	
				}
			}
		}
		if($myTask==1){
			$ValueArray=array(
				array(0=>$Client),
				array(0=>$ProductId,			1=>"align='center'"),
				array(0=>$TestStandard,		2=>"onmousedown='window.event.cancelBubble=true;'"),
				array(0=>$eCode,				3=>"..."),
				array(0=>$MainWeight,		1=>"align='center'"),
				array(0=>$Weight,1=>"align='right'"),
				array(0=>$CaseReport,1=>"align='center'"),
				array(0=>$Price."&nbsp;", 	1=>"align='right'"),
				array(0=>$Symbol,			1=>"align='center'"),
				array(0=>$profitRMB,			1=>"align='center'"),
				array(0=>$PackingUnit,		1=>"align='center'"),
				array(0=>$ShipQtySum,		1=>"align='center'",2=>$TempInfo),
				array(0=>$ReturnedQty,		1=>"align='center'",2=>$TempInfo2),
				array(0=>$LastShipMonth,			1=>"align='center'"),
				array(0=>$pRemark,			1=>"align='center'"),
				array(0=>$CodeFile,			1=>"align='center'"),
				array(0=>$LableFile,		1=>"align='center'"),
				array(0=>$BoxFile,			1=>"align='center'"),
				array(0=>$TypeName),
				array(0=>$Img_H,			1=>"align='center'",	2=>"onmousedown='window.event.cancelBubble=true;'"),
				array(0=>$bjRemark,3=>"...")
				);
			}
		else{
			$ValueArray=array(
				array(0=>$Client),
				array(0=>$ProductId,			1=>"align='center'"),
				array(0=>$TestStandard,		2=>"onmousedown='window.event.cancelBubble=true;'"),
				array(0=>$eCode,				3=>"..."),
				array(0=>$MainWeight,		1=>"align='center'"),
				array(0=>$Weight,1=>"align='right'"),
				array(0=>$CaseReport,1=>"align='center'"),
				array(0=>$Price, 			1=>"align='right'"),
				array(0=>$Symbol,			1=>"align='center'"),				
				array(0=>$PackingUnit,		1=>"align='center'"),
				array(0=>$ShipQtySum."(".$Orders.")",		1=>"align='center'",2=>$TempInfo),
				array(0=>$ReturnedQty,		1=>"align='center'",2=>$TempInfo2),
				array(0=>$LastShipMonth,			1=>"align='center'"),
				array(0=>$pRemark,			1=>"align='center'"),
				array(0=>$CodeFile,			1=>"align='center'"),
				array(0=>$LableFile,		1=>"align='center'"),
				array(0=>$BoxFile,			1=>"align='center'"),
				array(0=>$TypeName),
				array(0=>$Img_H,			1=>"align='center'",	2=>"onmousedown='window.event.cancelBubble=true;'"),
				array(0=>$bjRemark,3=>"...")
				);
			}
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
	  }
	   else{
		$KillRecord+=1;  
	  }
	 }while ($myRow = mysql_fetch_array($myResult));
	}
if ($i==1){
	noRowInfo($tableWidth);
  	}
//步骤7：
include "../model/subprogram/ColorInfo.php";
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
$RecordToTal-=$KillRecord;
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>