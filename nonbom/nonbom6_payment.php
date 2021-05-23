<?php 
//分更新或请款：要求可以分批请款  2013-11-21 ewen OK
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 分批请款主单");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_payment";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$Mid=$Mid==""?$checkqkid[0]:$Mid;
$upData=mysql_fetch_array(mysql_query("SELECT A.mainType,A.CompanyId,A.BuyerId,A.Date,A.PurchaseID,A.Remark,A.Operator,B.Name,SUM(C.Qty*C.Price) AS hkAmount,D.yqHK
	FROM $DataIn.nonbom6_cgmain A
	LEFT JOIN $DataPublic.staffmain B ON B.Number=A.BuyerId
	LEFT JOIN $DataIn.nonbom6_cgsheet C ON C.Mid=A.Id
	LEFT JOIN (SELECT cgMid,IFNULL(SUM(hkAmount),0) AS yqHK FROM $DataIn.nonbom11_qksheet WHERE cgMid='$Mid') D ON D.cgMid=A.Id
	WHERE A.Id='$Mid' GROUP BY A.Id ORDER BY A.Id DESC",$link_id));
$mainType=$upData["mainType"];
$CompanyId=$upData["CompanyId"];
$PurchaseID=$upData["PurchaseID"];
$Date=$upData["Date"];
$hkAmount=$upData["hkAmount"];
$Remark=$upData["Remark"];
$Name=$upData["Name"];
$BuyerId=$upData["BuyerId"];

$yqHK=$upData["yqHK"];

$wqHK=$hkAmount-$yqHK;
/*
if($BuyerId!=$Login_P_Number){
	$SaveSTR="NO";
	$LockSTR="<span class='redB'>(非采购本人，不允许操作)</span>";
	}*/
if($wqHK==0 ){
	$SaveSTR="NO";
	$LockSTR="<span class='redB'>(已全部请款，不允许更新或请款操作)</span>";
	}
        $CheckFormURL="thisPage";
//步骤4：
$tableWidth=950;$tableMenuS=600;
include "../model/subprogram/add_model_t.php";
$Parameter="Mid,$Mid,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,ActionId,155,CompanyId,$CompanyId,mainType,$mainType";
//步骤5：//需处理
 ?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
	  <td class="A0011">
      <table width="850" height="140" border="0" align="center" cellspacing="5"  id="NoteTable">
       <tr>
           <td height="22" align="right">采购单号</td>
           <td><?php echo $PurchaseID;?></td>
         </tr>
           <tr>
			<td width="152" height="22" valign="middle" scope="col" align="right">默认采购</td>
			<td valign="middle" scope="col"><?php echo $Name;?></td>
		</tr>
        <tr>
			<td width="152" height="22" valign="middle" scope="col" align="right">下单日期</td>
			<td valign="middle" scope="col"><?php echo $Date?></td>
		</tr>
         <tr>
			<td width="152" height="22" valign="middle" scope="col" align="right">请款类型</td>
			<td valign="middle" scope="col">
            <?php
		//	include "../model/subselect/nonbom11Type.php";
				$TypSelect="<select name='TypeId' id='TypeId' style='width:400px' dataType='Require' msg='未选择' onchange='ChangeType()'>
             <option value='' selected>--请选择--</option>";
               $TypeResult = mysql_query("SELECT A.Id,A.Name FROM $DataPublic.nonbom11_type A WHERE A.Estate=1 AND Id!=1 ORDER BY A.Id",$link_id);
             if($TypeRow = mysql_fetch_array($TypeResult)){
		      do{
			         $TypeName = $TypeRow["Name"];
		           	$theId=$TypeRow["Id"];
			        $TypSelect.="<option value='$theId'>$TypeName</option>";					
			     }while ($TypeRow = mysql_fetch_array($TypeResult));
		        $TypSelect.="</select>&nbsp;";
		        echo $TypSelect;
	         }
            ?>
            </td>
		</tr>
         <tr>
			<td width="152" height="22" valign="middle" scope="col" align="right">请款月份</td>
			<td valign="middle" scope="col"><INPUT name="qkMonth" class=textfield id="qkMonth" value="<?php echo date("Y-m")?>" style="width: 400px;" onfocus="WdatePicker({dateFmt:'yyyy-MM'})" datatype="Require" msg="未填写" readonly></td>
		</tr>
        <tr>
          <td height="22" valign="top" scope="col" align="right">请款备注</td>
          <td valign="middle" scope="col"><textarea name="Remark" rows="3" id="Remark" style="width: 400px;color: #009900;"></textarea></td>
        </tr>
        

        
        <tr>
			<td width="152" height="22" valign="top" scope="col" align="right">请款内容</td>
			<td valign="middle" scope="col"><table width="400" border="0" cellpadding="0" cellspacing="0">
			  <tr align="center">
			    <td width="100" height="20" bgcolor="#D6FEFF" class="A1111">项目</td>
			    <td width="100" align="center" bgcolor="#D6FEFF" class="A1101">金额</td>
			    <td width="100" align="center" bgcolor="#D6FEFF" class="A1101">未请款</td>
			    <td width="100" align="center" bgcolor="#D6FEFF" class="A1101">本次请款</td>
		      </tr>
			  <tr align="right">
			    <td height="20" class="A0111" align="left">货款</td>
			    <td class="A0101"><?php echo $hkAmount;?></td>
			    <td class="A0101"><input name="wqHK" type="text" id="wqHK" class="A0000" style="width: 90px; color:#F90" value="<?php echo $wqHK;?>" readonly="readonly"/></td>
			    <td class="A0101"><input name="qkHK" type="text" id="qkHK" style="width: 90px;" value="<?php echo $wqHK;?>" dataType="Currency" msg="格式不符或不符合条件"  onchange="chkAmount(1)"/></td>
		      </tr>
              
	      </table></td>
		</tr>
        <tr>
          <td height="22" valign="top" scope="col" align="right">&nbsp;</td>
          <td valign="middle" scope="col">&nbsp;<?php echo $LockSTR;?></td>
        </tr>
      </table>

<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
   <tr><td width="30"  bgcolor="#FFFFFF" >&nbsp;</td><td colspan="7" valign="bottom"><span class='redB'>1.已采购配件明细</span></td><td width="30"  bgcolor="#FFFFFF">&nbsp;</td></tr>
<tr bgcolor='<?php  echo $Title_bgcolor?>'>
		<td width="30"  bgcolor="#FFFFFF" height="25">&nbsp;</td>
		 <td class="A1111" width="60" align="center">序号</td>
          <td class="A1101" width="310" align="center">配件名称</td>
          <td class="A1101" width="60" align="center">单价</td>
          <td class="A1101" width="60" align="center">申购数量</td>
          <td class="A1101" width="40" align="center">单位</td>
          <td class="A1101" width="60" align="center">金额</td>
          <td class="A1101" width="280" align="center">申购备注</td>
		<td width="30"  bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr>
		<td width="30"  height="120">&nbsp;</td>
		<td colspan="7" align="center" class="A0111" id="ShowInfo">	
                    <div style='width:890;height:100%;overflow-x:hidden;overflow-y:scroll'>
                   <table width='100%' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' id='NoteTable'>
                   <?php
                     $sListResult = mysql_query("SELECT   F.Id,F.GoodsId,F.Qty,F.Price,F.Remark,D.GoodsName,D.Unit,F.qkId
                      FROM $DataIn.nonbom6_cgsheet F 
		              LEFT JOIN $DataPublic.nonbom4_goodsdata D ON D.GoodsId=F.GoodsId 
                     WHERE  F.Mid='$Mid'",$link_id);
                        $i=1;
                        while($ListRows= mysql_fetch_array($sListResult)){
                                               $cgId=$ListRows["Id"];
                                               $qkId=$ListRows["qkId"];
                                               $GoodsName=$ListRows["GoodsName"];
                                               $GoodsId=$ListRows["GoodsId"];
                                               $Qty=$ListRows["Qty"];
                                               $Price=$ListRows["Price"];
                                               $Remark=$ListRows["Remark"];
                                               $Unit=$ListRows["Unit"];$Amount=$Price*$Qty;
                                              if($qkId>0){
                                                     $disabledStr="disabled";
                                                  } 
                                               else  $disabledStr="";
                                               echo"<tr bgcolor='$theDefaultColor'>";
                                               echo"<td  align='center' width='59' class='A0101' height='30'><input name='PayCgId[]' type='checkbox' id='PayCgId$i' value='$cgId' onclick='ChangAmount()' $disabledStr ></td>";
                                               echo"<td  width='310' class='A0101'>$GoodsName</td>";
                                               echo"<td  align='right' width='60' class='A0101'>$Price</td>";
                                               echo"<td   align='right' width='60' class='A0101'>$Qty</td>";
                                               echo"<td  align='center' width='40' class='A0101'>$Unit</td>";
                                               echo"<td align='right'  width='60' class='A0101'><input name='cgAmount[]' id='cgAmount$i' type='text' size='6' value='$Amount' readonly></td>";
                                               echo"<td  width='279' class='A0100'>$Remark</td>";
                                               echo"</tr>";
                                       $i++;
                                  }
                     ?>
                 </table></div>
		</td>
		<td width="30" >&nbsp;</td>
	</tr>
</table>

</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script>
function CheckForm(){
         var MessageStr="";
         var qkHK= document.getElementById("qkHK").value;
         var qkMonth= document.getElementById("qkMonth").value;
         var TypeId= document.getElementById("TypeId").value;
          if(TypeId==""){
               MessageStr="未选择请款类型!";
             }
          if(qkMonth==""){
               MessageStr="未选择请款月份!";
             }
          if(qkHK<=0 && TypeId!=4){
               MessageStr="请输入请款金额!";
             }
        if(qkHK<=0 && TypeId==4){
               MessageStr="你选择的是分批款,请选择分批的明细";
              }
        if(MessageStr==""){
		     document.form1.action="nonbom6_updated.php?TypeId="+TypeId+"&qkHK="+qkHK+"&qkMonth="+qkMonth;
		     document.form1.submit();
           }
      else{
             alert(MessageStr);return false;
            }
}
function chkAmount(AmountFrom){
	var Message="";
	switch(AmountFrom){
		case 1://货款
			var wqHK_TEMP=document.form1.wqHK.value*1;	//可请款最大值
			var qkHK_TEMP=document.form1.qkHK.value*1;	//当前请款值
			//先检查
			var CheckSTR=fucCheckNUM(qkHK_TEMP,"Price");
			if(CheckSTR==0 || qkHK_TEMP>wqHK_TEMP){
				Message="不是规范或不允许的值！"+qkHK_TEMP+" "+wqHK_TEMP;
				}
			if(Message!=""){
				alert(Message);
				document.form1.qkHK.value=wqHK_TEMP;
				return false;
				}
		break;
		}	
	}

function  ChangeType(){
var TypeId=document.getElementById("TypeId").value;
var wqHK=document.getElementById("wqHK").value;
      var  PayCgId=document.getElementsByName("PayCgId[]");
      if(TypeId==4){
           document.getElementById("qkHK").value=0;
          document.getElementById("qkHK").disabled=true;
            for(k=0;k<PayCgId.length;k++){
                            PayCgId[k].disabled=false;
                   }
          }
      else{
             document.getElementById("qkHK").value=wqHK;
              document.getElementById("qkHK").disabled=false;
            for(k=0;k<PayCgId.length;k++){
                            PayCgId[k].disabled=true;
                            PayCgId[k].checked=false;
                   }
            }
}
function ChangAmount(){
   var TypeId=document.getElementById("TypeId").value;
if(TypeId==4){
      var totalAmount=0;
      var  PayCgId=document.getElementsByName("PayCgId[]");
      var  cgAmount=document.getElementsByName("cgAmount[]");
            for(k=0;k<PayCgId.length;k++){
                if(PayCgId[k].checked){
                         totalAmount+=parseFloat(cgAmount[k].value); 
                       }
          }
      document.getElementById("qkHK").value=totalAmount;
   }
}
</script>
