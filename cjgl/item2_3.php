<link href="css/keyboard.css" rel="stylesheet" type="text/css" />
<?php
//电信-zxq 2012-08-01
$path = $_SERVER["DOCUMENT_ROOT"];
include_once($path.'/FactoryCheck/CheckSkip.php');
$Th_Col="配件|55|序号|30|选项|30|客户名称|100|业务单号|150|产品名称|180|送货单号|120|生产日期|90|需求单流水号|150|历史<br>订单|40|未收数量|60|收货确认|60|品管备注|65|操作/审核|60";
//$Th_Col="配件|55|ID|30|选项|30|客户|80|业务单号|100|产品中文名|80|送货单号|100|送货单日期|75|需求单流水号|90|配件ID|50|配件名称|250|历史<br>订单|40|检讨<br>报告|40|QC图|40|REACH|50|单位|30|品检<BR>类型|40|采购总数|60|未收数量|60|收货确认|60|品管备注|65|审核操作|60";
$Field=explode("|",$Th_Col);
$Count=count($Field);
$Cols = $Count/2;
$SearchRows=" AND S.Estate=2";//收货状态：已到货审核，品质审核中
//供应商过滤
$GysList="";
$nowInfo="当前:质检审核";
$GysResult= mysql_query("
SELECT M.CompanyId,P.Forshort 
	FROM $DataIn.gys_shsheet S
	LEFT JOIN $DataIn.gys_shmain M ON M.Id=S.Mid
	LEFT JOIN $DataIn.trade_object P ON M.CompanyId=P.CompanyId
	WHERE 1 $SearchRows GROUP BY M.CompanyId ORDER BY M.CompanyId",$link_id);
if ($GysRow = mysql_fetch_array($GysResult)){
	$GysList="<select name='GysId' id='GysId'  onChange='ResetPage(0,2)'>";//BillNumber重置
	$i=1;
	do{
		$theGysId=$GysRow["CompanyId"];
		$theForshort=$GysRow["Forshort"];
		$GysId=$GysId==""?$theGysId:$GysId;
		if($GysId==$theGysId){
			$GysList.="<option value='$theGysId' selected>$i 、$theForshort</option>";
			$SearchRows.=" AND M.CompanyId='$theGysId'";
			$nowInfo.=" - ".$theForshort;
			}
		else{
			$GysList.="<option value='$theGysId'>$i 、$theForshort</option>";
			}
		$i++;
		}while($GysRow = mysql_fetch_array($GysResult));
		$GysList.="</select>";
	}
//送货单过滤
$checkNumSql = mysql_query("SELECT M.BillNumber,M.GysNumber
    FROM $DataIn.gys_shmain M 
	LEFT JOIN $DataIn.gys_shsheet S ON S.Mid=M.Id
	WHERE 1 $SearchRows GROUP BY S.Mid ORDER BY M.BillNumber DESC",$link_id);
if($checkNumRow = mysql_fetch_array($checkNumSql)){
	$BillNumStr="<select name='BillNumber' id='BillNumber' onchange='ResetPage(1,2)'>";
    $BillNumStr.="<option value='' selected>全部送货单</option>";
	do{
		$theBillNumber=$checkNumRow["BillNumber"];
		$tempBillNumber =  substr($theBillNumber, 4, 8);
		$theGysNumber =$checkNumRow["GysNumber"];

		if($theBillNumber==$BillNumber){
			$BillNumStr.="<option value='$theBillNumber' selected>$theBillNumber</option>";
			$SearchRows.=" AND M.BillNumber='$theBillNumber'";
			}
		else{
			$BillNumStr.="<option value='$theBillNumber'>$theBillNumber</option>";
			}
		$i++;
		}while($checkNumRow = mysql_fetch_array($checkNumSql));
	$BillNumStr.="</select>&nbsp;";
	}


$StuffId=$SignS==0?"":$StuffId;
$quickCheck="";
  // 日期
$mysql = "SELECT DATE_FORMAT(SC.scDate,'%Y-%m-%d') as scDate
FROM gys_shsheet S 
LEFT JOIN gys_shmain M ON S.Mid = M.Id 
LEFT JOIN cg1_stocksheet G ON G.StockId = S.StockId 
LEFT JOIN yw1_ordersheet Y ON Y.POrderId = G.POrderId 
LEFT JOIN yw1_scsheet SC ON Y.POrderId = SC.POrderId 
LEFT JOIN yw1_ordermain OM ON OM.OrderNumber = Y.OrderNumber 
LEFT JOIN productdata P ON P.ProductId = Y.ProductId 
LEFT JOIN trade_object O ON O.CompanyId = OM.CompanyId 
LEFT JOIN stuffdata D ON D.StuffId = S.StuffId 
LEFT JOIN stuffunit U ON U.Id = D.Unit 
LEFT JOIN stufftype T ON T.TypeId = D.TypeId 
WHERE 1 $SearchRows and SC.scDate is not null GROUP BY SC.scDate ORDER BY SC.scDate DESC";
//echo $mysql;
$dataResult = mysql_query($mysql,$link_id);
if ($dataRow = mysql_fetch_array($dataResult)){
    $dataList="<select name='scDate' id='scDate'  onChange='ResetPage(1,3)'>";
    do{
        $theScDate=$dataRow["scDate"];
        $scDate=$scDate==""?$theScDate:$scDate;
        if($scDate==$theScDate){
            $dataList.="<option value='$theScDate' selected>$theScDate</option>";
            $SearchRows.=" AND DATE_FORMAT(SC.scDate,'%Y-%m-%d')='$theScDate'";
        }
        else{
            $dataList.="<option value='$theScDate'>$theScDate</option>";
        }
        $i++;
    }while($dataRow = mysql_fetch_array($dataResult));
    $dataList.="</select>";
}

	//增加业务单号下拉筛选
	$OrderPOList="";
	$clientResult = mysql_query("
	        SELECT Y.OrderPO
	        FROM $DataIn.gys_shsheet S
		    LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id
		    LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId
		    LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = G.POrderId
	        LEFT JOIN yw1_scsheet SC ON Y.POrderId = SC.POrderId 
WHERE 1  $SearchRows and Y.OrderPO is not null GROUP BY Y.OrderPO order by Y.OrderPO
	        ",$link_id);
	if($clientRow = mysql_fetch_array($clientResult)) {
	    $OrderPOList .= "<select name='OrderPO' id='OrderPO' onchange='ResetPage(1,2)'>";
	    $OrderPOList .= "<option value='all' selected>全部PO</option>";
	    do{
	        $thisOrderPO=$clientRow["OrderPO"];
	        $OrderPO=$OrderPO==""?$thisOrderPO:$OrderPO;
	        if($OrderPO==$thisOrderPO){
	            $OrderPOList .= "<option value='$thisOrderPO' selected>$thisOrderPO</option>";
	            $SearchRows.=" and Y.OrderPO='$thisOrderPO' ";
	        }
	        else{
	            $OrderPOList .= "<option value='$thisOrderPO'>$thisOrderPO</option>";
	        }
	    }while ($clientRow = mysql_fetch_array($clientResult));
	    $OrderPOList .= "</select>&nbsp;";
	}


//增加客户下拉筛选
$ForshortList="";
$ForshortResult = mysql_query("SELECT O.Forshort 
		FROM $DataIn.gys_shsheet S
		LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id
		LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId
		LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = G.POrderId
        LEFT JOIN yw1_scsheet SC ON Y.POrderId = SC.POrderId 
LEFT JOIN $DataIn.yw1_ordermain OM ON OM.OrderNumber = Y.OrderNumber
        LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
        LEFT JOIN $DataIn.trade_object O ON O.CompanyId = OM.CompanyId
		LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
		LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit
        LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
		WHERE 1 $SearchRows GROUP BY O.Forshort" ,$link_id);

if($ForshortRow = mysql_fetch_array($ForshortResult)) {
    $ForshortList .= "<select name='khCompanyId' id='khCompanyId' onchange='ResetPage(1,2)'>";
    $ForshortList .= "<option value='' selected>全部客户</option>";
    do{
        $thisForshort=$ForshortRow["Forshort"];
        //$khCompanyId=$khCompanyId==""?$thisForshort:$khCompanyId;
        if ($thisForshort == ""){

        }elseif($khCompanyId==$thisForshort){
            $ForshortList .= "<option value='$thisForshort' selected>$thisForshort</option>";
            $SearchRows.=" and O.Forshort='$thisForshort' ";
        }
        else{
            $ForshortList .= "<option value='$thisForshort'>$thisForshort</option>";
        }
    }while ($ForshortRow = mysql_fetch_array($ForshortResult));
    $ForshortList .= "</select>&nbsp;";
}

if ($GysList || $BillNumStr || $OrderPOList || $ForshortList) {
    //$toExcel = '<span class="ButtonH_25" onclick="toExcelAll($SearchRowes,$SearchRows)">导出送货单汇总</span>';
    $toExcel = '<a class="ButtonH_25" href="./item2_3_excel.php?SearchRows='.$SearchRows.'" target="_blank">导出审核汇总</a>';
}

//步骤5：
include '../basic/loading.php';
echo"<table  border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr>
	<td colspan='".($Cols-5)."' height='40px' class=''>$GysList $BillNumStr $dataList $OrderPOList $ForshortList $quickCheck  &nbsp;&nbsp;$toExcel</td>
    <td colspan='3' align='center' class=''><span class='ButtonH_25'  id='checkBtn' value='全选'  onclick='All_elects()' >全选</span><span class='ButtonH_25'  id='checkBtn' value='审核通过'  onclick='batchCheck(this)' >审核通过</span></td>
	<td colspan='3' align='right' class=''><input name='NowInfo' type='text' id='NowInfo' value='$nowInfo' class='text' disabled></td></tr>";
	//输出表格标题
	for($i=0;$i<$Count;$i=$i+2){
		$Class_Temp=$i==0?"A1111":"A1101";
		$j=$i;
		$k=$j+1;
		echo"<td width='$Field[$k]' class='' height='25px' style='background-color: #F0F5F8'><div align='center'>$Field[$j]</div></td>";
		}
	echo"</tr>";
$DefaultBgColor=$theDefaultColor;
$i=1;
$mySql="SELECT DISTINCT O.Forshort,M.CompanyId,M.BillNumber,G.POrderId,Y.OrderPO,P.cName,
		S.Id,S.StockId,S.Qty,S.StuffId,S.SendSign,D.StuffCname,D.Picture,D.CheckSign,T.AQL,(G.AddQty+G.FactualQty) AS cgQty,DATE_FORMAT(SC.scDate, '%Y-%m-%d') as `Date`,D.TypeId ,U.Name AS UnitName
		FROM $DataIn.gys_shsheet S
		LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id
		LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId
		LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = G.POrderId
        LEFT JOIN yw1_scsheet SC ON Y.POrderId = SC.POrderId 
LEFT JOIN $DataIn.yw1_ordermain OM ON OM.OrderNumber = Y.OrderNumber
        LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
        LEFT JOIN $DataIn.trade_object O ON O.CompanyId = OM.CompanyId
		LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
		LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit
        LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
		WHERE 1 $SearchRows ORDER BY S.Id";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
	do{	//有图片才可以审核
		$czSign=1;
		$AskDay="";
		$thisBuyRMB=0;
		$OrderSignColor="bgColor='#FFFFFF'";
		$theDefaultColor=$DefaultBgColor;
         $UnitName=$myRow["UnitName"];
		$Id=$myRow["Id"];					//记录ID
        $Forshort=$myRow['Forshort'];
		$Date=$myRow["Date"];				//送货单生成日期
        /******************验厂过滤*******************
        $groupLeaderSql = "SELECT GroupLeader From $DataIn.staffgroup WHERE GroupId = 604 ";
        $groupLeaderResult = mysql_query($groupLeaderSql);
        $groupLeaderRow = mysql_fetch_assoc($groupLeaderResult);
        $Leader = $groupLeaderRow['GroupLeader'];
        $skip = false;
        if($FactoryCheck == 'on' and skipData($Leader, $Date, $DataIn, $DataPublic, $link_id)){
          continue;
        }else if($FactoryCheck == 'on'){
          $Date = substr($Date, 0, 10);
        }
         * */
        /***************************************/
		$StockId=$myRow["StockId"];			//配件需求流水号
		$StuffId=$myRow["StuffId"];			//配件ID
        $TypeId=$myRow["TypeId"];    //配件类型
		$StuffCname=$myRow["StuffCname"];	//配件名称
        $CheckSign=$myRow["CheckSign"];   //品检要求：0－抽检，1－全检
		$cgQty=$myRow["cgQty"];				//采购总数
		$Qty=$myRow["Qty"];					//供应商送货数量
		$Picture=$myRow["Picture"];			//配件图片
		$AQL=$myRow["AQL"];

		$checkComboxRow = mysql_fetch_array(mysql_query("SELECT OrderQty FROM $DataIn.cg1_stuffcombox  WHERE StockId = $StockId",$link_id));
		$comboxOrderQty = $checkComboxRow["OrderQty"];
		if($comboxOrderQty>0){
			$cgQty = $comboxOrderQty;
		}



		if($Picture==1){//有PDF文件
			include "../model/subprogram/stuffimg_model.php";	//检查是否有图片
			}
		//检讨报告
		$CaseReport="&nbsp;";
        $checkCaseSql=mysql_query("SELECT E.Picture,E.Title FROM $DataIn.casetostuff C
                                    LEFT JOIN $DataIn.errorcasedata  E ON E.Id=C.cId
                                    WHERE C.StuffId='$StuffId' LIMIT 1",$link_id);
         if($checkCaseRow=mysql_fetch_array($checkCaseSql)){
		       $Picture=$checkCaseRow["Picture"];
		       $f1=anmaIn($Picture,$SinkOrder,$motherSTR);
			   $d1=anmaIn("download/errorcase/",$SinkOrder,$motherSTR);
	           $CaseReport="<img onClick='OpenOrLoad(\"$d1\",\"$f1\",6)' src='../images/warn.gif'  width='18' height='18'>";
	      }
		//配件QC检验标准图
        include "../model/subprogram/stuffimg_qcfile.php";

		//add by zx 2011-0427  begin
		$CompanyId=$myRow["CompanyId"];
		$SendSign=$myRow["SendSign"];
		$SignString="";
		//if ($SendSign==1) // SendSign: 0送货，1补货, 2备品
		switch ($SendSign){
			case 1:
				$thSql=mysql_query("SELECT SUM( S.Qty ) AS thQty  
				FROM $DataIn.ck2_thmain M  					   
				LEFT JOIN $DataIn.ck2_thsheet S ON S.Mid = M.Id
				WHERE M.CompanyId = '$CompanyId' AND S.StuffId = '$StuffId' ",$link_id);
				$thQty=mysql_result($thSql,0,"thQty");

				//补货的数量 add by zx 2011-04-27
				$bcSql=mysql_query("SELECT SUM( S.Qty ) AS bcQty  
				FROM $DataIn.ck3_bcmain M 
				LEFT JOIN $DataIn.ck3_bcsheet S ON S.Mid = M.Id
				WHERE M.CompanyId = '$CompanyId' AND S.StuffId = '$StuffId' ",$link_id);
				$bcQty=mysql_result($bcSql,0,"bcQty");
				$cgQty=$thQty-$bcQty;
				$noQty=$cgQty;
				$SignString="(补货)";
				$StockId="本次补货";
			 break;
			case 2:
			  $cgQty=0;
			  $noQty=0;
			  $SignString="(备品)";
			  $StockId="本次备品";
			 break;
			default :
				$rkTemp=mysql_query("SELECT IFNULL(SUM(R.Qty),0) AS Qty 
				    FROM $DataIn.ck1_rksheet R 
					LEFT JOIN $DataIn.cg1_stocksheet S ON S.StockId=R.StockId
					WHERE R.StockId='$StockId'",$link_id);
				$rkQty=mysql_result($rkTemp,0,"Qty");	//收货总数
				$noQty=$cgQty-$rkQty;
			 break;
		}


		/*
		//已收货总数
		$rkTemp=mysql_query("SELECT IFNULL(SUM(R.Qty),0) AS Qty
		    FROM $DataIn.ck1_rksheet R
			LEFT JOIN $DataIn.cg1_stocksheet S ON S.StockId=R.StockId
			WHERE R.StockId='$StockId'",$link_id);
		$rkQty=mysql_result($rkTemp,0,"Qty");	//收货总数
		$noQty=$cgQty-$rkQty;					//未收货数量
		*/
		//检查权限
		$UpdateIMG="&nbsp;";$UpdateClick="&nbsp;";
		$UpdateReturnClick="&nbsp;";$setSignClick="";
		$CheckData="<input type='checkbox' disabled />";

		if($czSign==1){//有权限并且订单可以做审核状态
			if($SubAction==31){//有权限
               //if ($Login_P_Number==10019 || $Login_P_Number==10871)
                $setSignClick="onclick='setCheckSign(this,$StuffId)'";
                $UpdateIMG="<img src='../images/register.png' width='30' height='30'";

                $UpdateClick="<span name='UpdateOk$i' id='UpdateOk$i'  value='!'  onclick='showCheckWin($Id,this)' style='cursor: pointer;' ><img src='../images/register.png' width='30' height='30'></span>";

                $CheckData="<input type='checkbox' id='checkId$i' name='checkId$i' value='$Id' />";

				$UpdateReturnIMG="<img src='../images/unPass.png' width='30' height='30'";
                $UpdateReturnClick="";
                $UpdateReturnClick="<span class='ButtonH_25' name='UpdateRe$i' style='background-color: #9A0000' id='UpdateRe$i'  value='x'  onclick='RegisterEstate($Id,this,15)' ><b>Ｘ</b></span>";
				$RemarkClick="onClick='ShowRemark(this,$Id)'";
				}
			else{//无权限
				if($SubAction==1){
					$UpdateIMG="<img src='../images/registerNo.png' width='30' height='30'";
					}
                                $RemarkClick="";
				}
			}
		if($noQty<=0 && $SendSign!=2){  //当前已全部入库
			//无权限再审核通过，只有审核不通过
			$UpdateReturnIMG="<img src='../images/unPass.png' width='30' height='30'";
			$UpdateReturnClick="<span class='ButtonH_25' name='UpdateRe$i' id='UpdateRe$i' style='background-color: #9A0000' value='x'  onclick='RegisterEstate($Id,this,15)' ><b>Ｘ</b></span>";
			}
		else{
			if($noQty<$Qty && $SendSign!=2){//当前送货量比未送货量还大,则更新收货确认数量
				$UpdateReturnIMG="<img src='../images/unPass.png' width='30' height='30'";
				$UpdateReturnClick="<span class='ButtonH_25' name='UpdateRe$i' id='UpdateRe$i' style='background-color: #9A0000'  value='x'  onclick='RegisterEstate($Id,this,15)' ><b>Ｘ</b></span>";
				}
			}
          include "../model/subprogram/stuffimg_model.php";	//检查是否有图片
			/*//最后权限
			if($Picture==1){//有PDF文件，可以审核
				include "../model/subprogram/stuffimg_model.php";	//检查是否有图片
				}
			else{
				$UpdateIMG="<img src='../images/registerNo.png' width='30' height='30'";
				}
			*/
             $CheckSign=$CheckSign==1?"<div style='color:#F00;'>全检</div>":"<div style='color:#060;'>抽检</div>";
			 //历史订单
			 $OrderQtyInfo="<a href='../public/cg_historyorder.php?StuffId=$StuffId&Id=' target='_blank'>view</a>";
			 //REACH 法规图
		    include "../model/subprogram/stuffreach_file.php";

                //仓管备注
                 $remarkSql=mysql_query("SELECT Remark FROM $DataIn.ck6_shremark WHERE ShId='$Id' LIMIT 1",$link_id);
                 if($remarkRow=mysql_fetch_array($remarkSql)){
                       $Remark=$remarkRow["Remark"];
                     }
                 else{
                       $Remark="<img src='../images/remark.gif'/>";
                     }
                if ($SendSign==0){
                    $POrderId=substr($StockId,0,12);
                    $showPurchaseorderClick="onClick='NewShowOrHide(ListRow$i,theCel$i,$i,$POrderId,$StockId);'";
                    $showPurchaseorder="[ + ]";
                    $ListRow="<tr bgcolor='#D9D9D9' id='ListRow$i' style='display:none'><td class='A0111' height='30' colspan='$Count'><br><div id='ShowDiv$i'>&nbsp;</div><br></td></tr>";
                }else{
                    $showPurchaseorderClick="";
                    $showPurchaseorder="&nbsp;";
                    $ListRow="";
                }

                $BillNumber=$myRow["BillNumber"];
                $OrderPO=$myRow["OrderPO"];
                $cName=$myRow["cName"];

			echo"<tr $isLastBgColor><td class='A0111' align='center' id='theCel$i' height='25' valign='middle' $showPurchaseorderClick>$showPurchaseorder</td>";
			echo"<td class='A0101' align='center'>$i</td>";
			echo"<td class='A0101' align='center'>$CheckData</td>";
			echo"<td class='A0101' align='center'>$Forshort</td>";

			//增加业务单号 产品编号 送货单号
			echo "<td class='A0101' align='center'>$OrderPO</td>";
			echo "<td class='A0101' align='center'>$cName</td>";
			echo "<td class='A0101' align='center'>$BillNumber</td>";

			echo"<td class='A0101' align='center'>$Date</td>";
			echo"<td class='A0101' align='center'>$StockId</td>";
//			echo"<td class='A0101' align='center'>$StuffId</td>";
//			echo"<td class='A0101'>$StuffCname</td>";
			echo"<td class='A0101' align='center'>$OrderQtyInfo</td>";
//			echo"<td class='A0101' align='center'>$CaseReport</td>";
//            echo"<td class='A0101' align='center'>$QCImage</td>";
//			echo"<td class='A0101' align='center'>$ReachImage</td>";//REACH
//			echo"<td class='A0101' align='center'>$UnitName</td>";
//            echo"<td class='A0101' align='center' $setSignClick>$CheckSign</td>";
//			echo"<td class='A0101' align='right'>$cgQty</td>";	//采购总数
			echo"<td class='A0101' align='right'><div class='redB'>$noQty</div></td>";	//未收货数量
			echo"<td class='A0101' align='right'>$Qty</td>";					//送货数量
			echo"<td class='A0101' align='center' $RemarkClick>$Remark</td>";
			//echo"<td class='A0101' align='center' $UpdateReturnClick>$UpdateReturnIMG</td>";
			echo"<td class='A0101' align='center' >$UpdateClick</td>";
			echo"</tr>";
			echo $ListRow;
			$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	echo"<tr><td colspan='".$Cols."' align='center' height='30' class='A0111' style='background-color: #ffffff'><div class='redB' style='background-color: #ffffff'>没有记录!</div></td></tr>";
	}
echo "</table>";
	?>
</form>
</body>
<div id='divShadow' class="divShadow" style="display:none;z-index:2;" onDblClick="closeMaskDiv()"></div>
<div id="divPageMask" class="divPageMask" style="display:none; background-color:rgba(0,0,0,0.6);"></div>
</html>
<script language="javascript" src="checkform.js" type="text/javascript"></script>
<script src='showkeyboard.js' type='text/javascript'></script>
<script src='checkstyle.js' type='text/javascript'></script>
<script>
 var keyboard=new KeyBoard();
 var checkSignboard=new checkSignBoard();
 var selObj;
 var selObjTime=0;
 function showCheckWin(Id,ee){
    selObj=ee;
	document.getElementById("divShadow").innerHTML="";
    var url="item2_qccause.php?Id="+Id;
　	var ajax=InitAjax();
　	ajax.open("GET",url,true);
	ajax.onreadystatechange =function(){
	　　if(ajax.readyState==4 && ajax.status ==200){
	　　　	document.getElementById("divShadow").innerHTML=ajax.responseText;
			}
		}
　	ajax.send(null);
	//定位对话框
	divShadow.style.left = window.pageXOffset+(window.innerWidth-800)/2+"px";
    divShadow.style.top = "5px";
	//divShadow.style.top = window.pageYOffset+(window.innerHeight-330)/2+"px";
	document.getElementById('divPageMask').style.display='block';
	document.getElementById('divShadow').style.display='block';
	document.getElementById('divPageMask').style.width = document.body.scrollWidth;
	document.getElementById('divPageMask').style.height =document.body.offsetHeight+"px";
	}

function showKeyboard(e,checkQty,rows){
    var sumQty=document.getElementById("sumQty").value;
    var ReQty=document.getElementById("ReQty").value*1;
    var oldQty=e.value;
    var maxQty=checkQty-sumQty*1+oldQty*1;
    var addQtyFun=function(){
	sumQty=sumQty-(oldQty-e.value);
        document.getElementById("sumQty").value=sumQty;
        if (ReQty>0){
          var InfoBack=document.getElementById("InfoBack");
          if (sumQty>=ReQty) {
              InfoBack.innerHTML="<div style='color:#FF0000;font-weight:bold;font-size:15px;'>抽检结果：拒收，退回；</div>";
          }else{
              InfoBack.innerHTML="<div style='color:#339900;font-weight:bold;font-size:15px;'>抽检结果：允收，入库；</div>";
          }
        }
        if (rows==""){
             var upfileName="otherfileinput";
        }
        else{
             var upfileName="fileinput"+rows;
        }
        if (e.value>0){
            document.getElementById(upfileName).style.display="";
        }
        else{
            document.getElementById(upfileName).style.display="none";
        }
       // var reQty=InfoBack
     };
   keyboard.show(e,maxQty,'<=','',addQtyFun);
}

function setCheckSign(e,Id){
   var saveFun=function(){
        var tmp_Str=e.innerHTML;
        if (tmp_Str.indexOf('抽检')>-1){
           var  oldVal=0;
        }else{
           var  oldVal=1;
        }
	var reValue=checkSignboard.Value;
	if (reValue==oldVal){
            return false;
        }else{
           var url="item2_3_ajax.php?StuffId="+Id+"&Sign="+reValue+"&ActionId=21";
	   var ajax=InitAjax();
　	   ajax.open("GET",url,true);
	   ajax.onreadystatechange =function(){
	　　   if(ajax.readyState==4 && ajax.status ==200){

                 if (ajax.responseText=="Y"){
                    ResetPage(0,2);
                 }
	      }
	   }
　	   ajax.send(null);
        }
    };
   checkSignboard.show(e,1,saveFun,"");
}

function CauseClick(e){
   var cause_Str=e.innerHTML;
   var OCause=document.getElementById("otherCause").value;
   OCause=OCause.replace(/(^\s*)|(\s*$)/g,""); //去掉空格
   if (OCause==""){
        document.getElementById("otherCause").value=cause_Str;
   }else{
       if(OCause.indexOf(cause_Str)>-1){
         return false;
       }else{
         document.getElementById("otherCause").value=OCause + ";" +cause_Str;
       }
   }

}

function updateRegister(Id){
  var QtyStr="";
  var IdStr="";
  var indexStr="";
  var sumQty=document.getElementById("sumQty").value;
  var ReQty=document.getElementById("ReQty").value*1;
  var CheckAQL=document.getElementById("CheckAQL").value;
  var CheckQty=document.getElementById("CheckQty").value*1;

  if (sumQty==0){
       if(!confirm("品检结果：确认全部来料合格？")){
            return false;
       }
   }

if (sumQty>0){
  var CauseId=document.getElementsByName("CauseId");
  var badQty=document.getElementsByName("badQty");

  for (i=0;i<badQty.length;i++){
      if (badQty[i].value>0){
          IdStr+=CauseId[i].value+",";
          QtyStr+=badQty[i].value+",";
          indexStr+=i+",";
      }
  }
   var ObadQty=document.getElementById("otherbadQty").value;
   var OCause=document.getElementById("otherCause").value;

   OCause=OCause.replace(/(^\s*)|(\s*$)/g,""); //去掉空格
   if (ObadQty>0){
       if (OCause==""){
           var income=document.getElementById("incoming").innerHTML;
           var otherbadQty=document.getElementById("otherbadQty").value;

           if(Number(otherbadQty) > Number(income)){
               alert(Number(otherbadQty));
           }

           alert("请输入其它原因");
           document.getElementById("otherCause").value="";
           document.getElementById("otherCause").focus();
           return false;
           }
       else{
           IdStr+="-1";
           QtyStr+=ObadQty;
           }
       }
   else{
       IdStr=IdStr.slice(0,-1);
       QtyStr=QtyStr.slice(0,-1);
        indexStr=indexStr.slice(0,-1);
       }
       Id=Id+"|"+sumQty+"|"+CheckQty+"|"+CheckAQL+"|"+ReQty+"|"+IdStr+"|"+QtyStr+"|"+indexStr+"|"+escape(OCause);
     }//sumQty>0
 else{
       Id=Id+"|"+sumQty+"|"+CheckQty+"|"+CheckAQL+"|"+ReQty;
 }


  closeMaskDiv();
}

function checkObadValue()
{
    var ObadQty=document.getElementById("otherbadQty").value;
    var OCause=document.getElementById("otherCause").value;
    var sumQty=document.getElementById("sumQty").value;
    var income=document.getElementById("incoming").innerHTML;
    var otherbadQty=document.getElementById("otherbadQty").value;

    if(Number(otherbadQty) > Number(income)){
        alert('不良数量大于来料数量! ');
        return false;
    }

   OCause=OCause.replace(/(^\s*)|(\s*$)/g,""); //去掉空格
   if (ObadQty>0){
       if (OCause==""){
           alert("请输入其它原因");
           document.getElementById("otherCause").value="";
           document.getElementById("otherCause").focus();
           return false;
           }
       }

    if (sumQty==0){
//        if(!confirm("品检结果：确认全部来料合格？")){
//             return false;
//        }
   }
    closeMaskDiv();
    setButtonEstate(sumQty);
    return true;
}

function setButtonEstate(sumQty)
{
//     var ThisID=selObj.id;
// 	var No=ThisID.substring(8);
// 	var UpOK=document.getElementById("UpdateOk"+No);
//         UpOK.disabled="disabled";

        var parTd=selObj.parentNode;


        parTd.innerHTML="&nbsp;";
        if (sumQty==0){
        	parTd.style.backgroundColor="#339900";
        }
        else{
        	parTd.style.backgroundColor="#FF0000";
        }
}

function RegisterEstate(Id,ee,ActionId){
	var ThisID=ee.id;
	var No=ThisID.substring(8);
	var UpOK=document.getElementById("UpdateOk"+No);
	var UpRe=document.getElementById("UpdateRe"+No);
	var url="item2_3_ajax.php?Id="+Id+"&ActionId="+ActionId;
	var ajax=InitAjax();
　	ajax.open("GET",url,true);
	ajax.onreadystatechange =function(){
	　　if(ajax.readyState==4 && ajax.status ==200){
			alert(ajax.responseText);
			UpOK.disabled="disabled";
			UpRe.disabled="disabled";
			}
		}
　	ajax.send(null);
}

function ShowRemark(e,Id){
    var remark=e.innerHTML;
    if (remark.indexOf("<img")!=-1){
        remark="";
    }
    var strResponse=prompt("备注信息：",remark);
    strResponse=strResponse.replace(/(^\s*)|(\s*$)/g,"");
    if (strResponse){
       var url="item2_3_ajax.php?Id="+Id+"&ActionId=5&Remark="+strResponse;

	var ajax=InitAjax();
　	ajax.open("GET",url,true);
	ajax.onreadystatechange =function(){
	　　if(ajax.readyState==4 && ajax.status ==200){
               if (ajax.responseText=="Y")
		   e.innerHTML=strResponse;
	     }
	 }
　	ajax.send(null);
    }
}

function QuickCheck(e,stuffId,stuffCname)
{
    if(!confirm("品检结果：确认以下配件名称为:"+stuffCname +" ,全部来料合格？")){
            return false;
       }
      var gysId=document.getElementById("GysId").value;
      var billNumber=document.getElementById("BillNumber").value;
      if (gysId!=""){
             var url="item2_3_ajax.php?ActionId=23&GysId="+gysId+"&StuffId="+stuffId+"&BillNumber="+billNumber;
            // alert(url);
            e.disabled="disabled";
            var ajax=InitAjax();
            ajax.open("GET",url,true);
            ajax.onreadystatechange =function(){
                if(ajax.readyState==4 && ajax.status ==200){
                    //if (ajax.responseText=="Y")
                     //  alert(ajax.responseText);
                       ResetPage(1,2);
                    }
                }
            ajax.send(null);

      }
}

function batchCheck(e) {
    jQuery('.response').show();
    jQuery(e).hide();
	var choosedRow=0;
	var Ids;
	jQuery('input[name^="checkId"]:checkbox').each(function() {
        if (jQuery(this).prop('checked') ==true) {

			choosedRow=choosedRow+1;
    		if (choosedRow == 1) {
				Ids = jQuery(this).val();
			} else {
				Ids = Ids + "," + jQuery(this).val();
			}
        }
	});

	if (choosedRow == 0) {
		alert("该操作要求选定记录！");
		return;
	}
    jQuery('.response').show();
    var url="item2_3_1_ajax.php";
	var data = "ActionId=19&Ids="+Ids;
    var ajax=InitAjax();
    ajax.open("POST",url,true);
    ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
    ajax.onreadystatechange =function(){
        if(ajax.readyState==4 && ajax.status ==200){
            //if (ajax.responseText=="Y")
               //alert(ajax.responseText);
               ResetPage(1,2);

            }
        };
    ajax.send(data);
}

</script>