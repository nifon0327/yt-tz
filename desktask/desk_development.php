<?php   
/*电信---yang 20120801
$DataIn.development
$DataIn.trade_object
$DataPublic.staffmain
二合一已更新
*/
//步骤1
include "../model/modelhead.php";
echo "<SCRIPT src='../model/js/cg_stuffqty_read.js' type=text/javascript></script>";
//步骤2：需处理
$ColsNumber=10;
$tableMenuS=500;
$sumCols="8";
ChangeWtitle("$SubCompany 开发项目费用");
$funFrom="development";
$From=$From==""?"read":$From;
//$Th_Col="选项|50|序号|40|客户|60|项目编号|60|项目名称|400|内容/图片|60|起始日期|70|结束日期|70|开发费用|80";
$Th_Col="选项|50|序号|40|客户|60|项目编号|60|项目名称|400|备注|60|起始日期|70|结束日期|70|开发费用|80";

//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="";				//功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消

//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//必选,过滤条件
//步骤4：需处理-条件选项
if($From!="slist"){
	//开发人员过滤
	echo"<select name='Operator' id='Operator' onchange='document.form1.submit()'>";
	$checkType= mysql_query("
		SELECT P.Name,P.BranchId,P.Estate,D.Operator FROM $DataIn.development D,$DataPublic.staffmain P 
		WHERE D.Operator=P.Number AND P.Estate=1 GROUP BY D.Operator ORDER BY P.BranchId DESC,D.Operator DESC",$link_id);
	if($TypeRow = mysql_fetch_array($checkType)){			
		do{
			$Number=$TypeRow["Operator"];
			$Name=$TypeRow["Name"];
			$BranchId=$TypeRow["BranchId"];
			$Estate=$TypeRow["Estate"];
			$Operator=$Operator==""?$Number:$Operator;
				$FontColor="";
			if($Estate==0 || $BranchId!=4){
				$FontColor="style='color:#99CC99'";
				}
			if($Operator==$Number){
				echo"<option value='$Number' $FontColor selected>$Name</option>";
				$SearchRows=" AND D.Operator='$Number'";
				}
			else{
				echo"<option value='$Number' $FontColor>$Name</option>";
				}
			}while ($TypeRow = mysql_fetch_array($checkType));
		}	
		echo"</select>";
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";

//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$Keys=1;
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT D.Id,D.ItemId,D.Attached,D.ItemName,D.Content,D.Plan,D.StartDate,D.EndDate,D.Locks,D.Operator,C.Forshort,P.Name 
FROM $DataIn.development D 
LEFT JOIN $DataIn.trade_object C ON D.CompanyId=C.CompanyId
LEFT JOIN $DataPublic.staffmain P ON P.Number=D.Operator WHERE 1 $SearchRows ORDER BY D.ItemId desc";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];		
		$Forshort=$myRow["Forshort"];
		$ItemId=$myRow["ItemId"];
		$Plan=$myRow["Plan"]==""?"$myRow[ItemId]":"<span title='开发进度：$myRow[Plan]' style='CURSOR: pointer;color:#FF6633'>$ItemId</span>";
		$ItemName=$myRow["ItemName"];
		$StartDate=$myRow["StartDate"];
		$EndDate=$myRow["EndDate"]=="0000-00-00"?"&nbsp;":$myRow["EndDate"];
		$Name=$myRow["Name"];
		$Content=trim($myRow["Content"])==""?"&nbsp;":"<img src='../images/remark.gif' alt='$myRow[Content]' width='16' height='16'>";
		$Attached=$ItemId.".jpg";
		$f=anmaIn($Attached,$SinkOrder,$motherSTR);//加密字串
		$d=anmaIn("download/kfimg/",$SinkOrder,$motherSTR);
		$Attached=$myRow["Attached"]==0?"&nbsp;":"<span onClick='OpenOrLoad(\"$d\",\"$f\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
		//如果权限非最高，则锁定非自己的项目
		
	
			
		
		//开发费用计算
		$CheckSql=mysql_query("SELECT SUM(Amount) AS Amount FROM $DataIn.cwdyfsheet WHERE ItemId='$ItemId'",$link_id);
		if($CheckRow=mysql_fetch_array($CheckSql)){
			$Amount=$CheckRow["Amount"]==""?"&nbsp;":$CheckRow["Amount"];
			/*
			$Id=$CheckRow["Id"];
			$Bill=$CheckRow["Bill"];
			$Dir=anmaIn("download/dyf/",$SinkOrder,$motherSTR);
			if($Bill==1){
				$Bill="DYF".$Id.".jpg";
				$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
				$Bill="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
				}
			else{
				$Bill="&nbsp;";
				}	
			*/
			}
		//
		//传递项目ID
		$DivNum="a".$i;
		$TempId="$ItemId|$DivNum";
		$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"desk_development_a\");' id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' width='13' height='13' style='CURSOR: pointer'>";
		$HideTableHTML="
			<table width='$tableWidth' border='0' cellspacing='0' id='HideTable_$DivNum$i' style='display:none'>
				<tr bgcolor='#B7B7B7'>
					<td class='A0111' height='30'>
						<br>
							<div id='HideDiv_$DivNum$i' width='$subTableWidth' align='right'>&nbsp;</div>
						<br>
					</td>
				</tr>
			</table>";
		$Locks=0;
		
		

		
		//
		$ValueArray=array(
			0=>array(0=>$Forshort,
					 1=>"align='center'"),
			1=>array(0=>$Plan,
					 1=>"align='center'",					 
					 2=>"onmousedown='window.event.cancelBubble=true;'"),
			2=>array(0=>$ItemName),
			3=>array(0=>"&nbsp;$Content&nbsp;$Attached",					
					 2=>"onmousedown='window.event.cancelBubble=true;'"),
			4=>array(0=>$StartDate,
					 1=>"align='center'"),
			5=>array(0=>$EndDate,
					 1=>"align='center'"),
			6=>array(0=>$Amount,
					 1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		echo $HideTableHTML;
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>