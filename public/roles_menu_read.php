<?php 
include "../model/modelhead.php";
$funFrom="roles_menu";
$nowWebPage=$funFrom."_read";
$_SESSION["nowWebPage"]=$nowWebPage; 
$ColsNumber=5;
$Th_Col="选项|50|一级菜单|200|二级菜单|250|操作权限|350";
$Field=explode("|",$Th_Col);
$Count=count($Field);
$widthArray=array();
for ($i=0;$i<$Count;$i++){
	$i=$i+1;
	$widthArray[]=$Field[$i];
	$tableWidth=$tableWidth+$Field[$i];
	}

if(isFireFox()==1){	 
	$tableWidth=$tableWidth+$Count*2;}
if (isSafari6()==1){
   $tableWidth=$tableWidth+ceil($Count*1.5)+1; 
	}
if (isGoogleChrome()==1){
	$tableWidth=$tableWidth+ceil($Count*1.5);
	}
$tableMenuS=550;
?>
<body onkeydown="unUseKey()"   oncontextmenu="event.returnValue=false"   onhelp="return false;">
<form name="form1" method="post" action="">
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td class='timeTop' id="menuT1" width="<?php  echo $tableMenuS?>">
	<?php 
	$result = mysql_query("SELECT id,name,action FROM $DataIn.ac_roles   ORDER BY Id",$link_id);
	if($myrow = mysql_fetch_array($result)){
		echo " <select name='RoleId' id='RoleId' onchange='document.form1.submit();'>";
		$i=1;
             $nowAction = 0;
		do{
		     $theRoleId=$myrow["id"];	
		    $theRoleName=$myrow["name"];	
			$RoleId=$RoleId==""?$theRoleId:$RoleId;
			if ($RoleId==$theRoleId){
                 $nowAction = $myrow["action"];	
				echo "<option value='$theRoleId' selected>$i-$theRoleName</option>";}
			else{
				echo "<option value='$theRoleId'>$i-$theRoleName</option>";}
			$i++;
			} while ($myrow = mysql_fetch_array($result));
		echo"</select>";
		}
	?>		  
	</td>
   <td width="150" id="menuT2" align="center" class="">
		<table border="0" align="center" cellspacing="0">
   			<tr>
				<td class="readlink" >
					<?php 
					//权限设定
					echo"<nobr>";
					echo"<span onClick='javascript:ReOpen(\"$nowWebPage\");' $onClickCSS>重置</span>&nbsp;&nbsp;";
					//if(($Keys & mUPDATE) || ($Keys & mLOCK)){
						echo"<span onClick='upDateValue()' $onClickCSS>更新</span>&nbsp;";
				//	}
					echo"</nobr>";
					?>
				</td>
			</tr>
	 </table>
   </td>
  </tr>
