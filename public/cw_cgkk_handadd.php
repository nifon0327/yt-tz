<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 手工新增采购单扣款记录");//需处理
$nowWebPage =$funFrom."_handadd";	
$toWebPage  =$funFrom."_handsave";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=980;$tableMenuS=500;
$CheckFormURL="thisPage";
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="ListTable">
	<tr valign="bottom">
	  <td height="25" colspan="7" align="center" class="A0011">扣款单信息</td>
	</tr>
	<tr bgcolor='<?php  echo $Title_bgcolor?>'>
		<td width="10" height="25" class="A0010" bgcolor="#FFFFFF">&nbsp;</td>		
		<td align="center" class="A1111">供 应 商</td>
	  	<td align="center" class="A1101">采&nbsp;&nbsp;&nbsp;&nbsp;购</td>
	  	<td align="center" class="A1101">扣款单号</td>
		<td align="center" class="A1101">扣款日期</td>
		<td width="240" align="center" class="A1101">扣款备注</td>
		<td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr>
		<td width="10" class="A0010" height="25">&nbsp;</td>
		<td align="center" valign="middle" class="A0111"><select name="CompanyId" id="CompanyId" style="width: 125px;">
		<!-- onChange="javascript:document.form1.submit();" -->
          <?php 
			//供应商:有采购且收完货
			/*
			$GYS_Sql = "SELECT S.CompanyId,P.Forshort,P.Letter 
			FROM $DataIn.cg1_stocksheet S 
			LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId 			
			WHERE S.rkSign=0 AND S.Mid>0 GROUP BY S.CompanyId ORDER BY P.Letter";
			*/
			$GYS_Sql = "SELECT P.CompanyId,P.Forshort,P.Letter 
			FROM $DataIn.trade_object P 			
			WHERE 1 AND P.Estate=1  AND P.ObjectSign IN(1,3) ORDER BY P.Letter";
			
			$GYS_Result = mysql_query($GYS_Sql); 
			while ( $GYS_Myrow = mysql_fetch_array($GYS_Result)){
				$ProviderTemp=$GYS_Myrow["CompanyId"];
				$CompanyId=$CompanyId==""?$ProviderTemp:$CompanyId;
				$Forshort=$GYS_Myrow["Forshort"];
				$Letter=$GYS_Myrow["Letter"];
				$Forshort=$Letter.'-'.$Forshort;		
				if ($ProviderTemp==$CompanyId){
					echo "<option value='$ProviderTemp' selected>$Forshort</option>";
					}
				else{
					echo "<option value='$ProviderTemp'>$Forshort</option>";
					}
				} 
			?>
        </select></td>
		<td class="A0101" align="center"><select name="BuyerId" id="BuyerId" style="width: 80px;">
          <?php 
			$buyerSql = mysql_query("SELECT S.Number,S.Name 
			FROM  $DataPublic.staffmain S 
			WHERE S.BranchId IN (".$APP_CONFIG['PROCUREMENT_BRANCHID'] .") AND S.Estate=1 
			GROUP BY S.Number ",$link_id);
			if($buyerRow = mysql_fetch_array($buyerSql)){			
				do{
					$BuyerId=$buyerRow["Number"];
					$Name=$buyerRow["Name"];					
					echo "<option value='$BuyerId'>$Name</option>";
					}while($buyerRow = mysql_fetch_array($buyerSql));
				} 
			?>
        </select></td>
		<?php 
               $MaxValues=1;
               $CheckMaxBillNumber=mysql_query("SELECT  BillNumber  FROM $DataIn.cw15_gyskkmain WHERE 1  ORDER BY Date DESC LIMIT 0,100",$link_id);
               while($CheckMaxRow=mysql_fetch_array($CheckMaxBillNumber)){
                       $nowBillNumber=$CheckMaxRow["BillNumber"];
                        $nowBillTemp=substr($nowBillNumber,11);
                       if($nowBillTemp>$MaxValues)$MaxValues=$nowBillTemp;
               }
            $MaxValues=$MaxValues+1;
            $MaxBillNumber="Debit note ".$MaxValues;
		
		?>
		<td class="A0101" align="center"><input name="BillNumber" type="text" id="BillNumber" class="INPUT0100" size="15" value="<?php  echo $MaxBillNumber?>"></td>
		<td align="center" class="A0101"><input name="KKDate" type="text" id="KKDate" value="<?php  echo date("Y-m-d")?>" size="10" maxlength="10"></td>
		<td align="center" class="A0101"><input name="Remark" type="text" id="Remark" size="50" class="INPUT0100"></td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr>
	<tr>
		<td width="10" height="50" class="A0010">&nbsp;</td>
		<td colspan="5" align="center" valign="bottom">扣款单明细资料
	    <input name="TempValue" type="hidden" id="TempValue"><input name='AddIds' type='hidden' id="AddIds"></td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr>
</table>

<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
	<tr bgcolor='<?php  echo $Title_bgcolor?>'>
		<td width="10" class="A0010" bgcolor="#FFFFFF" height="25">&nbsp;</td>
		<td class="A1111" width="60" align="center">操作</td>
		<td class="A1101" width="40" align="center">序号</td>
		<td class="A1101" width="340" align="center">配件名称</td>
		<td class="A1101" width="80" align="center">单价</td>
		<td class="A1101" width="80" align="center">扣款数量</td>
		<td class="A1101" width="380" align="center">扣款原因</td>
		<td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>	
	<tr>
		<td width="10" class="A0010" bgcolor="#FFFFFF" height="25">&nbsp;</td>
		<td class="A0111" width="60" align="center"><a href="#" onclick="AddRow()" title="新增一行">+</a></td>
		<td class="A0101" width="40" align="center">1</td>
		<td class="A0101" width="340" align="center"> <input type="text" id="StuffName1" name="StuffName1" size="50"></td>
		<td class="A0101" width="80" align="center"><input type="text" id="Price1" name="Price1" size="8"></td>
		<td class="A0101" width="80" align="center"><input type="text" id="KKQty1" name="KKQty1" size="8"></td>
		<td class="A0101" width="380" align="center"> <input type="text" id="Remark1" name="Remark1" size="44"></td>
		<td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>	
</table>

<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>

<script>
function CheckForm(){
	var Message=""
	var BillNumber=document.form1.BillNumber.value;
	if(BillNumber==""){
		Message="没有输入扣款单号.";
		}
	if(Message!=""){
		alert(Message);return false;
		}
	else{
		var StockValues="";
		//读取加入的数据
		oTR=NoteTable.insertRow(NoteTable.rows.length);
		tmpNum=oTR.rowIndex-1;
		for(i=1;i <=tmpNum;i++){ 
		   var StuffName="StuffName"+i;
		   var Price="Price"+i;
		   var KKQty="KKQty"+i;
		   var Remark="Remark"+i;
		   var StuffNameobj=document.getElementById(StuffName);
		   var Priceobj =document.getElementById(Price);
		   var KKQtyobj=document.getElementById(KKQty);
		   var Remarkobj=document.getElementById(Remark);
		   if(document.getElementById(StuffName)!=null) {
		      if(StuffNameobj.value==""){alert("请输入配件名!");return false;}
		      if(Priceobj.value==""){alert("请输入价格!");return false;}
		      if(KKQtyobj.value==""){alert("请输入扣款数量!");return false;}
		      if(StockValues==""){
				    StockValues=StuffNameobj.value+"!"+Priceobj.value+"!"+KKQtyobj.value+"!"+Remarkobj.value;
				    }
			    else{
				    StockValues=StockValues+"|"+StuffNameobj.value+"!"+Priceobj.value+"!"+KKQtyobj.value+"!"+Remarkobj.value;
				    }
				}
		  } 
	    //alert(StockValues);
		document.form1.AddIds.value=StockValues;
		document.form1.action="cw_cgkk_handsave.php";
		document.form1.submit();
		}
	}

//删除指定行
function deleteRow(rowIndex){
	NoteTable.deleteRow(rowIndex);
	ShowSequence(NoteTable);
	}
	
function ShowSequence(TableTemp){
	for(i=1;i<TableTemp.rows.length;i++){ 
		TableTemp.rows[i].cells[2].innerText=i; 
		
		}
	}   
	
function AddRow(){
	oTR=NoteTable.insertRow(NoteTable.rows.length);
	tmpNum=oTR.rowIndex;
	//alert(tmpNum);
	//
	oTD=oTR.insertCell(0);
	oTD.innerHTML="&nbsp;";
	oTD.className ="A0010";
	oTD.align="center";
	oTD.height="20";
	oTD.width="10";
	
	//第一列:操作
	oTD=oTR.insertCell(1);
	oTD.innerHTML="<a href='#' onclick='deleteRow(this.parentNode.parentNode.rowIndex)' title='删除当前行'>×</a>";
	oTD.onmousedown=function(){
		window.event.cancelBubble=true;
		};
	oTD.className ="A0111";
	oTD.align="center";
	oTD.width="60";
				
	//第二列:序号
	oTD=oTR.insertCell(2);
	oTD.innerHTML=""+tmpNum+"";
	oTD.className ="A0101";
	oTD.align="center";
	oTD.width="40";
				
	//三、配件
	oTD=oTR.insertCell(3);
	oTD.innerHTML="<input name='StuffName"+tmpNum+"' type='text' id='StuffName"+tmpNum+"' size='50'>";
	oTD.className ="A0101";
	oTD.align="center";
	oTD.width="340";
	
	//四、单价
	oTD=oTR.insertCell(4);
	oTD.innerHTML="<input name='Price"+tmpNum+"' type='text' id='Price"+tmpNum+"' size='8'>";
	oTD.className ="A0101";
	oTD.align="center";
	oTD.width="80";
	
	//五、扣款数量
	oTD=oTR.insertCell(5);
	oTD.innerHTML="<input name='KKQty"+tmpNum+"' type='text' id='KKQty"+tmpNum+"' size='8'>";
	oTD.className ="A0101";
	oTD.align="center";
	oTD.width="80";
	
	//六、扣款原因
	oTD=oTR.insertCell(6);
	oTD.innerHTML="<input name='Remark"+tmpNum+"' type='text' id='Remark"+tmpNum+"' size='44'>";
	oTD.className ="A0101";
	oTD.align="center";
	oTD.width="380";
	
	//
	oTD=oTR.insertCell(7);
	oTD.innerHTML="&nbsp;";
	oTD.className ="A0001";
	oTD.align="center";
	oTD.width="10";
	
	}
</script>
