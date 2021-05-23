<body onkeydown="unUseKey()" oncontextmenu="event.returnValue=false" onhelp="return false;">
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<form name="form1" id="checkFrom" enctype="multipart/form-data" action="" method="post" >
  <input name="SafariReturnValue" id="SafariReturnValue" type="hidden" value=""> 
  <input name="Safaripassvars" id="Safaripassvars"  type="hidden" value="">
  <input name="TempMaxNumber" id="TempMaxNumber"  type="hidden" value="0">
  <tr>
    <td class="timeTop" id="menuT1" width="<?php  echo $tableMenuS?>">&nbsp;<?php  echo $SelectCode?></td>
   <td width="150" id="menuT2" align="center" class=''>
		<table border="0" align="center" cellspacing="0">
   			<tr>
				<td class="readlink" >
					<nobr>
					<span onClick="javascript:ReOpen('<?php  echo $fromWebPage?>');" <?php  echo $onClickCSS?>>返回</span>
					</nobr>
				</td>
			</tr>
	 </table>
   </td>
  </tr>
  <tr><td height="5" colspan="6" class="A0011">&nbsp;</td></tr>
</table>