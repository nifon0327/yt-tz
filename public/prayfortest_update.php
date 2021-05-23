<?php 
//已更新电信---yang 20120801
include "../model/modelhead.php";
//步骤2：//需处理
ChangeWtitle("$SubCompany 更新开发费用记录");
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
/*
$upResult = mysql_query("SELECT S.ItemId,S.TypeID,S.ModelDetail,S.Description,S.Amount,S.Currency,S.Remark,S.Bill,S.Provider,S.Date,D.ItemName 
FROM $DataIn.cwdyfsheet S 
LEFT JOIN $DataIn.producttest D ON D.ItemId=S.ItemId Where S.Id=$Id",$link_id);
*/
$upResult = mysql_query("SELECT S.ItemId,S.TypeID,S.ModelDetail,S.Description,S.Amount,S.Currency,S.Remark,S.Bill,S.Provider,S.Date,S.ItemName,S.CompanyId
FROM $DataIn.cwdyfsheet S  Where S.Id=$Id",$link_id);

if($upRow = mysql_fetch_array($upResult)){
	$ItemId=$upRow["ItemId"]."~".$upRow["ItemName"];
	$TypeID=$upRow["TypeID"];
	$ModelDetail=$upRow["ModelDetail"];
	$Description=$upRow["Description"];
	$Amount=$upRow["Amount"];
	$Currency=$upRow["Currency"];
	$Remark=$upRow["Remark"];
	$Bill=$upRow["Bill"];
	$Provider=$upRow["Provider"];
	$Date=$upRow["Date"];
	$CompanyId=$upRow["CompanyId"];
	$ItemName=$upRow["ItemName"];
	}
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Estate,$Estate,Pagination,$Pagination,Page,$Page";

//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr><td class="A0011">
		<table width="750" border="0" align="center" cellspacing="5" id="NoteTable">
			<tr>
              <td align="right" scope="col">日 &nbsp;&nbsp;&nbsp;期</td>
              <td scope="col"><input name="upDate" type="text" id="Date" value="<?php  echo $Date?>" size="97" onfocus="WdatePicker()" DataType="Date" format="ymd" Msg="日期不对或没选日期" readonly> </td>
		</tr>
		<input name="ItemStr" type="hidden" id="ItemStr"  value="<?php  echo $ItemId; ?>" />
        <!--
        
		<tr>
            <td align="right" scope="col">项目编号 </td>
            <td scope="col"><input name="ItemStr" type="text" id="ItemStr" title="必选项，点击在查询窗口选取" onclick="SearchRecord('development','<php  echo $funFrom?>',1)" value="<php  echo $ItemId?>" size="97" readonly DataType="Require" Msg="没有选取项目"></td>
		</tr>  -->
        <tr>
            <td width="160" align="right" scope="col">客&nbsp;&nbsp;&nbsp;&nbsp;户：</td>
            <td scope="col" class="A0001">
            <select name="CompanyId" id="CompanyId" size="1" style="width: 502px;">
            <?php  
            $result = mysql_query("SELECT * FROM $DataIn.trade_object WHERE cSign=$Login_cSign AND Estate=1 ORDER BY Id",$link_id);
            if($myrow = mysql_fetch_array($result)){
                do{	
                    if($myrow[CompanyId]==$CompanyId){
                        echo"<option value='$myrow[CompanyId]' selected>$myrow[Forshort]</option>";
                        }
                    else{
                        echo"<option value='$myrow[CompanyId]'>$myrow[Forshort]</option>";
                        }
                    } while ($myrow = mysql_fetch_array($result));
                }
            ?>
            </select>
            </td>
        </tr>
        <tr>
            <td height="26" align="right" scope="col">项目名称：</td>
            <td class="A0001" scope="col"><input name="ItemName" type="text" id="ItemName" size="94" value="<?php  echo $ItemName?>" title="可输入1-50个字节(每1中文字占2个字节，第1英文字母占1个字节)" DataType="LimitB"  Max="50" Min="1" Msg="没有填写或字符超出50字节"> </td>
        </tr>
            
         <tr>
            <td scope="col"><div align="right">费用分类</div></td>
            <td  scope="col">
                <select name="TypeID" id="TypeID" style="width:520px">
                <?php 
                $kftypedata_Result = mysql_query("SELECT Id,Name FROM $DataPublic.kftypedata WHERE Estate=1 order by Id",$link_id);
                if($kftypedata_Row = mysql_fetch_array($kftypedata_Result)){
                    do{
                        $Name=$kftypedata_Row["Name"];
                        $Id=$kftypedata_Row["Id"];
                        if($Id==$TypeID){
                            echo"<option value='$Id' selected>$Name</option>";
                            }
                        else{	
                            echo"<option value='$Id'>$Name</option>";
                            }
                        }while ($kftypedata_Row = mysql_fetch_array($kftypedata_Result));
                    }
                ?>
                </select></td>
          </tr> 
                  

         <tr>
            <td align="right">金&nbsp;&nbsp;&nbsp;&nbsp;额</td>
            <td>
            <input name="Amount" type="text" id="Amount" title="必选项,填写非负数" value="<?php  echo $Amount?>" size="97" DataType="Currency" Msg="没有填写或格式不对"></td>
         </tr>
         
          <tr>
            <td scope="col"><div align="right">结付货币</div></td>
            <td  scope="col">
                <select name="Currency" id="Currency" style="width:520px">
                <?php 
                $Currency_Result = mysql_query("SELECT Id,Name FROM $DataPublic.currencydata WHERE Estate=1 order by Id",$link_id);
                if($Currency_Row = mysql_fetch_array($Currency_Result)){
                    do{
                        $Name=$Currency_Row["Name"];
                        $Id=$Currency_Row["Id"];
                        if($Id==$Currency){
                            echo"<option value='$Id' selected>$Name</option>";
                            }
                        else{	
                            echo"<option value='$Id'>$Name</option>";
                            }
                        }while ($Currency_Row = mysql_fetch_array($Currency_Result));
                    }
                ?>
                </select></td>
          </tr>  
         <!-- 
  		<tr>
        <td align="right" scope="col">机&nbsp;&nbsp;&nbsp;&nbsp;型</td>
             <td scope="col"><input name="ModelDetail" type="text" id="ModelDetail" size="97" value="<=$ModelDetail?>" title="必选项,需输入1-60个字节(每1中文字占2个字节，每1英文字母占1个字节)" DataType="LimitB"  Max="60" Min="1" Msg="没有填写或字符超出60字节"></td>
		</tr>                  
         -->
        <tr>
          <td align="right" valign="top">凭&nbsp;&nbsp;&nbsp;&nbsp;证</td>
          <td><input name="Attached" type="file" id="Attached" size="65"  DataType="Filter" Accept="jpg" Msg="文件格式不对,请重选" Row="4" Cel="1"></td>
        </tr>
		<?php 
		if($Bill==1){
			echo"<tr><td height='13' scope='col'>&nbsp;</td>
			  <td scope='col'><input name='oldAttached' type='checkbox' id='oldAttached' value='1'><LABEL for='oldAttached'> 删除已传单据</LABEL></td></tr>";
			}?>
          <tr>

 		<tr>
             <td align="right" scope="col">供 应 商</td>
             <td scope="col"><input name="Provider" type="text" id="Provider" size="97" value="<?php  echo $Provider?>" title="必选项,需输入1-30个字节(每1中文字占2个字节，每1英文字母占1个字节)" DataType="LimitB"  Max="30" Min="1" Msg="没有填写或字符超出30字节"></td>
		</tr>         
          
         <tr>
            <td align="right" valign="top">请款说明</td>
            <td><textarea name="Description" cols="62" rows="5" id="Description" title="必选项,需要填写" DataType="Require" Msg="没有填写"><?php  echo $Description?></textarea></td>
         </tr>
         <tr>
            <td align="right" valign="top">请款备注</td>
            <td><textarea name="Remark" cols="62" rows="5" id="Contant" title="可选项"><?php  echo $Remark?></textarea></td>
         </tr>
        </table>
   </td></tr></table>
<?php 
//步骤6：表尾
include "../model/subprogram/add_model_b.php";
?>