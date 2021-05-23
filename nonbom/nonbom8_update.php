<?php 
//EWEN 2013-02-26 OK
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新非bom配件申领记录");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT A.GoodsId,A.Qty,A.WorkAdd,A.Remark,A.Locks,B.GoodsName,B.BarCode,B.Unit,C.wStockQty,C.mStockQty,C.oStockQty,D.TypeName,A.Date 
	FROM $DataIn.nonbom8_outsheet A
	LEFT JOIN $DataPublic.nonbom4_goodsdata B ON B.GoodsId=A.GoodsId
	LEFT JOIN $DataPublic.nonbom5_goodsstock C ON C.GoodsId=A.GoodsId	
	LEFT JOIN $DataPublic.nonbom2_subtype D ON D.Id=B.TypeId
	WHERE A.Id='$Id' LIMIT 1",$link_id));
$GoodsId=$upData["GoodsId"];
$GoodsName=$upData["GoodsName"];
$Remark=$upData["Remark"];
$TypeName=$upData["TypeName"];
$Attached=$upData["Attached"];
$Date=$upData["Date"];
$Qty=$upData["Qty"];
$Unit=$upData["Unit"];
$Locks=$upData["Locks"];
$BarCode=$upData["BarCode"];
$wStockQty=$upData["wStockQty"];
$mStockQty=$upData["mStockQty"];
$oStockQty=$upData["oStockQty"];
$WorkAdd=$upData["WorkAdd"];
$BiggestQty=$mStockQty>$oStockQty?$mStockQty:$oStockQty;//原则上采购库存>=在库，更新需要两者同时有库存才可以操作
$minQty=0;
if($BiggestQty==0){//没有库存时
	$maxQty=$Qty+1;
	}
else{//有库存时
	$maxQty=$Qty+1+$BiggestQty;
	}
//读取申购未审核数量
$checkSql2=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Qty),0) AS Qty FROM $DataIn.nonbom6_cgsheet WHERE GoodsId='$GoodsId' AND Estate<>1",$link_id));
$checkQty=$checkSql2["Qty"];
if($Locks==0){
	$Info="<span class='redB'>记录锁定中.先请主管解锁后更新.</span>";
	$SaveSTR="NO";
	}
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,GoodsId,$GoodsId,oldQty,$Qty,OperatorSign,$OperatorSign,Estate,$Estate";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
      <table width="100%" height="250" border="0" align="center" cellspacing="0" id="NoteTable" >
		<tr>
			<td align="right" valign="middle" scope="col">非BOM配件名称</td>
			<td valign="middle" scope="col" class="yellowN"><?php echo $GoodsName;?></td>
		</tr>
        <tr>
		  <td align="right">类型</td>
		  <td class="yellowN"><?php echo $TypeName?></td>
	    </tr>
		<tr>
		  <td align="right">编号</td>
		  <td class="yellowN"><?php echo $GoodsId?></td>
	    </tr>
		<tr>
		  <td align="right">条码</td>
		  <td class="yellowN"><?php echo $BarCode?></td>
	    </tr>
		<tr>
		  <td align="right">单位</td>
		  <td class="yellowN"><?php echo $Unit;?></td>
	    </tr>
        <tr>
			<td align="right" valign="middle" scope="col">在库</td>
			<td valign="middle" scope="col" class="yellowN"><?php echo $wStockQty;?></td>
		</tr>
        <tr>
			<td align="right" valign="middle" scope="col">采购库存</td>
			<td valign="middle" scope="col" class="yellowN"><?php echo $oStockQty;?></td>
		</tr>
        <tr>
          <td align="right">最低库存</td>
          <td class="yellowN"><?php echo $mStockQty;?></td>
        </tr>
         <tr>
		  <td  align="right">申领日期</td>
		  <td ><input name="slDate" type="text" id="slDate" style="width: 380px;"    onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"  datatype='Require' value="<?php echo $Date?>" msg="没有填写或格式错误" readonly="readonly"/></td>
	    </tr>
         <tr>
           <td align="right">使用地点</td>
           <td ><select name='WorkAdd' id='WorkAdd' style='width:380px' dataType='Require' msg='未选择'><option value="" selected>--请选择--</option>
				 <?php 
                $checkResult = mysql_query("SELECT A.Id,A.Name FROM $DataPublic.staffworkadd A ORDER BY A.Name",$link_id);
                while($checkRow = mysql_fetch_array($checkResult)){
                    $TempId=$checkRow["Id"];
                    $TempName=$checkRow["Name"];
					if($WorkAdd==$TempId){
						echo"<option value='$TempId' selected>$TempName</option>";
						}
					else{
                    	echo"<option value='$TempId'>$TempName</option>";
						}
                    }
                ?>
             </select>
          </td>
         </tr>
        <tr>
			<td align="right" valign="middle" scope="col">申领数量</td>
			<td valign="middle" scope="col"><input name="Qty" type="text" id="Qty" style="width: 380px;" value="<?php echo $Qty;?>" dataType="Range" min="<?php echo $minQty;?>" max="<?php echo $maxQty;?>" msg="格式不符或超出范围"/></td>
		</tr>
        <tr>
          <td align="right" valign="top">申领备注</td>
          <td><textarea name="Remark" rows="3" id="Remark" style="width: 380px;" dataType='Require' msg='未填写'><?php echo $Remark;?></textarea></td>
        </tr>
        <tr>
          <td align="right" valign="top">&nbsp;</td>
          <td>&nbsp;<?php
		  echo $Info;
		  ?></td>
        </tr>
	  </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>