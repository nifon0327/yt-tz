<?php   
//二合一已更新
echo"&nbsp;</td><td width='35' class='timeTop'></td><td width='150' id='menuT2' align='center' class=''>
<table border='0' align='center' cellspacing='0'><tr><td class='readlink'>";
//检测是否iIPad/iPhone登陆

if (strpos($_SERVER['HTTP_USER_AGENT'],"iPhone") || strpos($_SERVER['HTTP_USER_AGENT'],"iPad") || strpos($_SERVER['HTTP_USER_AGENT'],"Android")){ 
    $ListMenuStr="<a href='javascript:void(0);' onclick='showmenuie5();'>操作菜单</a>"; 
	//$ListMenuStr="<input type='button' id='menuBtn' value='操作菜单' onclick='showmenuie5();'/>";
 }else{
	$ListMenuStr="";
 }
$otherAction=$otherAction==""?$ListMenuStr:$otherAction;
$helpFile=$helpFile==1?"&nbsp&nbsp;<a href='../help/".$funFrom.".htm' target='_blank'>帮助文件</a>":"";
echo"<nobr>$otherAction $helpFile</nobr>";
echo"</td></tr></table></td>
   	<td width='34' class='A1000'></td>
   	<td class='A1000'>&nbsp;</td>
   	<td width='5'></td>
  </tr>
  <tr><td height='5' colspan='6' class='A0011'>&nbsp;$TitlePre</td></tr>
</table>";
?>