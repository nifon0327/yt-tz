<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
 <tr><td height="5" colspan="6" class="A0011">&nbsp;</td></tr>
  <tr>
   <td <?php  echo $td_bgcolor?> class="A1000" id="menuT1" width="<?php  echo $tableMenuS?>">&nbsp;</td>
   <td width="100" id="menuT2" align="center" class=''>
		<table border="0" align="center" cellspacing="0">
   			<tr>
				<td class="readlink" >
					<nobr>
					   <span onClick="openLoading();Validator.Validate(document.getElementById(document.form1.id),3,'<?php  echo $toWebPage?>',1,3,'<?php  echo $FromDir?>')" <?php  echo $onClickCSS?>>搜索</span>
					<span onClick="javascript:document.form1.submit();" <?php  echo $onClickCSS?>>重置</span> 
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