<?php 
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新配件条码");//需处理
$fromWebPage=$funFrom."_code";		
$nowWebPage =$funFrom."_upmain";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 

//步骤3：//需处理
$MidArray=explode("|", $Mid);
$rkId=$MidArray[0];
$GoodsId=$MidArray[1];
$upResult = mysql_query("SELECT A.Id,A.Mid,A.GoodsId,A.Qty,B.Bill,B.BillNumber,B.CompanyId,B.BuyerId,B.Remark,B.Date AS rkDate,B.Operator,C.GoodsName,C.Attached,C.CkId
                        FROM $DataIn.nonbom7_insheet A
                         LEFT JOIN $DataIn.nonbom7_inmain B ON B.Id=A.Mid 
                         LEFT JOIN $DataPublic.nonbom4_goodsdata C ON C.GoodsId=A.GoodsId 
						 WHERE  A.GoodsId='$GoodsId' AND A.Id=$rkId",$link_id);
      if($upData = mysql_fetch_array($upResult)){
        $BillNumber=$upData["BillNumber"];
        $Bill=$upData["Bill"];
		           			if($Bill==1){
			           			$Bill=$Mid.".jpg";
				           		$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
			           			$BillNumber="<span onClick='OpenOrLoad(\"$DirRK\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>$BillNumber</span>";
			           			}
	           				else{
			           			$BillNumber=$BillNumber;
			           			}
           $GoodsName=$upData["GoodsName"];
			$Attached=$upData["Attached"];
			 if($Attached==1){
				$Attached=$GoodsId.".jpg";
				$Attached=anmaIn($Attached,$SinkOrder,$motherSTR);
				$GoodsName="<span onClick='OpenOrLoad(\"$Dir\",\"$Attached\")' style='CURSOR: pointer;color:#FF6633'>$GoodsName</span>";
				}
        $CkId=$upData["CkId"];
        $rkDate=$upData["rkDate"];
        $rkQty=$upData["Qty"];
        $Operator=$upData["Operator"];
	}
//步骤4：
$tableWidth=870;$tableMenuS=550;
include "../model/subprogram/add_model_t.php";
$Parameter="ActionId,22,Mid,$Mid,funFrom,$funFrom,From,$From";
//步骤5：//需处理
                    $CheckResult=mysql_query("SELECT  *  FROM  $DataIn.nonbom7_code WHERE  rkId=$rkId AND GoodsId=$GoodsId",$link_id);
                      $BarCodeArray=array();$GoodsNumArray=array();
                      $Code=0;
                     while($CheckRow=mysql_fetch_array($CheckResult)){
                           $BarCodeArray[$Code]=$CheckRow["BarCode"];
                           $GoodsNumArray[$Code]=$CheckRow["GoodsNum"];
                           $CkIdArray[$Code]=$CheckRow["CkId"];
                           $Code++;
                          }   
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<tr>
<td class="A0011">	
	<table width="690" border="0" align="center" cellspacing="0" id="NoteTable">
         <tr ><td colspan="4"  height="30">配件名称：<?php  echo $GoodsName?><input id="GoodsId" name="GoodsId" type="hidden" value="<?php echo $GoodsId?>"></td></tr>
         <tr ><td colspan="4"  height="30">入库单：<?php  echo $BillNumber?><input id="rkId" name="rkId" type="hidden" value="<?php echo $rkId?>"></td></tr>
         <tr ><td colspan="4"  height="30">入库数量：<?php  echo $rkQty?></td></tr>
         <tr ><td colspan="4"  height="30">入库时间：<?php  echo $rkDate?></td></tr>
        <tr >
		  <td width="50" align="center" class="A1111">序号</td>
			<td width="180" height="30" align="center" class="A1101">条码</td>
			<td width="180"  align="center" class="A1101">资产编号</td>
			<td width="120"  align="center" class="A1101">入库地点</td>
			<td width="160" align="center" class="A1101"><span style="color:red; font-size:14px; font-weight:bold">图片</span>上传(限jpg,jpeg图片)</td>
		</tr>
     <?php
       $k=1;
          for($tempk=0;$tempk<$rkQty;$tempk++){ 
                            if($Code>0){
                                      $BarCode=$BarCodeArray[$tempk];
                                      $GoodsNum=$GoodsNumArray[$tempk];
                                      $thisCkId=$CkIdArray[$tempk];
                                       $thisCkId=$thisCkId==0?$CkId:$thisCkId;
                                    }
                            else{
                                     $BarCode="";
                                     $GoodsNum="";$thisCkId="";
                                     }
    ?>
		<tr>
         	<td class="A0111" align="center" height="30"><?php  echo $k; ?></td>
            <td class="A0101" align="center" ><?php  echo $BarCode; ?><input  type="hidden" id="BarCode" name="BarCode[]" value="<?php  echo $BarCode; ?>"></td>
            <td class="A0101" align="center" ><input  type="text" name="GoodsNum[]" id="GoodsNum" size="18" value="<?php  echo $GoodsNum; ?>"></td>
            <td class="A0101" align="center" ><select name="CkId[]" id="CkId" >
			           	<?PHP 
		                  $mySql="SELECT Id,Name,Remark FROM $DataPublic.nonbom0_ck  WHERE Estate=1 AND TypeId IN (0,1) order by  Remark";
	                      $result = mysql_query($mySql,$link_id);
                          if($myrow = mysql_fetch_array($result)){
	   	                  do{
			                    $FloorId=$myrow["Id"];
				                $FloorRemark=$myrow["Remark"];
				                $FloorName=$myrow["Name"];
                                   if($thisCkId==$FloorId) $echoInfo.= "<option value='$FloorId' selected>$FloorName</option>"; 
			     	           else  $echoInfo.= "<option value='$FloorId'>$FloorName</option>"; 
			                  }while ($myrow = mysql_fetch_array($result));
		                  }
                      echo $echoInfo;
			           	?></select>
</td>
            <td class="A0101">&nbsp;&nbsp;&nbsp;&nbsp;<input name="Picture[]" type="file" id="Picture[]" size="30" DataType="Filter" Accept="jpg,jpeg" Msg="格式不对,请重选" Row="1" Cel="2"></td>
    	</tr>
      <?php
              $k++;
             }
       ?>
	</table>	
</td></tr>
</table>
<?php 
include "../model/subprogram/add_model_b.php";
?>