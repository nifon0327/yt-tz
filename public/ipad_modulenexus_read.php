<?php 
//代码、数据库共享-zx
//电信-ZX
//代码共享-EWEN 2012-08-14
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=5;
$tableMenuS=500;
ChangeWtitle("$SubCompany ipad功能关系表");
$funFrom="ipad_modulenexus";
$From=$From==""?"read":$From;
$cSign=$_SESSION["Login_cSign"];
$Th_Col="选项|50|ipad主项目|200|ipad子项目|200|连接参数|300|权限设置|70";

//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="3";

//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
//一级项目
List_Title($Th_Col,"1",0);
$Result = mysql_query("SELECT * FROM $DataPublic.sc4_funmodule WHERE  Place =1  ORDER BY Place,OrderId",$link_id);
$i=1;
$j=1;//复选框序号
if ($myrow = mysql_fetch_array($Result)) {
	$SharingShow="Y";
	do {
		$m=1;
		$RowsA=1;
		$Id=$myrow["Id"];
		$ModuleId=$myrow["ModuleId"];
		$ModuleName=$myrow["ModuleName"];
		$cSignFrom=$myrow["cSign"];
		include "../model/subselect/cSign.php";
		echo"<table width='$tableWidth' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'><tr>";
		echo"<td class='A0111' width='$Field[$m]' height='20' align='center'>$i</td>";//主项目ID
		$m=$m+2;
		$Choose1="<input name='checkid[$j]' type='checkbox' id='checkid$j' value='$ModuleId'> <LABEL for='checkid$j'>$cSign $ModuleName($ModuleId)</LABEL>";
		echo"<td class='A0101' width='$Field[$m]'>$Choose1</td>";//主项目内容
		$j++;
		$m=$m+2;
		//子项目
		$Result2 = mysql_query("SELECT A.dModuleId,B.ModuleName,B.Parameter,B.cSign 
			FROM $DataPublic.sc4_modulenexus A 
			LEFT JOIN $DataPublic.sc4_funmodule B ON B.ModuleId=A.dModuleId
			WHERE A.ModuleId=$ModuleId and B.Estate > 0 order by A.ModuleId,A.OrderId",$link_id);		
		if($myrow2 = mysql_fetch_array($Result2)){
			$RowsB=0;
			do{
				$n=$m;$setupSTR="&nbsp;";
				$ModuleId2=$myrow2["dModuleId"];
				$ModuleName2=$myrow2["ModuleName"];
				$Parameter=$myrow2["Parameter"];
				$cSignFrom=$myrow2["cSign"];
				if($cSignFrom==$Login_cSign || $cSignFrom==0){//如果项目是当前系统所用
					$setupSTR="<a href='ipad_modulenexus_set.php?ModuleId=$ModuleId2&ModuleName=$ModuleName2'>设置</a>";
					}
				include "../model/subselect/cSign.php";
				if($RowsA!=1){//非首行时，要开新行
					echo"<tr>";
					}
				//输出子项目
				echo"<td class='A0101' width='$Field[$n]' height='20'>$cSign $ModuleName2($ModuleId2)</td>";
				$n=$n+2;
				echo"<td class='A0101' width='$Field[$n]'>&nbsp;$Parameter</td>";
				$n=$n+2;
				echo"<td class='A0101' width='' align='center'>$setupSTR</td>
				</tr>";
				$RowsA++;$RowsB++;
				}while ($myrow2 = mysql_fetch_array($Result2));
			}
		else{//没有设右侧子功能菜单
			//输出三级项目
			echo"<td class='A0101' width='$Field[$m]' height='20'>&nbsp;未设定</td>";
			$m=$m+2;
			echo"<td class='A0101' width='$Field[$m]' height='20'>&nbsp;未设定</td>";
			$m=$m+2;			
			echo"<td class='A0101' width='' height='20'>&nbsp;</td>
			</tr>";
			}
		//重写首行的并行数
		if($RowsA>1){
			echo"<script>ListTable$i.rows[0].cells[0].rowSpan=$RowsB;ListTable$i.rows[0].cells[1].rowSpan=$RowsB;</script>";}
		echo"</table>";
		$i++;
		} while ($myrow = mysql_fetch_array($Result));
  	}
//步骤7：
echo '</div>';
pBottom($i-1,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>