</table>
<?php 
//一级项目
List_Title($Th_Col,"1",0);
$Result = mysql_query("SELECT * FROM $DataIn.ac_menus WHERE  parent_id=0 and Estate>0 order by id ASC",$link_id);
$i=1;
$j=1;//复选框序号
if ($myrow = mysql_fetch_array($Result)) {
	do {
		$m=1;
		$RowsA=1;		
		$MenuId=$myrow["id"];
		$MenuName=$myrow["name"];
		$GradeA=0;
		//1级权限读取
		$checkResult = mysql_query("SELECT Id FROM $DataIn.ac_rolemenus  WHERE  role_id=$RoleId  and menu_id=$MenuId  and Action>0",$link_id);
		if($chexkRow = mysql_fetch_array($checkResult)){
			$ActionSTR1="checked";
			}
		else{
			        $ActionSTR1="";
			}
		//二级菜单		
	   $Result2 = mysql_query("SELECT *  FROM   $DataIn.ac_menus   WHERE  parent_id=$MenuId and Estate>0 order by id ASC",$link_id);		
		$Numbers2=@mysql_num_rows($Result2);
	   if($Numbers2>1){
					$rowspanA="rowspan='$Numbers2'";
					$GradeA=$GradeA+$Numbers2;
					}
				else{
					$GradeA=$GradeA+1;
					$rowspanA="";
					}
		echo"<table width='$tableWidth' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'><tr>";
		echo"<td class='A0111' width='$Field[$m]' height='20' align='center' $rowspanA>$i</td>";
		$m=$m+2;
		$Pre1=$j;//作为下级的上级
		$Choose1="<input name='checkid[$j]' type='checkbox' id='$j' value='0,$j,$Numbers2,1,$MenuId' onclick='Chooserow(this)' $ActionSTR1> <LABEL for='$j'>$MenuName($MenuId)</LABEL>";
		$j++;
		echo"<td class='A0101' width='$Field[$m]' $rowspanA>$Choose1</td>";
		$m=$m+2;
		//二级菜单处理
		if ($myrow2 = mysql_fetch_array($Result2)) {
			do{
				$MenuId2=$myrow2["id"];
				$MenuName2=$myrow2["name"];
				//如果是非首行，则开新行
				if($RowsA!=1){
					echo"<tr>";
					}		

						//2级权限读取
						$checkResult2 = mysql_query("SELECT Action FROM $DataIn.ac_rolemenus  WHERE  role_id=$RoleId AND menu_id=$MenuId2 AND Action>0 ",$link_id);
						if($chexkRow2 = mysql_fetch_array($checkResult2)){
									$ActionSTR2="checked";
								   $Action=$chexkRow2["Action"];
									if($Action & mADD){$ActionSTRa="checked";}else{$ActionSTRa="";}//2
									if($Action & mUPDATE){$ActionSTRb="checked";}else{$ActionSTRb="";}//4
									if($Action & mDELETE){$ActionSTRc="checked";}else{$ActionSTRc="";}//8
									if($Action & mLOCK){$ActionSTRd="checked";}else{$ActionSTRd="";}//16
							}
						else{
									$ActionSTR2="";
									$ActionSTRa="";
									$ActionSTRb="";
									$ActionSTRc="";
									$ActionSTRd="";
							}

				$Choose2="<input name='checkid[$j]' type='checkbox' id='$j' value='$Pre1,$j,$Numbers2,2,$MenuId2' onclick='Chooserow(this)' $ActionSTR2> <LABEL for='$j'>$MenuName2($MenuId2)</LABEL>";
				$Pre2=$j;
				$j++;
				echo"<td class='A0101' width='$Field[$m]' > $Choose2</td>";

						$m=$m+2;
						echo"<td class='A0101' width='' height='20'>&nbsp;";
						echo"&nbsp;&nbsp;<input name='checkid[$j]' type='checkbox' id='$j' value='$Pre2,$j,4,3,1' onclick='Chooserow(this)' $ActionSTR2><LABEL for='$j'>浏览</LABEL>&nbsp;";
						$Pre3=$j;
						$j++;		
						echo"&nbsp;&nbsp;<input name='checkid[$j]' type='checkbox' id='$j' value='$Pre3,$j,0,4,2' onclick='Chooserow(this)' $ActionSTRa><LABEL for='$j'>新增</LABEL>&nbsp;";
						$j++;
						echo"&nbsp;&nbsp;<input name='checkid[$j]' type='checkbox' id='$j' value='$Pre3,$j,0,4,4' onclick='Chooserow(this)' $ActionSTRb><LABEL for='$j'>更新</LABEL>&nbsp;";
						$j++;
						echo"&nbsp;&nbsp;<input name='checkid[$j]' type='checkbox' id='$j' value='$Pre3,$j,0,4,8' onclick='Chooserow(this)' $ActionSTRc><LABEL for='$j'>删除</LABEL>&nbsp;";
						$j++;
						echo"&nbsp;&nbsp;<input name='checkid[$j]' type='checkbox' id='$j' value='$Pre3,$j,0,4,16' onclick='Chooserow(this)' $ActionSTRd><LABEL for='$j'>锁定</LABEL>";
						$j++;
						echo"</td></tr>";		
                    $RowsA++;		
				}while ($myrow2 = mysql_fetch_array($Result2));
			}
		 else{
		 	//输出二级项目
			echo"<td class='A0101' width='$Field[$m]'>&nbsp;</td>";
			$m=$m+2;
			echo"<td class='A0101' width='' height='20'>&nbsp;</td>";			
			echo"</tr>";
			}
		//重写首行的并行数
		if($GradeA>1){
			echo"<script>ListTable$i.rows[0].cells[0].rowSpan=$GradeA;ListTable$i.rows[0].cells[1].rowSpan=$GradeA;</script>";}
		echo"</table>";
		$i++;
		} while ($myrow = mysql_fetch_array($Result));
  	}
