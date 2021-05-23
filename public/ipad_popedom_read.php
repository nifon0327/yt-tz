<?php
$Keys=31;
//电信-ZX  2012-08-01
//代码共享-EWEN 2012-08-14
include "../model/modelhead.php";
$funFrom="ipad_popedom";
$nowWebPage=$funFrom."_read";
$_SESSION["nowWebPage"]=$nowWebPage; 
//$cSign=$_SESSION["Login_cSign"] ;
$ColsNumber=5;
$Th_Col="选项|50|主项目|150|子项目|150|功能标识|100|连接|200|浏览权限|100|操作权限|100";
$Field=explode("|",$Th_Col);
$Count=count($Field);
$widthArray=array();
for ($i=0;$i<$Count;$i++){
	$i=$i+1;
	$widthArray[]=$Field[$i];
	$tableWidth=$tableWidth+$Field[$i];
	}
//还要关注modelfuction.php 中的标题显示  function List_Title($Th_Col,$Sign,$Height){
if(isFireFox()==1){	 //是FirFox add by zx 2011-0326  兼容IE,FIREFOX
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
    <td class="timeTop"  id="menuT1" width="<?php  echo $tableMenuS?>">
	<?php 
    echo " <select name='User' id='User' onchange='document.form1.submit();'>";
	//只对内部人员开放权限
	$result = mysql_query("SELECT A.* FROM (
		SELECT A.Id,A.uType,concat(C.Name,': ',B.Name) AS Name 
		FROM $DataIn.usertable A 
		LEFT JOIN $DataPublic.staffmain B ON B.Number=A.Number 
		LEFT JOIN $DataPublic.branchdata C ON C.Id=B.BranchId
		WHERE 1 AND  B.Estate=1 AND A.uType=1  
		UNION ALL 
		SELECT A.Id,A.uType,concat('外部人员: ',B.Name) AS Name 
		FROM $DataIn.usertable A 
		LEFT JOIN $DataIn.ot_staff B ON B.Number=A.Number 
		WHERE 1 AND  B.Estate=1 AND A.uType=4) A 
	 	ORDER BY uType,convert(Name using gbk) asc",$link_id);//AND A.uType=1 AND B.Estate=1
	 	
	if($myrow = mysql_fetch_array($result)){
		
		$i=1;
		do{
			$UserId=$myrow["Id"];
			$Name=$myrow["Name"];
			$User=$User==""?$UserId:$User;
			if ($UserId==$User){
				echo "<option value='$UserId']' selected>$i-$Name</option>";}
			else{
				echo "<option value='$UserId']'>$i-$Name</option>";}
			$i++;
			} while ($myrow = mysql_fetch_array($result));
		
		}echo"</select>";
	?>		  
	</td>
   <td width="150" id="menuT2" align="center" class="">
		<table border="0" align="center" cellspacing="0">
   			<tr>
				<td class="readlink" >
					<?php
					//权限设定
					echo"<nobr>";
					if(($Keys & mUPDATE) || ($Keys & mLOCK)){
						echo"<span onClick='upDateValue()' $onClickCSS>更新</span>&nbsp;";
						}
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
echo "
<table width='$tableWidth' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' id='TableHead'>
  <tr height='25' >
    <td width='50'  Class='B01111110'><div align='center'>&nbsp;</div></td>
	<td width='100' Class='B01011110'><div align='center'>&nbsp;</div></td>
    <td width='150' Class='B01011110'><div align='center'>&nbsp;</div></td>
    <td width='150' Class='B01011110'><div align='center'>&nbsp;</div></td>
    <td width='200' Class='A0101' align='center'><div align='center'>&nbsp;</div></td>
    <td width='100' Class='A0101' align='center' valign='middle'><div align='center' valign='middle'><input name='checkid1' type='checkbox' id='checkid1' value='1' onclick='ChooseCell(1)'><LABEL for='checkid1'>全选</LABEL></div></td>
    <td width='' Class='A0101' align='center' valign='middle'><input name='checkid2' type='checkbox' id='checkid2' value='2' onclick='ChooseCell(2)'><LABEL for='checkid2'>全选</LABEL></td>
  </tr>
</table>
";
$Result = mysql_query("SELECT ModuleId,ModuleName FROM $DataPublic.sc4_funmodule WHERE Place=1 AND (cSign=$Login_cSign or cSign=0) ORDER BY Place,OrderId",$link_id);
$i=1;
$j=3;//复选框序号
$k=1;
if ($myrow = mysql_fetch_array($Result)) {
	$SharingShow="Y";//显示共享
	do {
		$m=1;
		$RowsA=1;
		$Id=$myrow["Id"];
		$ModuleId=$myrow["ModuleId"];
		$ModuleName=$myrow["ModuleName"];
		echo"<table width='$tableWidth' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'><tr>";
		echo"<td class='A0111' width='$Field[$m]' height='20' align='center'>$i</td>";//主项目ID
			$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]'>$ModuleName($ModuleId)</td>";//主项目内容
			$m=$m+2;
		//子项目：只显示本系统所用项目
		$Result2 = mysql_query("SELECT A.dModuleId,B.ModuleName,B.Parameter,B.cSign 
			FROM $DataPublic.sc4_modulenexus A 
			LEFT JOIN $DataPublic.sc4_funmodule B ON B.ModuleId=A.dModuleId
			WHERE  A.ModuleId='$ModuleId' AND (B.cSign=$Login_cSign OR B.cSign=0) ORDER BY A.ModuleId,A.OrderId",$link_id);	
		if($myrow2 = mysql_fetch_array($Result2)){
			$RowsB=0;
			do{
				$n=$m;
				$ModuleId2=$myrow2["dModuleId"];
				$ModuleName2=$myrow2["ModuleName"];
				$cSignFrom=$myrow2["cSign"];
		          include"../model/subselect/cSign.php";
				$Parameter=$myrow2["Parameter"];
				if($RowsA!=1){//非首行时，要开新行
					echo"<tr>";
					}
				//权限检查
				$ActionSTR1="";$ActionSTR31="";
				$checkResult = mysql_query("SELECT Id,Action FROM $DataIn.sc4_upopedom WHERE 1 AND UserId=$User AND ModuleId=$ModuleId2 AND Action>0 ORDER BY Id LIMIT 1",$link_id);
				if($chexkRow = mysql_fetch_array($checkResult)){
					$Action=$chexkRow["Action"];
					$ActionSTR1="checked";
					$ActionSTR31=$Action==31?"checked":"";
					}
				//输出子项目
				echo"<td class='A0101' width='$Field[$n]' height='20'>$k&nbsp;$ModuleName2($ModuleId2)</td>";//子项目名称
				$n=$n+2;
				echo"<td class='A0101' width='$Field[$n]'>&nbsp;$cSign</td>";	//功能标识
				$n=$n+2;
					
				echo"<td class='A0101' width='$Field[$n]'>&nbsp;$Parameter</td>";	//连接参数
				$n=$n+2;
				echo"<td class='A0101' width='$Field[$n]' align='center'><input name='checkid[$j]' type='checkbox' id='$j' value='$j,$j,1,1,$ModuleId2' onclick='Chooserow(this)' $ActionSTR1><LABEL for='$j'>浏览</LABEL></td>";
				$Pre4=$j;$j++;
				$n=$n+2;
				echo"<td class='A0101' width='' align='center'><input name='checkid[$j]' type='checkbox' id='$j' value='$Pre4,$j,2,31,$ModuleId2' onclick='Chooserow(this)' $ActionSTR31><LABEL for='$j'>操作</LABEL>";
				$j++;
				echo"</td>
				</tr>";
				$RowsA++;$RowsB++;
				$k++;
				}while ($myrow2 = mysql_fetch_array($Result2));
			}
		else{
			echo"<td class='A0101' width='$Field[$m]'>&nbsp;未设定</td>";//子项目名称
			$m=$m+2;
			echo"<td class='A0101' width='$Field[$m]'>&nbsp;未设定</td>";//子项目标识
			$m=$m+2;					
			echo"<td class='A0101' width='$Field[$m]'>&nbsp;未设定</td>";//子项目连接
			$m=$m+2;			
			echo"<td class='A0101' width='$Field[$m]'>&nbsp;</td></tr>"; //子项目权限
			$m=$m+2;			
			echo"<td class='A0101' width=''>&nbsp;</td></tr>"; //子项目权限
			}
		//重写首行的并行数
		if($RowsA>1){
			echo"<script>ListTable$i.rows[0].cells[0].rowSpan=$RowsB;ListTable$i.rows[0].cells[1].rowSpan=$RowsB;</script>";}
		echo"</table>";
		$i++;
		} while ($myrow = mysql_fetch_array($Result));
  	}
echo"<input name='RowCount' type='hidden' id='RowCount' value='$j'>";
List_Title($Th_Col,"0",0);
pBottom($i-1,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
ChangeWtitle("$SubCompany ipad权限列表");
?>
<script  type=text/javascript>
function upDateValue(){
	if(form1.User.value!=""){
	document.form1.action="ipad_popedom_updated.php";
	document.form1.submit();
	}
	else{
		alert("没有选择用户");return false;
		}
	}
function Chooserow(thisValue){
	//拆分参数PreIndex,nowIndex,NextCount,Grade,ModuleId
	var thisVALUE=thisValue.value;	
	var valueArray=thisVALUE.split(",");
	var PreIndex=valueArray[0]*1;		//功能Id
	var nowIndex=valueArray[1]*1;		//元素ID
	var Grade=valueArray[2]*1;			//级别:1-浏览	2-其它功能
	var ModuleId=valueArray[3]*1;		//权限
	switch(Grade){
		case 1://OK 点击浏览
			if(!form1.elements[nowIndex].checked){
				nowIndex++;
				form1.elements[nowIndex].checked=false;
				}
		break;
		case 2://OK 点击：新增、更新、删除、锁定
			if(form1.elements[nowIndex].checked){
				form1.elements[PreIndex].checked=true;
				}
		break;
		}
	}
function ChooseCell(cellIndex){
	var thisVALUE=form1.elements[cellIndex].value*1;
	var RowCount=form1.RowCount.value*1;
	//如果选取操作
	switch(thisVALUE){
	case 1://全选/或取消浏览
		 if(form1.elements[cellIndex].checked){//选取，只选浏览列
			for(var j=2;j<=RowCount;j++){
				if(j%2==1){
					form1.elements[j].checked=true;
					}
				}
			}
		else{//取消，则全部全消
			for(var j=1;j<RowCount;j++){
				form1.elements[j].checked=false;
				}
			}
			break;
		case 2://全选或取消操作列
			 if(form1.elements[cellIndex].checked){//选取，只选浏览列
			for(var j=1;j<=RowCount;j++){
				form1.elements[j].checked=true;
				}
			}
		else{//取消，则只取消该列
			for(var j=2;j<RowCount;j++){
				if(j%2==0){
					form1.elements[j].checked=false;
					}
				}
			}
		break;
		}
	}
</script>