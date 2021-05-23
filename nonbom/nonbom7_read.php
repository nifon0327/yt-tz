<script>
function ShowOrHideFixed(e,f,Order_Rows,rkId,RowId,GoodsId){
	e.style.display=(e.style.display=="none")?"":"none";
	var yy=f.src;
	if (yy.indexOf("showtable")==-1){
		f.src="../images/showtable.gif";
		Order_Rows.myProperty=true;
		}
	else{
		f.src="../images/hidetable.gif";
		Order_Rows.myProperty=false;
		//动态加入采购明细
		if(rkId!=""){
			var url="../nonbom/nonbom7_ajax.php?rkId="+rkId+"&RowId="+RowId+"&GoodsId="+GoodsId;
		　	var show=eval("showStuffTB"+RowId);
		　	var ajax=InitAjax();
		　	ajax.open("GET",url,true);
			ajax.onreadystatechange =function(){
		　		if(ajax.readyState==4){// && ajax.status ==200
					var BackData=ajax.responseText;
					show.innerHTML=BackData;
					}
				}
			ajax.send(null);
			}
		}
	}

	function zhtj(obj){
		switch(obj){
			case "chooseDate"://改变采购
				if(document.all("CompanyId")!=null){
					document.forms["form1"].elements["CompanyId"].value="";
					}
			break;
			}
		document.form1.action="nonbom7_read.php";
		document.form1.submit();
	}

	function delete_img($id){
		if(!confirm('是否确认删除入库凭证')){
			return;
		}
	   ajax('nonbom7_clearpic.php?id='+$id,function(data){
	   	  var json=JSON.parse(data);
	   	  if(json.status==100){
	   	  	 alert('删除成功');
	   	  	 window.location.href='nonbom7_read.php';
	   	  }
	   });

	}

    function ajax(url,fnSucc)
    {
        if(window.XMLHttpRequest)
        {
            var oAjax = new XMLHttpRequest();
        }
        else
        {
            var oAjax = new ActiveXObject("Microsoft.XMLHTTP");//IE6浏览器创建ajax对象
        }
        oAjax.open("GET",url,true);//把要读取的参数的传过来。
        oAjax.send();
        oAjax.onreadystatechange=function()
        {
            if(oAjax.readyState==4)
            {
                if(oAjax.status==200)
                {
                    fnSucc(oAjax.responseText);//成功的时候调用这个方法
                }
                else
                {
                    if(fnfiled)
                    {
                        fnField(oAjax.status);
                    }
                }
            }
        };
    }

 

</script>
<?php
include "../model/modelhead.php";
//步骤2：需处理
$tableMenuS=500;
ChangeWtitle("$SubCompany 非bom配件入库记录");
$funFrom="nonbom7";
$From=$From==""?"read":$From;
$sumCols="6,7";			//求和列,需处理
$MergeRows=5;
$Th_Col="操作|50|入库日期|70|入库单号|100|凭证|50|采购|80|供应商|150|备注|60|选项|50|序号|50|客户项目|150|配件编号|50|非bom配件名称|250|条码|100|采购数量|60|入库总数|60|本次入库|60|单位|40|采购主单|70|申购流水号|70";