echo"<input name='IdCountNum' type='hidden' id='IdCountNum' value='$j'>";
List_Title($Th_Col,"0",0);
pBottom($i-1,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
ChangeWtitle("$SubCompany 角色默认权限");
?>
<script  type=text/javascript>
function upDateValue(){
	if(document.form1.RoleId.value!=""){
		document.form1.action="roles_menu_updated.php";
		document.form1.submit();
		}
	else{
		alert("未选择员工");
		}
	}
function Chooserow(thisValue){
	//拆分参数PreIndex,nowIndex,NextCount,Grade,ModuleId
	var thisVALUE=thisValue.value;	
	var valueArray=thisVALUE.split(",");
	var PreIndex=valueArray[0]*1;
	var nowIndex=valueArray[1]*1;
	var NextCount=valueArray[2]*1;
	var Grade=valueArray[3]*1;
	var ModuleId=valueArray[4]*1;
	switch(Grade){
		case 1://OK 点击1级菜单全选下级菜单及权限
		//处理2级项目		
		var theIndex=nowIndex*1;
		if(form1.elements[nowIndex].checked){
			for(var i=1;i<=NextCount;i++){			
					theIndex++;
					form1.elements[theIndex].checked=true;			
						for(var m=1;m<=5;m++){//处理权限部分
							theIndex++;
							form1.elements[theIndex].checked=true;
							}				
					}
				}
			else{
			for(var i=1;i<=NextCount;i++){			
					theIndex++;
					form1.elements[theIndex].checked=false;	
						for(var m=1;m<=5;m++){//处理权限部分
							theIndex++;
							form1.elements[theIndex].checked=false;
							}			
					}
				}
		break;
		case 2://OK 点击2级菜单，上级选定，下级全选
			var theIndex=nowIndex*1;
			if(form1.elements[nowIndex].checked){
				form1.elements[PreIndex].checked=true;//上级选定
					form1.elements[theIndex].checked=true;
					for(var m=1;m<=5;m++){//处理权限部分
						theIndex++;
						form1.elements[theIndex].checked=true;
						}
				}
			else{//取消选定，注意要判断是否已没有其它选定项目，是则上级不选定，否则不处理上级				
					form1.elements[theIndex].checked=false;
					for(var m=1;m<=5;m++){//处理权限部分
						theIndex++;
						form1.elements[theIndex].checked=false;
						}
				//★★★★★上级处理,检查是否还有选定的项目
				var Sign=CheckChoose(PreIndex,Grade);
				
				if(Sign==false){
					form1.elements[PreIndex].checked=false;
					}				
				}
		break;
		case 3://OK 点击浏览
			var theIndex=nowIndex*1;
			var PreIndex=nowIndex*1-1;	//2级序号
			if(form1.elements[nowIndex].checked){
				form1.elements[PreIndex].checked=true;//第2级直接选定
				//通过第3级取2级数据
				var thisVALUE2=form1.elements[PreIndex].value;
				var valueArray2=thisVALUE2.split(",");
				var PreIndex2=valueArray2[0]*1;
				var nowIndex2=valueArray2[1]*1;
				form1.elements[PreIndex2].checked=true;//1级选定	
				}
			else{//需检查上上级是否还有项目处于选定状态:取取浏览，则其它功能皆取消，直接上级取消选定
				for(var m=1;m<=4;m++){//处理权限部分
					nowIndex++;
					form1.elements[nowIndex].checked=false;
					}
				form1.elements[PreIndex].checked=false;//第2级
				//★★★★★通过第2级取1级数据,判断是否有其它选中项目
				var thisVALUE2=form1.elements[PreIndex].value;
				var valueArray2=thisVALUE2.split(",");
				var PreIndex2=valueArray2[0]*1;
				var Sign=CheckChoose(PreIndex2,3);
				if(Sign==false){
					form1.elements[PreIndex2].checked=false;
					var thisVALUE1=form1.elements[PreIndex2].value;				
					var valueArray1=thisVALUE1.split(",");
					var PreIndex1=valueArray1[0]*1;
					var Sign1=CheckChoose(PreIndex1,2);
					if(Sign1==false){
						form1.elements[PreIndex1].checked=false;
						}
					}
				}
		break;
		case 4://OK 点击：新增、更新、删除、锁定
			if(form1.elements[nowIndex].checked){
				form1.elements[PreIndex].checked=true;
				PreIndex--;
				form1.elements[PreIndex].checked=true;
				var thisVALUE2=form1.elements[PreIndex].value;
				var valueArray2=thisVALUE2.split(",");
				var PreIndex2=valueArray2[0]*1;
				var nowIndex2=valueArray2[1]*1;
				form1.elements[PreIndex2].checked=true;//2级
				
				var thisVALUE1=form1.elements[PreIndex2].value;
				var valueArray1=thisVALUE1.split(",");
				var PreIndex1=valueArray1[0]*1;
				var nowIndex1=valueArray1[1]*1;
				form1.elements[PreIndex1].checked=true;
				}
		break;
		}
	}
function CheckChoose(tempIndex,tempGrade){
	var thisVALUE=form1.elements[tempIndex].value;	
	var valueArray=thisVALUE.split(",");
	var PreIndex=valueArray[0]*1;
	var nowIndex=valueArray[1]*1;
	var NextCount=valueArray[2]*1;
	var Grade=valueArray[3]*1;
	var theIndex=tempIndex*1;
	switch(tempGrade){
		case 3:
			for(var j=1;j<=NextCount;j++){//3级处理
				theIndex++;
				if(form1.elements[theIndex].checked==true){
					return true;
					}
				else{
					for(var m=1;m<=5;m++){//处理权限部分
						theIndex++;
						if(form1.elements[theIndex].checked==true){
							return true;
							}
						}
					}
				}
			break;
		case 2:
		for(var i=1;i<=NextCount;i++){//2级处理
			theIndex++;
			if(form1.elements[theIndex].checked==true){
				return true;
				}
			else{				
				var thisVALUE2=form1.elements[theIndex].value;
				var valueArray2=thisVALUE2.split(",");
				var nowIndex2=valueArray2[1]*1;
				var NextCount2=valueArray2[2]*1;
				var Grade2=valueArray2[3]*1;
				for(var j=1;j<=NextCount2;j++){//3级处理
					theIndex++;
					if(form1.elements[theIndex].checked==true){
						return true;
						}
					else{
						for(var m=1;m<=5;m++){//处理权限部分
							theIndex++;
							if(form1.elements[theIndex].checked==true){
								return true;
								}
							}
						}
					}
				}
			}
		break;
		}
	return false;
	}
</script>