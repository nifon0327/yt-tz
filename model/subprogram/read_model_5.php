<?php
//二合一已更新
echo"&nbsp;</td>
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
echo"</td></tr></table>
  </tr>
</table>";
echo "</div><div class='div-mcmain' style='width:$tableWidth'>";
?>