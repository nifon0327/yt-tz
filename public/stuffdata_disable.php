<?php 
//$DataIn.电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 配件禁用");//需处理
$nowWebPage =$funFrom."_disable";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if($Id!=""){
		$Ids=$Ids==""?$Id:($Ids.",".$Id);
		}
	}

//步骤4：
$tableWidth=950;$tableMenuS=700;
$CheckFormURL="thisPage";
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,ActionId,107,fromWebPage,$fromWebPage";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">


	<table width="850" border="0" align="center" cellspacing="5"><input  type="hidden" id="Ids" name="Ids" value="<?php  echo $Ids?>">
      <tr><td colspan="2" >
             <table border="0" cellpadding="0" cellspacing="0">
              <tr><td class="A1110" width="80" align="center">配件ID</td><td class="A1110" width="300" align="center">配件名称</td><td class="A1110" width="80" align="center">可用库存</td><td class="A1110" width="80" align="center">在库</td><td class="A1110" width="80" align="center">报废</td><td class="A1110" width="80" align="center">报废类型</td><td class="A1111" width="180" align="center">报废原因</td></tr>
             <?php
         if($Ids!=""){
			$Ck8_Sql = "SELECT Id,TypeName FROM  $DataPublic.ck8_bftype WHERE 1 AND Estate=1  ";
			$Ck8_Result = mysql_query($Ck8_Sql); 
			$bfTypeStr= "<select name='bfTypeArray[]' id='bfTypeArray'<option value=''>请选择</option>";
			while ( $PD_Myrow = mysql_fetch_array($Ck8_Result)){
				$TypeId=$PD_Myrow["Id"];
				$TypeName=$PD_Myrow["TypeName"];
				if($TypeId==$Type){
					$bfTypeStr.= "<option value='$TypeId' selected>$TypeName</option>";
					}
				else{
					$bfTypeStr.= "<option value='$TypeId'>$TypeName</option>";
					}
				}
            $bfTypeStr.="</select>";
             $upResult =mysql_query("SELECT S.StuffId,S.StuffCname,S.TypeId,S.Picture,S.Jobid,S.Spec,S.Weight,S.Price,S.Remark,S.SendFloor,S.CheckSign,
             B.BuyerId,B.CompanyId,K.tStockQty,K.oStockQty
             FROM $DataIn.stuffdata S 
             LEFT JOIN $DataIn.bps B ON S.StuffId=B.StuffId 
            LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=S.StuffId
             WHERE S.Id  IN ($Ids)",$link_id);
             if($upData=mysql_fetch_array($upResult )){
              do{
                     $StuffId=$upData["StuffId"];
                     $StuffCname=$upData["StuffCname"];
                     $tStockQty=$upData["tStockQty"];
                     $oStockQty=$upData["oStockQty"];
                    if($tStockQty==0 ||$oStockQty==0 ){
                        $bfStr="&nbsp;";$bfQty=0;
                       }
                   else {
                        if($tStockQty>$oStockQty)$bfQty=$oStockQty;
                         else $bfQty=$tStockQty;
                         $bfStr="<span class='redB'>$bfQty</span>";
                       }

                 echo "<tr><td class='A0111' align='center' height='30'>$StuffId</td><td class='A0101'>$StuffCname</td><td class='A0101' align='center'>$oStockQty</td><td class='A0101' align='center'>$tStockQty</td><td class='A0101' align='center'>$bfStr</td><td class='A0101' align='center'>$bfTypeStr</td><td class='A0101' align='center'><input type='text' id='bfReasonArray' name='bfReasonArray[]' size='22'></td><input type='hidden' id='StuffCname' name='StuffCname[]' value='$StuffCname'><input type='hidden' id='StuffIdArray' name='StuffIdArray[]' value='$StuffId'><input type='hidden' id='bfQtyArray' name='bfQtyArray[]' value='$bfQty'></tr>";
                 }while($upData=mysql_fetch_array($upResult ));
             }
         }
else{
       echo "<tr><td colspan='4' class='A1111'></td></tr>";
           }
              ?>
            </table>
       </td>
      </tr>
		  <tr>
            <td width="80" align="center" scope="col" height="40">禁用原因:</td>
            <td scope="col"><select name="Reason" id="Reason" style="width:220px;" onchange="otherCauseClick(this)"  dataType="Require"  msg="未选择不良原因" >
	                    <option value="" selected>请选择</option>
	                    <option value="客户换包装" >客户换包装</option>
						<option value="一年未下单">一年未下单</option>
						<option value="配件名重复/备品转入">配件名重复/备品转入</option>
						<option value="0">其他</option></select>
				<input name="otherCause" type="hidden" id="otherCause" value=""></td>
          </tr> 
		         
       
        </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script language="javascript">
function otherCauseClick(ee){
      
    var oCause=document.getElementById("otherCause");
    if (ee.value=="0"){
       var inputStr=prompt("请输入其它原因",oCause.value);
       if (inputStr==null){
            ee.options[0].selected = true;
            return false;
           }
       else{
           inputStr=inputStr.replace(/(^\s*)|(\s*$)/g,""); 
           if (inputStr==""){
               oCause.value="";
               ee.options[0].selected = true; 
               }
           else{
               var n = ee.selectedIndex; 
               ee.options[n].text ="其它原因:"+ inputStr;   
               ee.options[n].value ="其它原因:"+ inputStr; 
               oCause.value=inputStr;       
           } 
       
         }
    }
}


function  CheckForm(){
      var Reason=document.getElementById("Reason").value;
       if(Reason==""){
           alert("未填写禁用原因!");return false;
        }
     var tempStr="";
     var StuffCnameArray=document.getElementsByName("StuffCname[]");
      var bfQtyArray=document.getElementsByName("bfQtyArray[]");
       for(var k=0;k<bfQtyArray.length;k++){
           if(bfQtyArray[k].value>0){
                  if(tempStr=="")tempStr ="配件："+StuffCnameArray[k].value+"的报废数量/"+bfQtyArray[k].value +"\n";
                  else tempStr=tempStr+"配件："+StuffCnameArray[k].value+"的报废数量/"+bfQtyArray[k].value +"\n";
               }
        }
          if(tempStr!="")alert(tempStr);
           document.form1.action="stuffdata_updated.php";
           document.form1.submit();
}
</script>