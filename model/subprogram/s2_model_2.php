<?php 
ChangeWtitle("$SubCompany 查询条件设置");
//  //这几个要带过去，也就是要带到点搜索带回cg_cgdmain_s1.php ,可见cg_cgdmain_s2.php,加入,可见cg_cgdmain_s1.php
//$ReturnParameter="CompanyId|$CompanyId|BuyerId|$BuyerId";  //这几个要保持不变的值，最终要返回到本页面的,也就是点击查询后不变的. 这几个要带过去，也就是要带到点搜索带回cg_cgdmain_s1.php ,可见cg_cgdmain_s2.php,加入,可见cg_cgdmain_s1.php
$Parameter="tSearchPage,$tSearchPage,fSearchPage,$fSearchPage,SearchNum,$SearchNum,Action,$Action,ReturnParameter,$ReturnParameter";
$tableMenuS=500;
$tableWidth=850;

$DirArray = explode('/', $_SERVER['PHP_SELF']);
$DirArray = array_reverse($DirArray);
$FromDir=$DirArray['1'];
//echo "FromDir:" . $FromDir;
/*<span onClick="javascript:SearchToNext(3);" <?php  echo $onClickCSS?>>搜索</span>*/
?>
<script type=text/javascript>window.name='win_test'</script><BASE target=_self>
<body onkeydown="unUseKey()" oncontextmenu="event.returnValue=false" onhelp="return false;">
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<form name="form1" id="checkFrom" enctype="multipart/form-data" action="" method="post" target="win_test">
  <tr>
    <td class="timeTop" id="menuT1" width="<?php  echo $tableMenuS?>">&nbsp;</td>
   <td width="150" id="menuT2" align="center" class=''>
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
  <tr><td height="5" colspan="6" class="A0011">&nbsp;</td></tr>
</table>