<?php    //二合一已更新?>
<body onkeydown="unUseKey()" oncontextmenu="event.returnValue=false" onhelp="return false;">
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<form name="form1" id="checkFrom" enctype="multipart/form-data" action="" method="post" >
  <tr>
    <td class="timeTop" id="menuT1" width="<?php    echo $tableMenuS?>">&nbsp;<?php    echo $SelectCode?></td>
   <td width="150" id="menuT2" align="center" class=''>
		<table border="0" align="center" cellspacing="0">
   			<tr>
				<td class="readlink" >
					<nobr>
					<?php   
					echo $CustomFun;//自定义功能
					if($CheckFormURL=="thisPage"){
						if($SaveSTR!="NO"){
							echo"<span onClick='CheckForm()' $onClickCSS>删除</span>&nbsp;";							
							}
						echo"<span onClick='javascript:ReOpen(\"$nowWebPage\");' $onClickCSS>重置</span>";
						}
					else{
						if($SaveSTR!="NO"){
							$ErrorInfoModel=$ErrorInfoModel==""?3:$ErrorInfoModel;
							echo"<span onClick='Validator.Validate(document.getElementById(document.form1.id),$ErrorInfoModel,\"$toWebPage\")' $onClickCSS>删除</span>&nbsp;";
							}
							echo"<span onClick='javascript:ReOpen(\"$nowWebPage\");' $onClickCSS>重置</span>";
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
  <tr><td height="5" colspan="6" class="A0011">&nbsp;</td></tr>
</table>