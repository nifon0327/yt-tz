<style type="text/css">
<!--
.moveLtoR{ filter:revealTrans(Transition=6,Duration=0.3)}
.moveRtoL{ filter:revealTrans(Transition=7,Duration=0.3)}
/* 为 DIV 加阴影 */ 
.out {position:relative;background:#EEEEEE;margin:10px auto;}
.in {background:#FFFFFF;border:1px solid #555;padding:10px 5px;position:relative;top:-5px;left:-5px;}  
-->
</style>
<?php 
//电信---yang 20120801
//代码共享-EWEN 2012-08-19
include "../model/modelhead.php";
echo"<SCRIPT src='../model/pands.js' type=text/javascript></script>";
//步骤2：
ChangeWtitle("$SubCompany 更新BOM资料");//需处理
$nowWebPage =$funFrom."_update";	
//$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$ProductId=$ProductId==""?$Id:$ProductId;//重置
//$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,CompanyId,$CompanyId,ProductType,$ProductType,ProductId,$ProductId";
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,ProductType,$ProductType,ProductId,$ProductId";

//步骤3：
$tableWidth=1120;$tableMenuS=600;$ColsNumber=10;
$CustomFun="<span onClick='addPandStuffId(3)' $onClickCSS>加入配件</span>&nbsp;";//自定义功能
$CheckFormURL="thisPage";
include "../model/subprogram/sys_parameters.php";
$S_Result = mysql_query("SELECT A.Relation,A.Diecut,A.Cutrelation ,D.StuffId,D.StuffCname,D.Price,T.TypeName,M.Name,P.Forshort,P.Currency,R.Rate,S.uName AS bpName,S.Id AS bpId,T.MainType 
FROM $DataIn.pands A
LEFT JOIN $DataIn.stuffdata D ON A.StuffId=D.StuffId
LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
LEFT JOIN $DataIn.bps B ON D.StuffId=B.StuffId
LEFT JOIN $DataPublic.staffmain M ON B.BuyerId=M.Number
LEFT JOIN $DataIn.trade_object P ON B.CompanyId=P.CompanyId
LEFT JOIN $DataPublic.currencydata R ON R.Id=P.Currency
LEFT JOIN $DataPublic.standbyrate  S  ON S.Id=A.bpRate
WHERE A.ProductId=$ProductId ORDER BY A.Id",$link_id);
/*
echo "SELECT A.Relation,A.Diecut,A.Cutrelation ,D.StuffId,D.StuffCname,D.Price,T.TypeName,M.Name,P.Forshort,P.Currency,R.Rate,S.uName AS bpName,S.Id AS bpId
FROM $DataIn.pands A
LEFT JOIN $DataIn.stuffdata D ON A.StuffId=D.StuffId
LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
LEFT JOIN $DataIn.bps B ON D.StuffId=B.StuffId
LEFT JOIN $DataPublic.staffmain M ON B.BuyerId=M.Number
LEFT JOIN $DataIn.trade_object P ON B.CompanyId=P.CompanyId
LEFT JOIN $DataPublic.currencydata R ON R.Id=P.Currency
LEFT JOIN $DataPublic.standbyrate  S  ON S.Id=A.bpRate
WHERE A.ProductId=$ProductId ORDER BY A.Id";
*/
$P_Row = mysql_fetch_array(mysql_query("SELECT P.cName,(P.Price*R.Rate) AS saleAmount,p.CompanyId FROM $DataIn.productdata P LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId LEFT JOIN $DataIn.currencydata R ON R.Id=C.Currency WHERE P.ProductId=$ProductId LIMIT 1",$link_id));
$cName=$P_Row["cName"];
$saleAmount=sprintf("%.4f",$P_Row["saleAmount"]);//售价
$CompanyId=$P_Row["CompanyId"];
//成本计算
$cbRMB=0;$cbUSD=0;
$CostResult=mysql_query("SELECT A.Relation,D.CostPrice,D.Price,R.Rate,R.Id 
FROM $DataIn.pands A 
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=A.StuffId 
LEFT JOIN $DataIn.bps B ON B.StuffId=D.StuffId 
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId 
LEFT JOIN $DataPublic.currencydata R ON R.Id=P.Currency WHERE A.ProductId=$ProductId",$link_id);
if($CostRow= mysql_fetch_array($CostResult)){
	do{
		$Relation=$CostRow["Relation"];
		$OppositeQTY=explode("/",$Relation);//拆分对应数量
		$gRate=$CostRow["Rate"]==""?1:$CostRow["Rate"];	//汇率
		$CurrencyTemp=$CostRow["Id"];		//货币编码
		$sPrice=$CostRow["Price"];			//配件价格
		$CostPrice=$CostRow["CostPrice"];	
		/*if ($CostPrice>0){
			$sPrice=$CostPrice;
			$CurrencyTemp=1; 
			$gRate=1;
		}*/
		
		if($OppositeQTY[1]!=""){//非整数对应关系
			$thisRMB=$gRate*$sPrice*$OppositeQTY[0]/$OppositeQTY[1];
			}
		else{//整数对应关系
			$thisRMB=$gRate*$sPrice*$OppositeQTY[0];
			}
		if($CurrencyTemp!=2){   //不包括美元的
			$cbRMB+=$thisRMB;
			}
		else{
			$cbUSD+=$thisRMB;
			}
		}while ($CostRow= mysql_fetch_array($CostResult));
	$cbRMB=sprintf("%.4f",$cbRMB);
	$cbUSD=sprintf("%.4f",$cbUSD);
	$cbHZ=sprintf("%.4f",$cbRMB*$HzRate);
	}
$Maori=sprintf("%.4f",$saleAmount-$cbUSD-$cbRMB-$cbHZ);
//毛利计算：售价-总成本-RMB成本*7%
$SelectCode="成品 $cName 的配件清单<input name='HZ' type='hidden' id='HZ' value='$HzRate'>
<input name='ProductName' type='hidden' id='ProductName' value='$cName'>";
include "../model/subprogram/add_model_pt.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr bgcolor='<?php  echo $Title_bgcolor?>' >
		<td width="10" class="A0010" height="25">&nbsp;</td>
		<td height="25"  width='1040' class="A0111">
		<div style="width:100%;height:100%;overflow-x:hidden;overflow-y:none">
				<table cellpadding="0" width="100%" cellspacing="0" bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' >
                <tr >
                    <td width="70" height="25" class="A1001" align="center"> 操作</td>
                    <td width="50" class="A1001" align="center">序号</td>
                    <td width="90" class="A1001" align="center">类别</td>
                    <td width="50" class="A1001" align="center">配件ID</td>
                    <td width="310" class="A1001" align="center">配件名称</td>
                    <td width="80" class="A1001" align="center">对应数量</td>
                     <td width="70" class="A1001" align="center">备品率</td>
                    <td width="70" class="A1001" align="center">采购</td>
                    <td width="80 " class="A1000" align="center">供应商</td>
                    <td width="" class="A1100" align="center">关联配件</td>
                </tr>
			</table>
		</div>
		</td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr>
		<tr>
		<td width="10" class="A0010" height="25">&nbsp;</td>
		<td   height="336" class="A0111">
            <div style="width:100%;height:100%;overflow-x:hidden;overflow-y:scroll">
                <table cellpadding="0" width="100%" cellspacing="0" bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' id='ListTable'>
			<?php 
		if($S_Row=mysql_fetch_array($S_Result)) {//如果设定了产品配件关系
			$i=0;
			do{
				$StuffId=$S_Row["StuffId"];
				$Relation=$S_Row["Relation"];
				$Diecut=$S_Row["Diecut"];
				$Cutrelation=$S_Row["Cutrelation"];
				$bpName=$S_Row["bpName"];
				$bpId=$S_Row["bpId"];
				//$Diecut=$Diecut==""?"&nbsp;":$Diecut;
				$Cutrelation=$Cutrelation==0?"":$Cutrelation;
				$bpRate=$bpRate==0?"":$bpRate;
				$StuffCname=$S_Row["StuffCname"];
				include"../model/subprogram/stuff_Property.php";//配件属性
				$TypeName=$S_Row["TypeName"];
				$Name=$S_Row["Name"]==""?"&nbsp;":$S_Row["Name"];
				$Forshort=$S_Row["Forshort"]==""?"&nbsp;":$S_Row["Forshort"];
				$Currency=$S_Row["Currency"];
				$Price=$S_Row["Price"];
				$Rate=$S_Row["Rate"];
				$theAmount=sprintf("%.4f",$Price*$Rate);
				
				$MainType=$S_Row["MainType"];
				
				
				//关联配件
				$UniteStuffId="";
				$G_Reslut=mysql_query("SELECT uStuffId FROM $DataIn.pands_unite WHERE ProductId='$ProductId' AND StuffId='$StuffId' ",$link_id);
				while($G_Row=mysql_fetch_array($G_Reslut)){
					$UniteStuffId.=$UniteStuffId==""?$G_Row["uStuffId"]:",".$G_Row["uStuffId"];
				}
				
				$Numbers=$i+1;
				echo"<tr>
				<td align='center' class='A0101' width='70' height='25' onmousedown='window.event.cancelBubble=true;'>
				 <a href='#' onclick='deleteRow(this.parentNode)' title='删除当前行'>×</a>&nbsp;
				 <a href='#' onclick='upMove(this.parentNode)' title='当前行上移'>∧</a>&nbsp;
				 <a href='#' onclick='downMove(this.parentNode)' title='当前行下移'>∨</a>
				</td>
		   			<td align='center' class='A0101' width='50'>$Numbers</td>
		   			<td class='A0101' width='90'>$TypeName</td>
				   <td class='A0101' width='50'>$StuffId<input type='hidden' name='StuffId[]' id='StuffId$i' value='$StuffId'></td>
				   <td class='A0101' width='310'>$StuffCname</td>
				   <td class='A0101' align='center' width='80'>
				   <input name='Qty[]' type='text' id='Qty$i' size='8' value='$Relation' onchange='checkNum(this)' onfocus='toTempValue(this.value)' $disabledSign>
				   <input name='Fb[]' type='hidden' id='Fb$i' value='$Currency'>
				   <input name='sPrice[]' type='hidden' class='noLine' id='sPrice$i' value='$Price'>
				   </td>
				   </td>
				   <td class='A0101' align='center' width='70'>
				    <input name='bpRateName[]'  id='bpRateName$i'  type='text'  size='7' value='$bpName' onclick='addbpRate(this,$i)'><input type='hidden' name='bpRate[]' id='bpRate$i' value='$bpId' >
					</td>
				   <td class='A0101' align='center' width='70'>$Name</td>
				   <td class='A0101' width='80'>$Forshort</td>
				   <td class='A0100' align='center' width=' '>
					  <input name='Unite[]' type='text' id='Unite$i' size='26' value='$UniteStuffId'  onclick='updateJq(this,$i,1)'  readonly>
				   </td>
				  </tr>";
		  		$i++;
				}while ($S_Row=mysql_fetch_array($S_Result));
			}
			$Rows=$i-1;
			?>
			</table>
		</div>
		</td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr>
	<tr>
	  <td class="A0010">&nbsp;</td>
	  <td height="35"  class="A0111">
	  <table  cellpadding="0" width="100%" cellspacing="0" bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	 	<tr>
	 	  <td>售价</td>
	 	  <td>-USD含税成本</td>
	 	  <td>-RMB含税成本</td>
	 	  <td>-行政费用</td>
	 	  <td>=毛利</td>
	 	  <td>&nbsp;</td>
	 	</tr>
	  	<tr>
	  	  <td><input name="saleAmount" type="text" id="saleAmount" value="<?php  echo $saleAmount?>" size="20" readonly></td>
	  	  <td><input name="cbUSD" type="text" id="cbUSD" value="<?php  echo $cbUSD?>" size="20" readonly></td>
	  	  <td><input name="cbRMB" type="text" id="cbRMB" value="<?php  echo $cbRMB?>" size="20" readonly></td>
	  	  <td><input name="cbHZ" type="text" id="cbHZ" value="<?php  echo $cbHZ?>" size="20" readonly></td>
		  <td><input name="Maori" type="text" id="Maori" value="<?php  echo $Maori?>" size="20" readonly></td>
		   <td align='center'><input name="graphic" type="button" id="graphic" value="生成流程图" onclick="createBomflow()"></td>
	  	</tr>
	  </table>
	  </td>
	  <td class="A0001">&nbsp;</td>
  </tr>
</table>
<input name="TempValue" type="hidden" id="TempValue" value='1'><input name="SIdList" type="hidden" id="SIdList">
<input name="CompanyId" type="hidden" id="CompanyId" value="<?php echo $CompanyId?>"  >
<input name="RelationMainType" type="hidden" id="RelationMainType" value="<?php  echo $APP_CONFIG['PANDS_RELATION_MAINTYPE']?>">
<?php 
echo"<div id='Jp' style='position:absolute;width:400px; height:50px;z-index:1;visibility:hidden;' tabIndex=0><input name='ActionTableId' type='hidden' id='ActionTableId'><input name='ActionRowId' type='hidden' id='ActionRowId'><input name='ObjId' type='hidden' id='ObjId'>
			<div class='in' id='infoShow'>
			</div>
	</div>";
//步骤5：
include "../model/subprogram/add_model_p.php";
?>
