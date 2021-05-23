<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
 <tr><td height="5" colspan="6" class="A0011">&nbsp;</td></tr>
  <tr>
   <td class="timeBottom" id="menuB1" width="<?php  echo $tableMenuS?>">&nbsp;</td>
   <td width="150" id="menuT2" align="center" class=''>
		<table border="0" align="center" cellspacing="0">
   			<tr>
				<td class="readlink">
					<nobr><span onClick="javascript:ReOpen('<?php  echo $fromWebPage?>');" <?php  echo $onClickCSS?>>返回</span></nobr>					
				</td>
			</tr>
	 </table>
   </td>
   <td class="A0100">&nbsp;</td>
   </tr>
   	<?php 
	PassParameter($Parameter);
	?>
  </form>
</table>
</body>
</html>