<?php  //二合一已更新?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
 <tr><td height="5" colspan="6" class="A0011">&nbsp;</td></tr>
  <tr>
   <td class="timeBottom" id="menuB1" width="<?php  echo $tableMenuS?>">&nbsp;</td>
   <td width="150" id="menuT2" align="center" class=''>
		<table border="0" align="center" cellspacing="0">
   			<tr>
				<td class="readlink">
					<nobr>
					<?php 
					echo $CustomFun;//自定义功能
					if($CheckFormURL=="thisPage"){
							if($SaveSTR!="NO"){
							echo"<span onClick='checkInput()' $onClickCSS>保存</span>&nbsp;";							
							}
							echo"<span onClick='javascript:ReOpen(\"$nowWebPage\");' $onClickCSS>重置</span>";
						}
					else{
						if($SaveSTR!="NO"){
							$ErrorInfoModel=$ErrorInfoModel==""?3:$ErrorInfoModel;
							echo"<span onClick='Validator.Validate(document.getElementById(document.form1.id),$ErrorInfoModel,\"$toWebPage\")' $onClickCSS>保存</span>&nbsp;";
							echo"<span onClick='javascript:ReOpen(\"$nowWebPage\");' $onClickCSS>重置</span>";
							}
						}
					if($isBack!="N"){
						echo"&nbsp;<span onClick='javascript:ReOpen(\"$fromWebPage\");' $onClickCSS>返回</span>";
						}
					?>
					</nobr>					
				</td>
			</tr>
	 </table>
   </td>
   </tr>
   	<?php 
	if($Parameter!=""){
		PassParameter($Parameter);
		}
	?>
  </form>
</table>
</body>
</html>