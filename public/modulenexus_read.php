<?php 
//电信-yang 20120801
//代码、数据库共享-EWEN
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=5;
$tableMenuS=500;
ChangeWtitle("$SubCompany 功能关系表");
$funFrom="modulenexus";
$From=$From==""?"read":$From;
$Th_Col="选项|50|顶/底部项目|150|右侧项目|180|功能模块|210|连接参数|250|权限设置|70";

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
$Result = mysql_query("SELECT * FROM $DataPublic.funmodule WHERE 1 and TypeId<4  AND Estate>0 order by TypeId,OrderId",$link_id);
$i=1;
$j=1;//复选框序号
if ($myrow = mysql_fetch_array($Result)) {
	do {
		$m=1;
		$RowsA=1;
		$Id=$myrow["Id"];
		$ModuleId=$myrow["ModuleId"];
		$ModuleName=$myrow["ModuleName"];
		echo"<table width='$tableWidth' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'><tr>";
		echo"<td class='A0111' width='$Field[$m]' height='20' align='center'>$i</td>";
		$m=$m+2;
		$Choose1="<input name='checkid[$j]' type='checkbox' id='checkid$j' value='$ModuleId'> <LABEL for='checkid$j'>$ModuleName($ModuleId)</LABEL>";
		$j++;
		echo"<td class='A0101' width='$Field[$m]'>$Choose1</td>";
		$m=$m+2;
		$GradeA=0;
		//二级菜单		
		$Result2 = mysql_query("SELECT M.dModuleId,F.ModuleName 
        FROM $DataPublic.modulenexus M 
       LEFT JOIN $DataPublic.funmodule F ON F.ModuleId=M.dModuleId
		WHERE 1 and M.ModuleId=$ModuleId  AND F.Estate>0 order by M.ModuleId,M.OrderId",$link_id);		
		if ($myrow2 = mysql_fetch_array($Result2)) {
			$GradeB=1;
			do{
				$RowsB=1;
				$ModuleId2=$myrow2["dModuleId"];
				$ModuleName2=$myrow2["ModuleName"];
				//如果是非首行，则开新行
				if($RowsA!=1){
					echo"<tr>";
					}				
				//三级菜单
				$Result3 = mysql_query("SELECT M.dModuleId,F.ModuleName,F.Parameter 
                 FROM $DataPublic.modulenexus M 
                LEFT JOIN $DataPublic.funmodule F ON F.ModuleId=M.dModuleId
				WHERE 1 AND M.ModuleId=$ModuleId2 AND F.Estate>0 ORDER BY M.ModuleId,M.OrderId",$link_id);		
				//统计三级菜单的数目
				$Numbers=@mysql_num_rows($Result3);
				if($Numbers>1){
					$rowspanB="rowspan='$Numbers'";
					$GradeA=$GradeA+$Numbers;
					}
				else{
					$GradeA=$GradeA+1;
					$rowspanB="";
					}
				//输出二级项目
				$Choose2="<input name='checkid[$j]' type='checkbox' id='checkid$j' value='$ModuleId2'> <LABEL for='checkid$j'>$ModuleName2($ModuleId2)</LABEL>";
				$j++;
				echo"<td class='A0101' width='$Field[$m]' $rowspanB>$Choose2</td>";
				if($myrow3 = mysql_fetch_array($Result3)){
					do{
						$ModuleId3=$myrow3["dModuleId"];
						$ModuleName3=$myrow3["ModuleName"];
						$Parameter=$myrow3["Parameter"];
						//同样非首行，则开新行
						if($RowsB!=1){
							echo"<tr>";
							}
						//输出三级项目
						$m=$m+2;
						echo"<td class='A0101' width='$Field[$m]' height='20'>&nbsp;$ModuleName3($ModuleId3)</td>";
						$m=$m+2;
						echo"<td class='A0101' width='$Field[$m]'>&nbsp;$Parameter</td>";
						$m=$m+2;
						echo"<td class='A0101' width='' align='center'><a href='modulenexus_set.php?ModuleId=$ModuleId3&ModuleName=$ModuleName3'>设置</a></td>
						</tr>";
						$RowsB++;
						}while ($myrow3 = mysql_fetch_array($Result3));
					}
				else{//没有设右侧子功能菜单
					//输出三级项目
					$m=$m+2;			
					echo"<td class='A0101' width='$Field[$m]' height='20'>&nbsp;未设定</td>";
					$m=$m+2;
					echo"<td class='A0101' width='$Field[$m]' height='20'>&nbsp;未设定</td>";
					$m=$m+2;			
					echo"<td class='A0101' width='' height='20'>&nbsp;</td>
					</tr>";
					}
					
				}while ($myrow2 = mysql_fetch_array($Result2));
			}
		 else{
		 	//输出二、三级项目
			echo"<td class='A0101' width='$Field[$m]' height='20'>&nbsp;未设定</td>";
			$m=$m+2;
			echo"<td class='A0101' width='$Field[$m]'>&nbsp;未设定</td>";
			$m=$m+2;
			echo"<td class='A0101' width='$Field[$m]'>&nbsp;未设定</td>";
			$m=$m+2;
			echo"<td class='A0101' width=''>&nbsp;</td>
			</tr>";
			}
		//重写首行的并行数
		if($GradeA>1){
			echo"<script>ListTable$i.rows[0].cells[0].rowSpan=$GradeA;ListTable$i.rows[0].cells[1].rowSpan=$GradeA;</script>";}
		echo"</table>";
		$i++;
		} while ($myrow = mysql_fetch_array($Result));
  	}
//步骤7：
echo '</div>';
pBottom($i-1,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>