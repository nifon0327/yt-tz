<body >
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<form name="form1" id="checkFrom" enctype="multipart/form-data" action="" method="post" >
  <tr>
    <td class="timeTop" id="menuT1" width="<?php    echo $tableMenuS?>">&nbsp;</td>
   <td width="150" id="menuT2" align="center" class=''>
		<table border="0" align="center" cellspacing="0">
   			<tr>
				<td class="readlink" >
					<nobr>
					<span onClick="Validator.Validate(document.getElementById(document.form1.id),3,'<?php    echo $toWebPage?>',1,1)" <?php    echo $onClickCSS?>>搜索</span> 					
					<span onClick="javascript:ReOpen('<?php    echo $nowWebPage?>');" <?php    echo $onClickCSS?>>重置</span>
					<span onClick="javascript:ReOpen('<?php    echo $fromWebPage?>');" <?php    echo $onClickCSS?>>返回</span>
					</nobr>
				</td>
			</tr>
	 </table>
   </td>
  </tr>
  <tr><td height="5" colspan="6" class="A0011">&nbsp;</td></tr>
</table>