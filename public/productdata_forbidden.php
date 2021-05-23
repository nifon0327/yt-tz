<style type="text/css">
.moveLtoR{ filter:revealTrans(Transition=6,Duration=0.3)};
.moveRtoL{ filter:revealTrans(Transition=7,Duration=0.3)};
/* 为 DIV 加阴影 */ 
.out {position:relative;background:#006633;margin:10px auto;width:400px;}
.in {background:#FFFFE6;border:1px solid #555;padding:10px 5px;position:relative;top:-5px;left:-5px;}  
/* 为 图片 加阴影 */ 
.imgShadow {position:relative;     background:#bbb;      margin:10px auto;     width:220px; } 
.imgContainer {position:relative;      top:-5px;     left:-5px;     background:#fff;      border:1px solid #555;     padding:0; } 
.imgContainer img {     display:block; } 
.glow1 { filter:glow(color=#FF0000,strengh=2)}
</style>
<?php 
/*
分开已更新
*/
//步骤1电信---yang 20120801
include "../model/modelhead.php";
include "../model/subprogram/UpdateCode.php"; //更新条码 add by zx 20100701
include "../model/subprogram/business_authority.php";//看客户权限
//步骤2：需处理
$ColsNumber=27;
$tableMenuS=850;
ChangeWtitle("$SubCompany 半年未下单产品禁用列表");
$funFrom="productdata";
$From=$From==""?"forbidden":$From;
	$Th_Col="选项|40|序号|35|客户|80|产品ID|50|中文名|200|&nbsp;|30|Product Code|160|QC图|50|客户<br>授权书|50|单品重<br>(g)|50|成品重<br>(g)|40|Price|60|理论净利|100|装箱<br>单位|40|外箱<br>条码|30|已出<br>(下单次数)|60|退货|50|最后下单<br>月份|50|状态|30|交货<br>均期|50|产品<br>备注|50|背卡<br>条码|30|PE袋<br>条码|30|白盒<br>坑盒|30|外箱<br>标签|30|属电<br>子类|30|认证下载|200|所属分类|100|高清|35|报价规则|350";
include "../model/subprogram/sys_parameters.php";
//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
//步骤3：
$Keys=31;
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-可选条件下拉框
if($From!="slist"){
	$SearchRows="";
	/*echo "<select name='CompanyId' id='CompanyId' onchange='ResetPage(this.name)'>";
	$result = mysql_query("SELECT M.CompanyId,M.Forshort FROM $DataIn.trade_object M 
WHERE M.cSign=$Login_cSign AND M.Estate=1 $ClientStr  ORDER BY M.Id",$link_id);
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
	$result = mysql_query("SELECT P.TypeId,T.TypeName,T.Letter,C.Color 
	FROM $DataIn.productdata P
	LEFT JOIN $DataIn.ProductType T ON T.TypeId=P.TypeId
	LEFT JOIN $DataIn.productmaintype C ON C.Id=T.mainType
	WHERE T.Estate=1 $SearchRows GROUP BY P.TypeId ORDER BY T.mainType DESC,T.Letter",$link_id);
	echo "<option value='' selected>全部</option>";
	while ($myrow = mysql_fetch_array($result)){
			$TypeId=$myrow["TypeId"];
			$Color=$myrow["Color"]==""?"#FFFFFF":$myrow["Color"];
			if ($ProductType==$TypeId){
				echo "<option value='$TypeId' style= 'color: $Color;font-weight: bold' selected>$myrow[Letter]-$myrow[TypeName]</option>";
				}
			else{
				echo "<option value='$TypeId' style= 'color: $Color;font-weight: bold'>$myrow[Letter]-$myrow[TypeName]</option>";
				}
			} 
		echo"</select>&nbsp;";
	$TypeIdSTR=$ProductType==""?"":" AND P.TypeId=".$ProductType;
	$SearchRows.=$TypeIdSTR;*/
	
	/*$TempProfitSTR="ProfitTypeStr".strval($ProfitType); 
	$$TempProfitSTR="selected";
	echo"<select name='ProfitType' id='ProfitType' onchange='ResetPage(this.name)'>";
		echo"<option value='' $ProfitTypeStr>全部净利</option>
		<option value='1' style= 'color:#FF00CC;' $ProfitTypeStr1>0以下</option>
		<option value='2' style= 'color:#FF0000;' $ProfitTypeStr2>0-7%</option>
		<option value='3' style= 'color:#FF6633;' $ProfitTypeStr3>8-15%</option>
		<option value='4' style= 'color:#009900;' $ProfitTypeStr4>16%以上</option>
		<option value='5' style= 'color:#BB0000;' $ProfitTypeStr5>未设定</option>
	</select>&nbsp;";*/
	$LastM=$LastM==""?2:$LastM;
	$TempProfitSTR="LastMStr".strval($LastM); 
	$$TempProfitSTR="selected";
	echo"<select name='LastM' id='LastM' onchange='ResetPage(this.name)'>
		<option value='1' style= 'color:#f60;' $LastMStr1>半年至1年</option>
		<option value='2' style= 'color:#f00;' $LastMStr2>1年以上</option>
	</select>&nbsp;";
	switch($LastM){
		case 1://半年
			$ShipMonthStr=" AND E.Months>5 AND E.Months<12 AND E.Months IS NOT NULL";
		break;
		case 2://一年以上
			$ShipMonthStr=" AND E.Months>11 AND E.Months IS NOT NULL";
		break;
		}
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
$NowYear=date("Y");
$NowMonth=date("m");
//增加快带查询Search按钮
$searchtable="productdata|P|cName|0"; //快速搜索的表名，字段名. 表名|别名|字段|1  1表示带Estate字段,其它值无无
include "../model/subprogram/QuickSearch.php";

//步骤5：
include "../model/subprogram/read_model_5.php";
include "../model/subprogram/CurrencyList.php";
echo"<div id='Jp' style='position:absolute; left:1020px; top:229px; width:480px; height:50px;z-index:1;visibility:hidden;' tabIndex=0><input name='ActionTableId' type='hidden' id='ActionTableId'><input name='ActionRowId' type='hidden' id='ActionRowId'><input name='ObjId' type='hidden' id='ObjId'>
		<div class='out'>
			<div class='in' id='infoShow'>
			</div>
		</div>
	</div>";

//步骤6：需处理数据记录处理
$i=1;$KillRecord=0;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql= "SELECT P.Id,P.ProductId,P.cName,P.eCode,P.Price,P.Unit,P.Moq,P.Weight,P.CompanyId,P.Description,P.Remark,P.pRemark,P.bjRemark,P.dzSign,P.TestStandard,P.Img_H,P.Date,P.PackingUnit,P.Estate,P.Locks,P.Code,P.Operator,
T.TypeName,C.Forshort,D.Rate,D.Symbol,D.PreChar,P.MainWeight,E.Months,E.LastMonth
	FROM $DataIn.productdata P
	LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
	LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
	LEFT JOIN (
			    SELECT DATE_FORMAT(MAX(M.OrderDate),'%Y-%m') AS LastMonth,TIMESTAMPDIFF(MONTH,MAX(M.OrderDate),now()) AS Months,S.ProductId            
                FROM $DataIn.yw1_ordermain M 
	            LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
                LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
                WHERE 1 AND P.Estate=1 GROUP BY S.ProductId ORDER BY M.OrderDate DESC
	    ) E ON E.ProductId=P.ProductId
	WHERE 1 AND P.Estate>0 $SearchRows  $ShipMonthStr ORDER BY Estate DESC,Id DESC";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d=anmaIn("download/productfile/",$SinkOrder,$motherSTR);	
	$dirforstuff=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		$Months=$myRow["Months"];
		$LastMonth=$myRow["LastMonth"];
		$Id=$myRow["Id"];		
		$ProductId=$myRow["ProductId"];
		$Rate=$myRow["Rate"];
		$Symbol=$myRow["Symbol"];
		$Client=$myRow["Forshort"];
		$cName=$myRow["cName"];
		$PreChar=$myRow["PreChar"];
		$eCode=$myRow["eCode"]==""?"&nbsp;":$myRow["eCode"];
		$Remark=trim($myRow["Remark"])==""?"&nbsp;":"<img src='../images/remark.gif' title='$myRow[Remark]' width='18' height='18'>";
		$pRemark=trim($myRow["pRemark"])==""?"&nbsp;":"<img src='../images/remark.gif' title='$myRow[pRemark]' width='18' height='18'>";
		$bjRemark=trim($myRow["bjRemark"])==""?"&nbsp;":$myRow["bjRemark"];
		$Description=$myRow["Description"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$myRow[Description]' width='18' height='18'>";
		$Price=$myRow["Price"];
		$Moq=$myRow["Moq"]==0?"&nbsp;":$myRow["Moq"];
		$Weight=$myRow["Weight"]==0?"&nbsp;":$myRow["Weight"];
		$MainWeight=$myRow["MainWeight"]==0?"&nbsp;":$myRow["MainWeight"];
		$TestStandard=$myRow["TestStandard"];
		include "../admin/Productimage/getProductImage.php";
		$Img_H=$myRow["Img_H"]; 
		if($Img_H>0){
			$I_FilePath="download/teststandard/";
			$I_Field="T".$ProductId."_".'H'.".zip";
			$I_Field=anmaIn($I_Field,$SinkOrder,$motherSTR);
			$I_td=anmaIn("$I_FilePath",$SinkOrder,$motherSTR);
			$Img_H="<a href=\"../admin/openorload.php?d=$I_td&f=$I_Field&Type=&Action=6\" target=\"download\">H</a>";
			}
		else{
			$Img_H="&nbsp;";
			
			}		
			
		 //产品QC检验标准图
         $QCImage="";
         include "../admin/subprogram/product_qcfile.php";
         $QCImage=$QCImage==""?"&nbsp;":$QCImage;
         //  客户授权书
         include "../model/subprogram/product_clientproxy.php";
		//认证下载
		$CerSql="SELECT Picture,Remark FROM $DataIn.product_certification WHERE ProductId='$ProductId'";
		$CerResult=mysql_query($CerSql,$link_id);
		$CerImg="&nbsp;";
		$CerPicture=array();
		$index=0;
		$Cer_FilePath="download/productcer/";
		if($CerRow = mysql_fetch_array($CerResult)){
			do{
				$CerPicture[$index]=$CerRow["Picture"];
				$CerRemark[$index]=$CerRow["Remark"];
				$Cer_Field=anmaIn($CerPicture[$index],$SinkOrder,$motherSTR);
				$Cer_td=anmaIn("$Cer_FilePath",$SinkOrder,$motherSTR);
				$CerDownload="<a href=\"../admin/openorload.php?d=$Cer_td&f=$Cer_Field&Type=&Action=6\" target=\"download\">$CerRemark[$index]</a>";
				$CerImg.=$CerDownload."</br>";
				$index++;
			}while($CerRow = mysql_fetch_array($CerResult));
		}
		//include "subprogram/product_teststandard.php";
		$Code=$myRow["Code"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$myRow[Code]' width='18' height='18'>";
		$Estate=$myRow["Estate"];
		switch($Estate){
			case 1:$Estate="<div class='greenB'>√</div>";	break;
			case 2:$Estate="<div class='yellowB'>√.</div>";	break;
			default:$Estate="<div class='redB'>×</div>";	break;
			}
		$PackingUnit=$myRow["PackingUnit"];
		$uResult = mysql_query("SELECT Name FROM $DataPublic.packingunit WHERE Id=$PackingUnit order by Id Limit 1",$link_id);
		if($uRow = mysql_fetch_array($uResult)){
			$PackingUnit=$uRow["Name"];
			}			
		$Unit=$myRow["Unit"];
		$Date=$myRow["Date"];
		$Locks=$myRow["Locks"];
		$dzSign=$myRow["dzSign"]=="1"?"√":"&nbsp;";
		//操作员姓名
		$Operator=$myRow["Operator"];
		include"../model/subprogram/staffname.php";
		$thisCId=$myRow["CompanyId"];		
		$TypeName=$myRow["TypeName"];
		$saleRMB=sprintf("%.2f",$Price*$Rate);//产品销售RMB价格
		$GfileStr="";
		$StuffResult = mysql_query("SELECT A.Relation,B.Price,E.Rate,D.Currency
		FROM $DataIn.pands A
		LEFT JOIN $DataIn.stuffdata B ON B.StuffId=A.StuffId
		LEFT JOIN $DataIn.bps C ON C.StuffId=B.StuffId
		LEFT JOIN $DataIn.trade_object D ON D.CompanyId=C.CompanyId		
		LEFT JOIN $DataPublic.currencydata E ON E.Id=D.Currency		
		where A.ProductId=$ProductId order by A.Id",$link_id);
		if($StuffmyRow=mysql_fetch_array($StuffResult)) {//如果设定了产品配件关系
			$buyRMB=0;$buyHZsum=0;
			do{	
				$stuffPrice=$StuffmyRow["Price"];
				$stuffRelation=$StuffmyRow["Relation"];
				$stuffRate=$StuffmyRow["Rate"];//加密
				$CurrencyTemp=$StuffmyRow["Currency"];
				//成本
				$OppositeRelation=explode("/",$stuffRelation);
				if ($OppositeRelation[1]!=""){//非整数对应关系
					$thisRMB=sprintf("%.4f",$stuffRate*$stuffPrice*$OppositeRelation[0]/$OppositeRelation[1]);
					}
				else{//整数对应关系
					$thisRMB=sprintf("%.4f",$stuffRate*$stuffPrice*$OppositeRelation[0]);
					}
				$buyRMB=$buyRMB+$thisRMB;	//总成本
				if($CurrencyTemp!=2){		//非外购
					$buyHZsum+=$thisRMB;
					}
				}while($StuffmyRow=mysql_fetch_array($StuffResult));
			$profitRMB=sprintf("%.2f",$saleRMB-$buyRMB-$buyHZsum*$HzRate);
			if($saleRMB != 0)
			{
				$profitRMBPC=sprintf("%.0f",($profitRMB*100/$saleRMB));
			}
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
			/*$profitRMB=$profitRMBPC>15?"<span class='greenB'>$profitRMB($profitRMBPC%)</sapn>":($profitRMBPC>7?"<span class='yellowB'>$profitRMB($profitRMBPC%)</sapn>":($profitRMB<0?"<span class='purpleB'>$profitRMB($profitRMBPC%)</sapn>":"<span class='redB'>$profitRMB($profitRMBPC%)</sapn>"));
			$profitRMB="<a href='pands_profit.php?From=task&Cid=$ProductId' target='_blank'>$profitRMB</a>";
			}
		else{
			$profitRMB="<div class='redB'>未设定</div>";
			}
		 */
	  if ($ViewSign==1){
		//交货期
		include "../model/subprogram/product_chjq.php";
		//订单总数
		$checkAllQty= mysql_query("
								  SELECT SUM(ALLQTY) AS ALLQTY,count(*) AS Orders FROM( 
									SELECT SUM(S.Qty) AS AllQty FROM $DataIn.yw1_ordersheet S
									LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
									WHERE P.eCode LIKE (SELECT eCode FROM $DataIn.productdata WHERE ProductId='$ProductId') GROUP BY OrderPO
									)A
								  ",$link_id);
		$AllQtySum=toSpace(mysql_result($checkAllQty,0,"AllQty"));
		$Orders=mysql_result($checkAllQty,0,"Orders");
		//已出货数量
		$checkShipQty= mysql_query("SELECT SUM(Qty) AS ShipQty FROM $DataIn.ch1_shipsheet WHERE ProductId='$ProductId'",$link_id);
		$ShipQtySum=toSpace(mysql_result($checkShipQty,0,"ShipQty"));
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
		$TempPC=$AllQtySum==0?0:($ShipQtySum/$AllQtySum)*100;
		$TempPC=$TempPC>=1?(round($TempPC)."%"):(sprintf("%.2f",$TempPC)."%");
		if($AllQtySum>0){
			$TempInfo.="title='订单总数:$AllQtySum,已出数量占:$TempPC'";
			}
//退货数量
		$checkReturnedQty= mysql_query("SELECT SUM(Qty) AS ReturnedQty FROM $DataIn.product_returned WHERE ProductId='$ProductId'",$link_id);
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
			//$ShipQtySum="<span class='yellowB'>".$ShipQtySum."</span>";
		//高清图片检查		
		$checkImgSQL=mysql_query("SELECT Picture FROM $DataIn.productimg WHERE ProductId=$ProductId",$link_id);
		if($checkImgRow=mysql_fetch_array($checkImgSQL)){
			$Picture=$checkImgRow["Picture"];
			$f=anmaIn($Picture,$SinkOrder,$motherSTR);
				$ProductId="<a href='openorload.php?d=$d&f=$f&Type=product'>$ProductId</a>";
			}	
		$ShipQtySum="<span class='yellowB'>".$ShipQtySum."</span>";
		$GfileStr=$GfileStr==""?"&nbsp;":$GfileStr;
		$TableId="ListTable$i";
		
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

			$ValueArray=array(
				array(0=>$Client),
				array(0=>$ProductId,			1=>"align='center'"),
				array(0=>$TestStandard,		2=>"onmousedown='window.event.cancelBubble=true;'" ,3=>"line"),
				array(0=>$CaseReport,1=>"align='center'"),
				array(0=>$eCode,				3=>"..."),
				array(0=>$QCImage,		1=>"align='center'"),
                array(0=>$clientproxy,		1=>"align='center'"),
				array(0=>$MainWeight,		1=>"align='center'"),
				array(0=>$Weight,		1=>"align='center'"),
				array(0=>$PreChar.$Price."&nbsp;", 	1=>"align='right'"),
				//array(0=>$Symbol,			1=>"align='center'"),
				array(0=>$profitRMB,			1=>"align='center'"),
				array(0=>$PackingUnit,		1=>"align='center'"),
				array(0=>$Code,				1=>"align='center'"),				
				array(0=>$ShipQtySum,		1=>"align='center'",2=>$TempInfo),
				array(0=>$ReturnedQty,		1=>"align='center'",2=>$TempInfo2),
				array(0=>$LastShipMonth,			1=>"align='center'"),
				array(0=>$Estate,			1=>"align='center'"),
				array(0=>$JqAvg,			1=>"align='center'"),
				array(0=>$pRemark,			1=>"align='center'"),
				array(0=>$CodeFile,			1=>"align='center'"),
				array(0=>$LableFile,		1=>"align='center'"),
				array(0=>$WhiteFile,			1=>"align='center'"),
				array(0=>$BoxFile,			1=>"align='center'"),
				array(0=>$dzSign,			1=>"align='center'"),
				array(0=>$CerImg,			1=>"align='center'"),
				array(0=>$TypeName),
				array(0=>$Img_H,			1=>"align='center'",	2=>"onmousedown='window.event.cancelBubble=true;'"),
				array(0=>$bjRemark)
				);
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
$ActioToS="1,6";				
include "../model/subprogram/read_model_menu.php";
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