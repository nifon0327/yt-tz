<?php   
//电信-zxq 2012-08-01
//步骤1  $DataIn.trade_object / $DataIn.yw3_pimodel 二合一已理锌板 
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增PI");//需处理
$nowWebPage =$funFrom."_topi";	
$toWebPage  =$funFrom."_pitopdf";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=1000;
$tableMenuS=800;
$SaveSTR="NO";$ResetSTR="NO";
include "../model/subprogram/add_model_t.php";
$SaveSTR="";
//步骤4：需处理
//读取产品数据
$Ids="";
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if ($Id!=""){
		$Ids=$Ids==""?$Id:($Ids.",".$Id);
		}
	}

//取得CompanyId       
$CompResult = mysql_query("SELECT M.CompanyId  FROM $DataIn.yw1_ordersheet S 
             LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
             WHERE S.Id IN ($Ids) LIMIT 1",$link_id); 
$sCompanyId=mysql_result($CompResult,0,"CompanyId");

//检查PI文件名
if ($sCompanyId!=""){
   if($sCompanyId!=1074){
             $MaxPISql="SELECT MAX(SUBSTRING(PI,LOCATE('PI',PI)+2)) AS maxPI  FROM $DataIn.yw3_pisheet  WHERE CompanyId='$sCompanyId'  AND Date>='2011-01-01'";
              $LastPISql="SELECT LEFT(PI,LOCATE('PI',PI)+1) AS PrePIStr,Paymentterm  FROM $DataIn.yw3_pisheet  WHERE CompanyId='$sCompanyId' ORDER BY Date DESC LIMIT 1";
               }
      else {//专为SAS 编写
              $MaxPISql="SELECT MAX(SUBSTRING(PI,LOCATE('SAS',PI)+4)) AS maxPI  FROM $DataIn.yw3_pisheet  WHERE CompanyId='$sCompanyId'  AND Date>='2011-01-01'";
             $LastPISql="SELECT LEFT(PI,LOCATE('SAS',PI)+3) AS PrePIStr,Paymentterm  FROM $DataIn.yw3_pisheet  WHERE CompanyId='$sCompanyId' ORDER BY Date DESC LIMIT 1";
              }
  $PIResult =mysql_fetch_array(mysql_query($MaxPISql,$link_id));
    $MaxPI=$PIResult["maxPI"];
    $LastPIResult =mysql_fetch_array(mysql_query($LastPISql,$link_id));
    $PrePIStr=$LastPIResult["PrePIStr"];
    $Paymentterm=$LastPIResult["Paymentterm"];
    if ($MaxPI>0){
       $MaxPI=$MaxPI+1;  
    }
    else{
       $MaxPI=date('Y') . "001";   
    }
    $newPI=$PrePIStr . $MaxPI;
    
    $checkPayment=mysql_query("SELECT B.eName FROM $DataIn.trade_object A LEFT JOIN $DataPublic.clientpaymode B ON B.Id=A.PayMode WHERE A.CompanyId='$sCompanyId' LIMIT 1",$link_id);
     if($checkPaymentRow = mysql_fetch_array($checkPayment)){
           $Paymentterm=$checkPaymentRow["eName"];
     }
   // echo $newPI;
} 
?>
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
    	<td colspan="2" class='A0011'><input name="Ids" type="hidden" id="Ids" value="<?php    echo $Ids?>"></td>
	</tr>
    <tr>
    	<td width="300" height="35" valign="middle" class='A0010' align="right">客&nbsp;&nbsp;&nbsp;&nbsp;户：
      </td>
	    <td valign="middle" class='A0001'>
			<select name="CompanyId" id="CompanyId" style="width: 400px;" dataType="Require"  msg="未选择或该客户无需PI">
			<?php   
			$checkSql = "SELECT C.Letter,C.CompanyId,C.Forshort FROM $DataIn.trade_object C  
			WHERE  C.Estate=1 AND C.ObjectSign IN (1,2) ORDER BY C.Letter"; 
			$checkResult = mysql_query($checkSql);
			if($checkRow = mysql_fetch_array($checkResult)){
				echo "<option value='' selected>-Select-</option>";
				do{
					$CompanyId=$checkRow["CompanyId"];
					$Letter=$checkRow["Letter"];
					$Forshort=$checkRow["Forshort"];
                                        if ($CompanyId==$sCompanyId){
                                            echo "<option value='$CompanyId' selected>$Letter-$Forshort</option>"; 
                                            }
                                        else{
                                            echo "<option value='$CompanyId'>$Letter-$Forshort</option>";  
                                           }
				
				    }while($checkRow = mysql_fetch_array($checkResult));
				}
			?>		 
		  </select>
		</td>
    </tr>
    <tr>
    	<td height="43" valign="middle" class='A0010' align="right">PI文件名：</td>
	    <td valign="middle" class='A0001'><input name="PI" type="text" id="PI" style="width: 400px;" value="<?php    echo $newPI?>" dataType="Require" msg="没有填写PI文件名"></td>
    </tr>
    <tr align="center">
      <td height="47" colspan="2" align="middle"  class='A0011'>注意：PI文件禁止使用中文或中文标点符号</td>
    </tr> 
    <tr>
      <td height="47" valign="middle" class='A0010' align="right">PaymentTerm： </td>
      <td valign="middle" class='A0001'><input name="PaymentTerm" type="text" id="PaymentTerm"  value="<?php    echo $Paymentterm?>" style="width: 400px;" dataType="Require" msg="没有填写Payment term"></td>
    </tr>
    
    <tr>
    	<td height="25" align="right" class='A0010'>Notes：</td>
    	<td class='A0001'><textarea name="Notes" cols="39" style="width: 400px;" rows="3" id="Notes"></textarea></td>
    </tr>
    <tr>
    	<td height="25" align="right" class='A0010'>Terms：</td>
	<td class='A0001'><textarea name="Terms" cols="39" style="width: 400px;" rows="3" id="Terms"></textarea></td>
    </tr>  
   <?php  if ($sCompanyId==1088){?>
	   <tr>
	    	<td height="25" align="right" class='A0010'>ShipTo：</td>
		  <td class='A0001'><textarea name="ShipTo" cols="39" style="width: 400px;" rows="3" id="ShipTo"></textarea></td>
	    </tr>  
   <?php }?>  
  <tr>
      <td  height="45"  colspan="2" align="center" class='A0011'>请填写以下订单的PI交期</td>
  </tr>
  <tr>
      <td   colspan="2" align="center" class='A0011'>
         <table border="0" width="880" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
            <tr height='25'>
                <td  width='30' align='center' class='A1111'><b>序号</b></td>
                <td  width='80' align='center' class='A1101'><b>PO</b></td>
                <td  width='300' align='center' class='A1101'><b>产品名称</b></td>
                <td  width='50' align='center' class='A1101'><b>Qty</b></td>
                <td  width='200' align='center' class='A1101'><b>PI交期</b></td>
               <td  width='200' align='center' class='A1101'><b>Remark</b></td>
            </tr>
          <?php   
          $n=1;$diffFlag=0;
          $orderResult = mysql_query("SELECT S.Id,S.OrderPO,S.Qty,P.cName,PI.Leadtime,L.Leadtime AS Leadtime2,PI.Remark 
            FROM $DataIn.yw1_ordersheet S 
             LEFT JOIN $DataIn.productdata P ON S.ProductId=P.ProductId 
             LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id
             LEFT JOIN $DataIn.yw3_pileadtime L ON L.POrderId=S.POrderId 
             WHERE S.Id IN ($Ids) ",$link_id); 
          while($orderRow = mysql_fetch_array($orderResult)){
              $Id=$orderRow["Id"];
              $idTempName="Leadtime_" . $orderRow["Id"];
              $OrderPO=$orderRow["OrderPO"];
              $Qty=$orderRow["Qty"];
              $cName=$orderRow["cName"];
              $Leadtime=$orderRow["Leadtime"];
              $Leadtime2=$orderRow["Leadtime2"];
               $Remark=$orderRow["Remark"];
              if ($Leadtime==""){
                  $bgColor="#FFFFFF";
                   $Leadtime=$Leadtime2==""?"":$Leadtime2;
                  }
              else{
                  $bgColor="#FF0000";
                  $SaveSTR="NO";
              }
              
              if ($Leadtime==""  ||  $Leadtime=="0000-00-00"){
	                  $LeadWeek="";
              }
              else{
	                  $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$Leadtime',1) AS Weeks",$link_id));
                     $LeadWeek=$dateResult["Weeks"];
                     $LeadWeek=$LeadWeek>0?"Week ". substr($LeadWeek,4,2):"";
              }
             
              echo "<tr height='30'><td  width='30' align='center' class='A0111' style='background: $bgColor;'>$n</td>";
              echo "<td width='100' align='center' class='A0101'>$OrderPO</td>";
              echo "<td width='300' align='left' class='A0101'>$cName</td>";
              echo "<td width='50'  align='center' class='A0101'>$Qty</td>";
               echo "<td width='230' valign='middle' align='center' class='A0101'>
                    <input  type='text' value='$LeadWeek' style='width:100px;'  dataType='Require' msg='没有填写PI交期'   onclick='set_weekdate(this,$Id)' readonly>
                    <input name='$idTempName' id='$idTempName' type='hidden' value='$Leadtime' >
                    </td>";
              echo "<td width='200' valign='middle' align='center' class='A0101' style='width:100px;' >
                   <input name='Remark$Id' id='Remark$Id' type='text' value='$Remark' style='width:170px;color:#FF0000'>
               </td></tr>";//,onpicked:\"dateChange($Id,0)\"}
              if ($n==1){
                 $oldOrderPO=$OrderPO; 
              }else{
                 if ($oldOrderPO!=$OrderPO) {
                     $diffFlag=1;
                     $SaveSTR="NO";
                 }
              }    
              $n++;
          }
          if ($n==1){
              $SaveSTR="NO";
              echo "<tr height='50'><td  colspan='6' align='center'  class='A0111'><span style='color:#FF0000;font-weight:bold;'>数据读取有误！</span></td></tr>";
           }
          ?>   
         </table>    
      </td>
  </tr>
  <?php    if ($diffFlag==1){?>
    <tr align="center">
      <td height="35" colspan="2" align="middle"  class='A0011'><span style='color:#FF0000;font-weight:bold;'>注意：所选的订单PO不相同！</span></td>
    </tr> 
  <?php   }?>
</table>
<?php   
//步骤5：
$ResetSTR="NO";
include "../model/subprogram/add_model_b.php";
?>
<script src='../model/weekdate.js' type=text/javascript></script>
<script language="JavaScript" type="text/JavaScript">
var weekdate=new WeekDate();
function dateChange(index,sign){
     var LeadtimeName="Leadtime_"+index;
     var elLeadtime=document.getElementById(LeadtimeName);
     
     var LeaddayName="Leadday_"+index;
     var elLeadday=document.getElementById(LeaddayName);
     switch(sign){
	    case 0:
	       var Leadtime=elLeadtime.value;
	       if (Leadtime!=""){
	            var now=new Date();
		        Leadtime=new Date(Leadtime.replace(/\*/g,""));
		        elLeadday.value=parseInt((Leadtime-now)/(24*60*60*1000));
	       }
	       break;
	   case 1:
	        var days=parseInt(elLeadday.value);
	        var  Leadtime=new Date();
		     Leadtime.setDate(Leadtime.getDate()+days);
		     elLeadtime.value=Leadtime.getFullYear() + "-" + (Leadtime.getMonth() + 1) + "-" + Leadtime.getDate(); 
	      break;
    }
}

function set_weekdate(el,index){
	  var saveFun=function(){
			     if (weekdate.Value>0){
					       var tempWeeks=weekdate.Value.toString();
					       tempWeeks="Week "+tempWeeks.substr(4, 2);
						   var tempPIDate=weekdate.getFriday("-");
						   el.value=tempWeeks;
						   
						   var LeadtimeName="Leadtime_"+index;
                           document.getElementById(LeadtimeName).value=tempPIDate;
				}
		};
	   
	   weekdate.show(el,1,saveFun,"");
}
</script>