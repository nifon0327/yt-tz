<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
//解密
$fArray=explode("|",$f);
$RuleStr1=$fArray[0];
$EncryptStr1=$fArray[1];
$Id=anmaOut($RuleStr1,$EncryptStr1,"f");
if ($Sign==1){
	 $thTableMain="ck12_thmain";
	 $thTableSheet="ck12_thsheet ";
	 $thTableReview="ck12_threview";
}
else{
     $thTableMain="ck2_thmain";
	 $thTableSheet="ck2_thsheet ";	
	 $thTableReview="ck2_threview";
}

if($Id!=""){
	$StockResult = mysql_query("SELECT 
	M.BillNumber,M.Date,
	P.Forshort,I.Tel,I.Fax
	FROM $DataIn.$thTableMain M 
	LEFT JOIN $DataIn.trade_object P ON M.CompanyId =P.CompanyId 
	LEFT JOIN $DataIn.companyinfo I ON I.CompanyId=P.CompanyId
	WHERE M.Id='$Id' ",$link_id);
if ($StockResult){
	if ($myrow = mysql_fetch_array($StockResult)) {
		$BillNumber=$myrow["BillNumber"];
		$Date=$myrow["Date"];
		$Forshort=$myrow["Forshort"];
		$Tel=$myrow["Tel"]==""?"":"电话：".$myrow["Tel"];
		$Fax=$myrow["Fax"]==""?"":"传真：".$myrow["Fax"];
		}
}
include "../model/subprogram/mycompany_info.php";
?>
<style type="text/css">
<!--
body {
	margin-top: 0px;
}
-->
</style><body>
<table style="width:770px;height:500px" border=0 cellpadding=0 cellspacing=0>
	<tr>
		
		<td width="130" style="height:45px" align="right">&nbsp;</td>
		<td width="510" align="center"><div class="TitleModel">黑 云 退 料 单</div></td>
		<td width="100" align="right" valign="bottom" class="A0000">NO.<?php  echo $BillNumber?></td>
		<td width="30" class="A0000" align="center" valign="middle">&nbsp;</td>
    </tr>
  	<tr>
    	<td colspan="2" style="height:20px">供 应 商：<?php  echo $Forshort?>&nbsp;&nbsp;<?php  echo $Tel?>&nbsp;&nbsp;<?php  echo $Fax?></td>
    	<td align="right">日期：<?php  echo $Date?></td>
		<td class="A0000" align="center" valign="middle">&nbsp;</td>
  	</tr>
    <tr align="left" valign="top">
      	<td colspan=3>
			<table width="100%" border=0 cellpadding=0 cellspacing=0 style="TABLE-LAYOUT: fixed; WORD-WRAP: break-word">
			  <tr>
				<td style="width:40px;height:24px" class="A1111" align="center">序号</td>
				<td class="A1101" style="width:340px" align="center">配件Id/配件名称</td>
				<td style="width:60px" class="A1101" align="center">退换数量</td>
				<td style="width:300px" class="A1101" align="center">原因</td>
			  </tr>
			    <?php 
				$Result = mysql_query("SELECT S.StuffId,S.Qty,S.Remark,S.Estate,D.StuffCname,S.Id 
                FROM $DataIn.$thTableSheet S
                LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
                WHERE S.StuffId=D.StuffId AND S.MId='$Id' ORDER BY D.StuffCname",$link_id);
				$k=1;$tempthEstate=0;
				if($myRow = mysql_fetch_array($Result)){		
					do{
                        $Id=$myRow["Id"];
						$StuffId=$myRow["StuffId"];
						$StuffCname=$myRow["StuffCname"];
						$Qty=$myRow["Qty"];
						$Remark=$myRow["Remark"];
						
			            $checkThSql=mysql_query("SELECT R.Estate,R.Remark  FROM $DataIn.$thTableReview R WHERE R.Mid='$Id' LIMIT 1",$link_id);		
			            if(!$checkThRows = mysql_fetch_array($checkThSql)){
                                $tempthEstate++;
                           }
						$EstateColor=$myRow["Estate"]>1?" bgcolor='#FF0000'":"";
					?>
					  <tr <?php  echo $EstateColor;?>>
						<td align="center" class="A0111" style="height:24px"><?php  echo $k;?></td>
						<td class="A0101" >&nbsp;<?php  echo $StuffId?>/<?php  echo $StuffCname;?></td>
						<td class="A0101" align="right"><?php  echo $Qty;?>&nbsp;</td>
						<td class="A0101">&nbsp;<?php  echo $Remark;?></td>
					  </tr>
		  			<?php 
					$k++;
					}while ($myRow = mysql_fetch_array($Result));
				}//end if 
			for($i=$k;$i<16;$i++){
				echo"
				<tr>
						<td align='center' class='A0111' style='height:24px'>$i</td>
						<td class='A0101' >&nbsp;</td>
						<td class='A0101' align='right'>&nbsp;</td>
						<td class='A0101'>&nbsp;</td>
					  </tr>
				";
				}
			?>
           </table>
		</td>
		<td class="A0000" align="center" valign="middle">①<br>联<br> 存<br>档<br> <p>②<br>联<br> 供<br>应<br>商</td>
    </tr>
    <tr valign="top">
	  <td class="A0000"  valign="top">签收人：<?php  
      if($tempthEstate==0)echo $Forshort;
       ?></td>
      <td colspan="2" align="right" class="A0000"  valign="top">
	  <?php 
	  $S_Tel=$ExtNo!=""?$S_Tel."-".$ExtNo:$S_Tel;
	  $S_Fax=$ExtNo!=""?$S_Fax."-".$ExtNo:$S_Fax;
	 echo $S_Company."<br>电话:".$S_Tel."-".$ExtNo." 传真:".$S_Fax."<br>".$S_Address."邮政编码:".$S_ZIP;
	  ?></td>
	  <td class="A0000" align="center" valign="middle">&nbsp;</td>
  </tr>
</table>
</body>
</html>
<?php 
	}
else{
	echo "读取数据错误!";
	}
?>