//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 200;							//每页默认记录数量
$ActioToS="1,2,3,4,7,8";
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-可选条件下拉框
if($From!="slist"){	//非查询：过滤采购、结付方式、供应商、月份
	$SearchRows="";
	$date_Result = mysql_query("SELECT DATE_FORMAT(Date,'%Y-%m')  AS  Date FROM $DataIn.nonbom7_inmain WHERE 1 GROUP BY DATE_FORMAT(Date,'%Y-%m') ORDER BY DATE_FORMAT(Date,'%Y-%m')  DESC",$link_id);
	if($dateRow = mysql_fetch_array($date_Result)) {
		echo"<select name='chooseDate' id='chooseDate' onchange='zhtj(this.name)'>";
		do{
			$dateValue=$dateRow["Date"];
			$chooseDate=$chooseDate==""?$dateValue:$chooseDate;
			if($chooseDate==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows.=" and  DATE_FORMAT(B.Date,'%Y-%m') ='$dateValue'";
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		echo"</select>&nbsp;";
		}
	$providerSql = mysql_query("SELECT B.CompanyId,P.Forshort,P.Letter 
			FROM $DataIn.nonbom7_inmain B,$DataPublic.nonbom3_retailermain P 
        WHERE B.CompanyId=P.CompanyId $SearchRows GROUP BY B.CompanyId ORDER BY P.Letter",$link_id);
	if($providerRow = mysql_fetch_array($providerSql)){
		echo "<select name='CompanyId' id='CompanyId' onchange='zhtj(this.name)'>";
		echo"<option value='' selected>全部</option>";
		do{
			$Letter=$providerRow["Letter"];
			$Forshort=$providerRow["Forshort"];
			$Forshort=$Letter.'-'.$Forshort;
			$thisCompanyId=$providerRow["CompanyId"];
			if($CompanyId==$thisCompanyId){
				echo"<option value='$thisCompanyId' selected>$Forshort </option>";
				$SearchRows.=" and E.CompanyId='$thisCompanyId'";
				}
			else{
				echo"<option value='$thisCompanyId'>$Forshort</option>";
				}
			}while ($providerRow = mysql_fetch_array($providerSql));
		echo"</select>&nbsp;";
		}


	}
//检查进入者是否采购
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT B.Bill,B.BillNumber,B.CompanyId,B.BuyerId,B.Remark,B.Date AS rkDate,B.Locks AS mainLocks,B.Operator,
		A.Id,A.Mid,A.GoodsId,A.Qty,A.cgId,A.Locks,
		C.GoodsName,C.BarCode,C.Attached,C.Unit,
		D.TypeName,
		E.Forshort,
		F.Name AS Buyer,
		G.wStockQty,G.oStockQty,G.mStockQty,H.Qty AS cgQty,I.PurchaseID,I.Id AS cgMid,T.Forshort AS Company
FROM $DataIn.nonbom7_insheet A
LEFT JOIN $DataIn.nonbom7_inmain B ON B.Id=A.Mid 
LEFT JOIN $DataPublic.nonbom4_goodsdata C ON C.GoodsId=A.GoodsId 
LEFT JOIN $DataPublic.nonbom2_subtype D ON D.Id=C.TypeId
LEFT JOIN $DataPublic.nonbom3_retailermain E ON E.CompanyId=B.CompanyId 
LEFT JOIN $DataPublic.staffmain F ON F.Number=B.BuyerId 
LEFT JOIN $DataPublic.nonbom5_goodsstock G ON G.GoodsId=A.GoodsId
LEFT JOIN $DataIn.nonbom6_cgsheet H ON H.Id=A.cgId
LEFT JOIN $DataIn.nonbom6_cgmain I ON I.Id=H.Mid
LEFT JOIN $DataPublic.trade_object T ON T.Id = C.TradeId
WHERE 1 $SearchRows  ORDER BY B.Date DESC,B.Id DESC";

//echo $mySql;

$mainResult = mysql_query($mySql." $PageSTR",$link_id);
if($mainRows = mysql_fetch_array($mainResult)){
	$tbDefalut=0;
	$midDefault="";
	$Dir=anmaIn("download/nonbom/",$SinkOrder,$motherSTR);
	$DirRK=anmaIn("download/nonbom_rk/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		//主单信息
		$Mid=$mainRows["Mid"];
		$rkDate=$mainRows["rkDate"];
		$Bill=$mainRows["Bill"];
		$BillNumber=$mainRows["BillNumber"];
		$BuyerId=$mainRows["BuyerId"];
		$Buyer=$mainRows["Buyer"];
		$Forshort=$mainRows["Forshort"];
		$Remark=$mainRows["Remark"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$mainRows[Remark]' width='18' height='18'>";
		if($Bill==1){
			$Bill=$Mid.".pdf";
			$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
			$Bill="<span onClick='OpenOrLoad(\"$DirRK\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>查看</span>";
			}
		else{
			$Bill="&nbsp;";
			}

		$upMian="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"nonbom7_upmain\",$Mid)' src='../images/edit.gif' alt='更新入库主单资料' width='16' height='16'>";
		$upMian.="&nbsp;&nbsp;<img src='../images/icon-delete.png' style='CURSOR: pointer' class='clear-image' width='15' height='15' value='".$Mid."' onclick='delete_img(".$Mid.")'>";
		//明细资料
		$GoodsId=$mainRows["GoodsId"];
		if($GoodsId!=""){
			$checkidValue=$mainRows["Id"];
			$cgId=$mainRows["cgId"];
			$GoodsName=$mainRows["GoodsName"];
			$BarCode=$mainRows["BarCode"]==""?"&nbsp;":$mainRows["BarCode"];
			$Attached=$mainRows["Attached"];
			if($Attached==1){
				$Attached=$GoodsId.".jpg";
				$Attached=anmaIn($Attached,$SinkOrder,$motherSTR);
				$GoodsName="<span onClick='OpenOrLoad(\"$Dir\",\"$Attached\")' style='CURSOR: pointer;color:#FF6633'>$GoodsName</span>";
				}
            include"../model/subprogram/good_Property.php";//非BOM配件属性
			$PurchaseID=$mainRows["PurchaseID"];
			$cgMid=$mainRows["cgMid"];
			$cgMidSTR=anmaIn($cgMid,$SinkOrder,$motherSTR);
			$PurchaseID="<a href='nonbom6_view.php?f=$cgMidSTR' target='_blank'>$PurchaseID</a>";

			$Unit=$mainRows["Unit"];
			$cgQty=del0($mainRows["cgQty"]);
			$Qty=del0($mainRows["Qty"]);

		    $PurchaseIDStr="<a href='../model/subprogram/purchaseorder_view.php?f=$MidSTR' target='_blank'>$PurchaseID</a>";

			$rkTemp=mysql_query("SELECT IFNULL(SUM(Qty),0) AS Qty FROM $DataIn.nonbom7_insheet WHERE cgId='$cgId' order by Id",$link_id);
			$rkQty=mysql_result($rkTemp,0,"Qty");
			$rkQty=$rkQty==""?0:del0($rkQty);
			if($rkQty==$cgQty){
				$rkBgColor="class='greenB'";
				$rkQty="<a href='nonbom7_list.php?cgId=$cgId' target='_blank' style='color:#093'>$rkQty</a>";
				}
			else{
				$rkBgColor="class='redB'";
				if($rkQty>0){
					$rkQty="<a href='nonbom7_list.php?cgId=$cgId' target='_blank' style='color:#F00'>$rkQty</a>";
					}
				else{
					$rkQty="&nbsp;";
					}
				}
			$Company = $mainRows["Company"];
			$CompanyId=$mainRows["CompanyId"];
			//加密
			$CompanyId=anmaIn($CompanyId,$SinkOrder,$motherSTR);
			$Forshort="<a href='nonbom3_view.php?d=$CompanyId' target='_blank'>$Forshort</a>";
			//配件分析
			$GoodsIdStr="<a href='nonbom4_report.php?GoodsId=$GoodsId' target='_blank'>$GoodsId</a>";
			$Locks=$mainRows["Locks"];
			//检查是否已请款
			$LockRemark="";

			//$checkFK=mysql_fetch_array(mysql_query("SELECT count(*) AS Nums FROM $DataIn.nonbom12_cwsheet WHERE cgId='$cgId'",$link_id));
		    $checkFK=mysql_fetch_array(mysql_query("SELECT count(*) AS Nums  FROM $DataIn.nonbom11_qksheet WHERE CgMid='$cgMid'  ",$link_id));
			if($checkFK["Nums"]>0 && $Login_P_Number!=10868){
				$LockRemark="已请款";
				}
			if($Locks==0){//锁定状态:A一种是可以操作记录（分权限）；B一种是不可操作记录（不分权限）
				if($Keys & mLOCK){
					if($LockRemark!=""){//财务强制锁定
						$Choose="&nbsp;&nbsp;&nbsp;<img src='../images/lock.png' title='$LockRemark' width='15' height='15'>";
						}
					else{
						$Choose="<input name='checkid[]' type='checkbox' id='checkid$i' value='$checkidValue' disabled><img src='../images/lock.png' width='15' height='15'>";
						}
					}
				else{		//A2：无权限对锁定记录操作
					$Choose="&nbsp;&nbsp;&nbsp;<img src='../images/lock.png' title='锁定操作!' width='15' height='15'>";
					}
				}
			else{
				if(($BuyerId==$Login_P_Number && ($Keys & mUPDATE || $Keys & mDELETE)) || $Keys & mLOCK){//有权限
					if($LockRemark!=""){
						$Choose="&nbsp;&nbsp;&nbsp;<img src='../images/lock.png' title='$LockRemark' width='15' height='15'/>";
						}
					else{
						$Choose="<input name='checkid[]' type='checkbox' id='checkid$i' value='$checkidValue' disabled><img src='../images/unlock.png' width='15' height='15'/>";
						}
					}
				else{//无权限
					$Choose="&nbsp;&nbsp;&nbsp;<img src='../images/lock.png' title='锁定操作!' width='15' height='15'/>";
					}
				}
             $CheckCodeRow=mysql_fetch_array(mysql_query("SELECT  Id FROM $DataIn.nonbom7_code WHERE rkId='$checkidValue' and GoodsId=$GoodsId LIMIT 1",$link_id));
             $CheckCodeId=$CheckCodeRow["Id"];
             if($CheckCodeId>0){
			         $showPurchaseorder="<img src='../images/showtable.gif' onClick='ShowOrHideFixed(StuffList$i,showtable$i,StuffList$i,\"$checkidValue\",$i,\"$GoodsId\");' name='showtable$i' title='显示或隐藏信息资料.' width='13' height='13' style='CURSOR: pointer'/>";
              $subTableWidth=$tableWidth-440;
			$StuffListTB="
				<table width='$subTableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
				<tr bgcolor='#B7B7B7'>
				<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
           }
        else{
           $showPurchaseorder="";$StuffListTB="";
          }
			$Sid=anmaIn($StockId,$SinkOrder,$motherSTR);
			////////////////////////////////////////////////////
			if($tbDefalut==0 && $midDefault==""){//首行
				//并行列$Th_Col="操作|50|入库日期|70|入库凭证|80|采购|50|选项|60|序号|40|非bom配件编号|50|非bom配件名称|300|单位|40|采购数量|60|本次入库|60|采购单号|70|申购流水号|70|供应商|80";
				echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
				echo"<td scope='col' class='A0111' width='$Field[$m]' align='center'>$upMian</td>";//更新
				$unitWidth=$tableWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='right'>$rkDate</td>";//入库日期
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$BillNumber</td>";		//下单
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Bill</td>";		//下单
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Buyer</td>";		//采购
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Forshort</td>";		//供应商
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Remark</td>";		//备注
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				//echo"<td width='$unitWidth' class='A0101'>";
				echo"<td width='' class='A0101'>";
				$midDefault=$Mid;
				}
			if($midDefault!="" && $midDefault==$Mid){//同属于一个主ID，则依然输出明细表格
				$m=15;
				echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
				echo"<tr onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
					onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
					onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
				$unitFirst=$Field[$m]-1;
				echo"<td class='A0001' width='$unitFirst'  align='center' $tdBGCOLOR>$Choose  $showPurchaseorder</td>";//选项
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$j</td>";			//序号
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$Company</td>";			//项目
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$GoodsIdStr</td>";	//配件ID
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]'>$GoodsName</td>";	//配件
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]'>$BarCode</td>";	//条码
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$cgQty</td>";		//需求数量
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$rkQty</td>";		//入库总数
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'><div $rkBgColor>$Qty</div></td>";		//入库数量
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$Unit</td>";	//单位
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$PurchaseID</td>";		//采购单号
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='center'>$cgId</td>";//需求流水号
				echo"</tr></table>";
				$i++;
				$j++;
				}
			else{
				//新行开始
				echo"</td></tr></table>";//结束上一个表格
				//并行列
				echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
				echo"<td scope='col' class='A0111' width='$Field[$m]' align='center'>$upMian</td>";//更新
				$unitWidth=$tableWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='right'>$rkDate</td>";//入库日期
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$BillNumber</td>";		//入库单号
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Bill</td>";		//入库凭证
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Buyer</td>";		//采购
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Forshort</td>";		//供应商
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Remark</td>";		//备注
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				//并行宽
				//echo"<td width='$unitWidth' class='A0101'>";
				echo"<td width='' class='A0101'>";
				$midDefault=$Mid;
				echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
				echo"<tr onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
					onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
					onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
				$unitFirst=$Field[$m]-1;
				echo"<td class='A0001' width='$unitFirst'  align='center' $tdBGCOLOR>$Choose $showPurchaseorder</td>";//选项
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$j</td>";			//序号
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$Company</td>";			//项目
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$GoodsIdStr</td>";	//配件ID
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]'>$GoodsName</td>";	//配件
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]'>$BarCode</td>";	//条码
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$cgQty</td>";		//需求数量
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$rkQty</td>";		//入库总数
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'><div $rkBgColor>$Qty</div></td>";		//入库数量
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$Unit</td>";	//单位
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$PurchaseID</td>";		//采购单号
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='center'>$cgId</td>";//需求流水号
				echo"</tr></table>";
				$i++;
				$j++;
				}
                            echo $StuffListTB;
			}
		}while($mainRows = mysql_fetch_array($mainResult));
	echo"</tr></table>";
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
