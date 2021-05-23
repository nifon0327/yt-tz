<?php    //二合一已更新?>
<body ><form name="form1" id="checkFrom" enctype="multipart/form-data" action="" method="post" >
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <input name="SafariReturnValue" id="SafariReturnValue" type="hidden" value=""> 
  <input name="Safaripassvars" id="Safaripassvars"  type="hidden" value="">
  <input name="TempMaxNumber" id="TempMaxNumber"  type="hidden" value="0">
  <tr>
    <td class="timeTop" id="menuT1" width="<?php    echo $tableMenuS?>">&nbsp;<?php    echo $SelectCode?></td>
   <td width="150" id="menuT2" align="center" class=''>
		<table border="0" align="center" cellspacing="0">
   			<tr>
				<td class="readlink" >
					<nobr>
					<?php   
					echo $SaveFun;
					echo $CustomFun;//自定义功能
					if($CheckFormURL=="thisPage"){
						if($SaveSTR!="NO"){
							echo"<span onClick='CheckForm()' $onClickCSS>保存</span>&nbsp;";							
							}
                                                if ($ResetSTR!="NO"){
						  echo"<span onClick='javascript:ReOpen(\"$nowWebPage\");' $onClickCSS>重置</span>";
                                                   }
						}
					else{
						if($SaveSTR!="NO"){
							$ErrorInfoModel=$ErrorInfoModel==""?3:$ErrorInfoModel;
							echo"<span onClick='Validator.Validate(document.getElementById(document.form1.id),$ErrorInfoModel,\"$toWebPage\")' $onClickCSS>保存</span>&nbsp;";
							}
                                                  if ($ResetSTR!="NO"){
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
</table>