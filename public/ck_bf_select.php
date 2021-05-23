<?php 
//电信-zxq 2012-08-01
/*
$DataIn.ck8_bfsheet
$DataPublic.staffmain
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 报废记录查询");			//需处理
$nowWebPage =$funFrom."_select";	
$toWebPage  ="temptb_model";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,chooseDate,$chooseDate";
$tableMenuS=500;
$tableWidth=850;
//步骤3：
include "../model/subprogram/select_model_t.php";
//步骤4：需处理
$CheckTb="$DataIn.ck8_bfsheet";
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td class="A0011">
			  <input name="Action" type="hidden" id="Action" value="<?php  echo $Action?>">
			<TABLE width="572" border=0 align="center">
              <TBODY>
<TR>
                  <TD align="right" valign="top">报废日期
            <input name="Field[]" type="hidden" id="Field[]" value="Date"></TD>
                  <TD align="center" valign="top">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== 
          selected>=</OPTION>
                        <OPTION value=">">&gt;</OPTION>
                        <OPTION 
          value=">=">&gt;=</OPTION>
                        <OPTION value="<">&lt;</OPTION>
                        <OPTION 
          value="<=">&lt;=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=18 onfocus="WdatePicker()" readonly>
至
  <INPUT name=DateArray[] class=textfield id="DateArray[]" size=18 onfocus="WdatePicker()" readonly>
  <input name="table[]" type="hidden" id="table[]" value="F">
  <input name="types[]" type="hidden" id="types[]" value="isDate">
            </TD>
           </TR>
		   <TR>
               <TD width="89" align="right">配 件 ID 
             <input name="Field[]" type="hidden" id="Field[]" value="StuffId"></TD>
               <TD width="99" align="center">
                 <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== 
          selected>=</OPTION>
                        <OPTION value=">">&gt;</OPTION>
                        <OPTION 
          value=">=">&gt;=</OPTION>
                        <OPTION value="<">&lt;</OPTION>
                        <OPTION 
          value="<=">&lt;=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                 </SELECT>
             </TD>
               <TD width="370"><INPUT name=value[] class=textfield id="value[]" size=48>
                    <input name="table[]" type="hidden" id="table[]" value="F">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
                <TR>
                  <TD align="right"><p>配件名称
                    <input name="Field[]" type="hidden" id="Field[]" value="StuffCname">
                  </p>
                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <option value="LIKE" selected>包含</option>
                        <OPTION value==>=</OPTION>
                        <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                    <input name="table[]" type="hidden" id="table[]" value="D">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
                <TR>
                  <TD align="right">报废数量
                  <input name="Field[]" type="hidden" id="Field[]" value="Qty"></TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== 
          selected>=</OPTION>
                        <OPTION value=">">&gt;</OPTION>
                        <OPTION 
          value=">=">&gt;=</OPTION>
                        <OPTION value="<">&lt;</OPTION>
                        <OPTION 
          value="<=">&lt;=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                      <input name="table[]" type="hidden" id="table[]" value="F">
                      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                  </TD>				  
                </TR>
			<TR>
                  <TD align="right">报废原因
              <input name="Field[]" type="hidden" id="Field[]" value="Remark"></TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                    <input name="table[]" type="hidden" id="table[]" value="F">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
                
                <TR>
                  <TD align="right">报废分类
                  <input name="Field[]" type="hidden" id="Field[]" value="Type"></TD>
                  <TD align="center">
                    <select name="fun[]" id="fun[]" style="width: 60px;">
                      <option value="=" selected>=</option>
                      <option value="!=">!=</option>
                    </select>
                  </TD>
                  <TD>
				  <select name=value[] id="value[]" style="width: 274px;">
				  
                    <option selected value=''>全部</option>
                     <!-- 
				    <option value="0">配件报废</option>
              <option value="1">配件调用</option>
              <option value="2">无单出货(收费)</option>
              <option value="3">无单出货(不收费)</option>
				  </select> -->
 			<?php 
			
			$Ck8_Sql = "SELECT Id,TypeName FROM  $DataPublic.ck8_bftype WHERE 1 AND Estate=1  ";
			$Ck8_Result = mysql_query($Ck8_Sql); 
			while ( $PD_Myrow = mysql_fetch_array($Ck8_Result)){
				$TypeId=$PD_Myrow["Id"];
				$TypeName=$PD_Myrow["TypeName"];
				if($TypeId==$Type){
					echo "<option value='$TypeId' selected>$TypeName</option>";
					}
				else{
					echo "<option value='$TypeId'>$TypeName</option>";
					}
				}
			?>
            </select>                    
                  
                  
                  
                  <input name="table[]" type="hidden" id="table[]" value="F">                  
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
<TR>
                  <TD align="right">申 请 人
            <input name="Field[]" type="hidden" id="Field[]" value="ProposerId"></TD>
                  <TD align="center">
                    <select name="fun[]" id="fun[]" style="width: 60px;">
                      <option value="=" selected>=</option>
                      <option value="!=">!=</option>
                    </select>
                  </TD>
                  <TD>
				  <select name=value[] id="value[]" style="width: 274px;">
					<?php 
					$ppResult =mysql_query("SELECT F.ProposerId,M.Name 
					FROM $DataIn.ck8_bfsheet F LEFT JOIN $DataPublic.staffmain M ON M.Number=F.ProposerId WHERE 1 GROUP BY F.ProposerId ORDER BY F.ProposerId",$link_id);
					echo "<option value='' selected>全部</option>";
					while($ppMyrow = mysql_fetch_array($ppResult)){
						$ProposerId=$ppMyrow["ProposerId"];
						$ppName=$ppMyrow["Name"];					
						echo "<option value='$ProposerId'>$ppName</option>";
						} 
					?>		 
				  </select>
                  <input name="table[]" type="hidden" id="table[]" value="F">                  
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
<TR>
                  <TD align="right">操 作 员                  
            <input name="Field[]" type="hidden" id="Field[]" value="Operator"></TD>
                  <TD align="center">
                    <select name="fun[]" id="fun[]" style="width: 60px;">
                      <option value="=" selected>=</option>
                      <option value="!=">!=</option>
                    </select>
                  </TD>
                  <TD>
				  <select name=value[] id="value[]" style="width: 274px;">
				  	<option value="" selected>全部</option>
                    <?php 
					include "../model/subprogram/select_model_stafflist.php";
					?>
				  </select>
                  <input name="table[]" type="hidden" id="table[]" value="F">                  
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum">
			</TD>
                </TR>				
				<TR>
                  <TD align="right">记录状态
                  <input name="Field[]" type="hidden" id="Field[]" value="Estate"></TD>
                  <TD align="center">
                    <select name="fun[]" id="fun[]" style="width: 60px;">
                      <option value="=" selected>=</option>
                      <option value="!=">!=</option>
                    </select>
                  </TD>
                  <TD>
				  <select name=value[] id="value[]" style="width: 274px;">
				    <option value="" selected>全部</option>
				    <option value="0">已审核</option>
				    <option value="1">未审核</option>
				  </select>
                  <input name="table[]" type="hidden" id="table[]" value="F">                  
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
<TR>
  <TD align="right">操作状态
    <input name="Field[]" type="hidden" id="Field[]" value="Locks"></TD>
  <TD align="center">
      <select name="fun[]" id="fun[]" style="width: 60px;">
            <option value="=" selected>=</option>
            <option value="!=">!=</option>
      </select>
  </TD>
  <TD><select name=value[] id="value[]" style="width: 274px;">
    <option value="" selected>全部</option>
    <option value="0">锁定</option>
    <option value="1">未锁定</option>
  </select>
    <input name="table[]" type="hidden" id="table[]" value="F">
    <input name="types[]" type="hidden" id="types[]"
                value="isNum" /></TD>
</TR>				
			  </TBODY>
	    </TABLE>
		</td>
	</tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/select_model_b.php";
?>