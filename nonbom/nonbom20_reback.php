<?php 
include "../model/modelhead.php";
//include "nonbom_function.php";
ChangeWtitle("$SubCompany 非bom配件退回仓库");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_reback";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT B.GoodsId,B.GoodsName,B.BarCode,B.Unit,D.TypeName
FROM $DataPublic.nonbom4_goodsdata B 
LEFT JOIN $DataPublic.nonbom5_goodsstock C ON C.GoodsId=B.GoodsId	
LEFT JOIN $DataPublic.nonbom2_subtype D ON D.Id=B.TypeId
WHERE B.GoodsId=$Id",$link_id));
$GoodsId=$upData["GoodsId"];
$GoodsName=$upData["GoodsName"];
$TypeName=$upData["TypeName"];
$Attached=$upData["Attached"];
$Unit=$upData["Unit"];
$lyResult=mysql_fetch_array(mysql_query("SELECT  IFNULL(SUM(Qty),0) AS Qty  FROM $DataIn.nonbom8_outsheet  WHERE  GoodsId=$GoodsId AND GetNumber='$Login_P_Number'",$link_id));
$Qty=$lyResult["Qty"];
$lyResult=mysql_fetch_array(mysql_query("SELECT  IFNULL(SUM(Qty),0) AS lyQty  FROM $DataIn.nonbom8_outsheet  WHERE  GoodsId=$GoodsId 
AND GetNumber='$Login_P_Number' AND Estate=0",$link_id));
$lyQty=$lyResult["lyQty"];
$backResult=mysql_fetch_array(mysql_query("SELECT  IFNULL(SUM(Qty),0) AS backQty  FROM $DataIn.nonbom8_reback  WHERE  GoodsId=$GoodsId 
AND BackNumber='$Login_P_Number'",$link_id));
$backQty=$backResult["backQty"];

$bfResult=mysql_fetch_array(mysql_query("SELECT  IFNULL(SUM(Qty),0) AS bfQty  FROM $DataIn.nonbom8_bf  WHERE  GoodsId=$GoodsId 
AND bfNumber='$Login_P_Number'",$link_id));
$bfQty=$bfResult["bfQty"];

$InResult=mysql_fetch_array(mysql_query("SELECT  IFNULL(SUM(Qty),0) AS InQty  FROM $DataIn.nonbom8_turn  WHERE  GoodsId=$GoodsId 
AND InNumber='$Login_P_Number' AND Estate=1",$link_id));
$InQty=$InResult["InQty"];

$OutResult=mysql_fetch_array(mysql_query("SELECT  IFNULL(SUM(Qty),0) AS OutQty  FROM $DataIn.nonbom8_turn  WHERE  GoodsId=$GoodsId 
AND OutNumber='$Login_P_Number'",$link_id));
$OutQty=$OutResult["OutQty"];

$MaxQty=$lyQty-$backQty-$bfQty+$InQty-$OutQty;

$PropertyResult=mysql_query("SELECT Id FROM $DataPublic.nonbom4_goodsproperty WHERE GoodsId=$GoodsId AND Property=7",$link_id);  
 if($PropertyRow=mysql_fetch_array($PropertyResult)){
          $PropertySign=1;
          $Info="<span class='redB'>(属性为固定资产，必须选择退回的领用固定资产记录)</span>";
      }
else {
         $PropertySign=0;
         $Info="<span class='redB'>(属性为非固定资产，直接保存即可)</span>";
          }

 $CheckFormURL="thisPage";
$tableWidth=770;$tableMenuS=600;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,GoodsId,$GoodsId,ActionId,157";
//步骤5：//需处理
?>

<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
      <table width="100%"  border="0" align="center" cellspacing="0">
		<tr>
			<td align="right" valign="middle" scope="col" width="200" >非BOM配件名称：</td>
			<td valign="middle" scope="col" ><?php echo $GoodsName?><span class="redB">(领用配件退回仓库处理)</span><input type="hidden" id="SIdList" name="SIdList"><input  id="PropertySign" name="PropertySign" type="hidden" value="<?php echo $PropertySign?>"></td>
		</tr>
		<tr>
		  <td align="right">配件ID：</td>
		  <td ><?php echo $GoodsId?></td>
	    </tr>
		<tr>
		  <td align="right">单位：</td>
		  <td ><?php echo $Unit;?></td>
	    </tr>
        <tr>
			<td align="right" valign="middle" scope="col">申领总数：</td>
			<td valign="middle" scope="col" ><?php echo $Qty;?></td>
		</tr>

        <tr>
			<td align="right" valign="middle" scope="col">发放总数：</td>
			<td valign="middle" scope="col" ><?php echo $lyQty;?></td>
		</tr>

        <tr>
			<td align="right" valign="middle" scope="col">报废总数：</td>
			<td valign="middle" scope="col"><?php echo $bfQty;?></td>
		</tr>

      <tr>
			<td align="right" valign="middle" scope="col">转入总数：</td>
			<td valign="middle" scope="col" ><?php echo $InQty;?></td>
		</tr>

     <tr>
			<td align="right" valign="middle" scope="col">转出总数：</td>
			<td valign="middle" scope="col" ><?php echo $OutQty;?></td>
		</tr>

       <tr>
			<td align="right" valign="middle" scope="col">已退回数：</td>
			<td valign="middle" scope="col" ><?php echo $backQty;?></td>
		</tr>

       <tr>
			<td align="right" valign="middle" scope="col">剩余数量：</td>
			<td valign="middle" scope="col" class="greenB"><?php echo $MaxQty;?></td>
		</tr>


       <tr>
			<td align="right" valign="middle" scope="col">本次退回：</td>
			<td valign="middle" scope="col" ><input  id="thisthQty" name="thisthQty" type="text" size="14" onblur="CheckNum(this,<?php echo $MaxQty?>)"></td>
		</tr>

		<tr>
		  <td align="right"  height="30">单 &nbsp;&nbsp;&nbsp;据：</td>
		  <td scope="col"><input name="Attached" type="file" id="Attached" size="52" DataType="Filter" Accept="jpg" Msg="文件格式不对,请重选" Row="5" Cel="1"></td>
	    </tr>
        <tr>
          <td  align="right" valign="top">备注：</td>
          <td ><textarea name="Remark" rows="3" id="Remark" style="width: 300px;" dataType='Require' msg='未填写'></textarea></td>
        </tr>
	  </table>
</td></tr></table>


<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
 <tr><td  class="A0010">&nbsp;</td><td colspan="5" height="30" ><span class='blueB' >1.非BOM固定资产个人领用明细</span></td><td  class="A0001">&nbsp;</td></tr>
<tr bgcolor='<?php  echo $Title_bgcolor?>'>
		<td width="80" class="A0010" bgcolor="#FFFFFF" height="25">&nbsp;</td>
		<td class="A1111" width="50" align="center">操作</td>
		 <td class="A1101" width="30" align="center">序号</td>
          <td class="A1101" width="170" align="center">固定条码</td>
          <td class="A1101" width="160" align="center">资产编号</td>
          <td class="A1101" width="100" align="center">状态</td>
		<td width="80" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr>
		<td width="80" class="A0010" height="250">&nbsp;</td>
		<td colspan="5" align="center" class="A0111" id="ShowInfo">	
                    <div style='width:620;height:100%;overflow-x:hidden;overflow-y:scroll'>
                   <table width='100%' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' id='NoteTable'>
                   <?php
        $i=1;
        $sListResult = mysql_query("SELECT C.BarCode,C.GoodsNum,C.Picture  FROM  $DataIn.nonbom7_code C  WHERE   C.GoodsId=$GoodsId AND Number='$Login_P_Number'",$link_id);
        while($ListRows= mysql_fetch_array($sListResult)){
              $BarCode=$ListRows["BarCode"];
              $GoodsNum=$ListRows["GoodsNum"];
              $Picture=$ListRows["Picture"];
		      $Dir=anmaIn("download/nonbomCode/",$SinkOrder,$motherSTR);
               if($Picture!=""){
                      $BarCode="<span onClick='OpenOrLoad(\"$Dir\",\"$Picture\")'  style='CURSOR: pointer;color:#FF6633'>$BarCode</span>";
                  }

            echo"<tr bgcolor='$theDefaultColor'><td  align='center' height='20' width='50' class='A0101'><input name='backCode[]' type='checkbox' id='backCode$i' value='$BarCode'  $disabledStr ></td>";
           echo"<td  align='center' width='30' class='A0101'>$i</td>";
           echo"<td  align='center' width='170' class='A0101'>$BarCode</td>";
           echo"<td  align='center' width='160' class='A0101'>$GoodsNum</td>";
            echo"<td align='center'   width='100' class='A0100'>$EstateStr</td>";
           echo"</tr>";
           $i++;
       }
       ?>
                 </table></div>
		</td>
		<td width="80" class="A0001">&nbsp;</td>
	</tr>
</table>
<?php
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script>
function  CheckNum(e,MaxQty){
           var thisQty=e.value;
          if(thisQty>MaxQty){
               alert("超出范围!");
               e.value="";
                 return false;
          }
}

function  CheckForm(){
        var Message="";
      var PropertySign=document.getElementById("PropertySign").value;
       var thisQty=document.getElementById("thisthQty").value;
        var Remark=document.getElementById("Remark").value;
       if(thisQty<=0){
                Message="请输入退回数量";  
           }
       /*   if(Remark==""){
                Message="请输入退回原因";
            }*/
       var chooseSign=0;
       var backCode=document.getElementsByName("backCode[]");
       for(k=0;k<backCode.length;k++){
             if(backCode[k].checked){
                         chooseSign++;
                 }
            }
    if(chooseSign!=thisQty && PropertySign==1){//是固定资产
                    Message="选择退回仓库的领用资产和输入数量不一致!"; 
              }

       if(Message==""){
		     document.form1.action="nonbom20_updated.php";
		     document.form1.submit();
        }
     else{
        alert(Message);
        }
}
</script>

