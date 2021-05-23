<?php 
//电信-zxq 2012-08-01
//步骤1
include "../model/modelhead.php";
echo "<SCRIPT src='../model/js/cg_stuffqty_read.js' type=text/javascript></script>";
//步骤2：需处理
$ColsNumber=12;
$tableMenuS=500;
ChangeWtitle("$SubCompany 报价明细");
$funFrom="ywbj_pands";
$From=$From==""?"read":$From;
$Th_Col="选项|80|序号|50|产品|100|说明|200|配件分析|300|报价分析|80|报价|80|更新日期|80";
//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="1,2,3,4,40";				//功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-可选条件下拉框
if($From!="slist"){
	$SearchRows="";
	echo "<select name='CompanyId' id='CompanyId' onchange='ResetPage(this.name)'>";
	$result = mysql_query("SELECT CompanyId,Forshort FROM $DataIn.trade_object WHERE cSign=$Login_cSign AND Estate=1 ORDER BY Id",$link_id);
	if($myrow = mysql_fetch_array($result)){
		do{
			$theCompanyId=$myrow["CompanyId"];
			$theForshort=$myrow["Forshort"];
			$CompanyId=$CompanyId==""?$theCompanyId:$CompanyId;
			if($CompanyId==$theCompanyId){
				echo"<option value='$theCompanyId' selected>$theForshort</option>";
				$SearchRows=" AND P.CompanyId=".$theCompanyId;
				}
			 else{
			 	echo"<option value='$theCompanyId'>$theForshort</option>";
				}
			}while($myrow = mysql_fetch_array($result));
		}
	echo"</select>&nbsp;";
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql= "SELECT A.Pid,T.TypeName,P.Remark,SUM(A.Sprice) AS Price,A.Date
FROM $DataIn.ywbj_pands A
LEFT JOIN $DataIn.ywbj_productdata P ON P.Id=A.Pid
LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId
WHERE 1 $SearchRows GROUP BY A.Pid ORDER BY A.Pid DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Pid=$myRow["Pid"];
		$TypeName=$myRow["TypeName"];
		$Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		
		$Date=$myRow["Date"];
		$Price=sprintf("%.2f",$myRow["Price"]);
		//读取配件数
		$PO_Temp=mysql_query("SELECT count(*) FROM $DataIn.ywbj_pands WHERE Pid=$Pid",$link_id);
		$PO_myrow = mysql_fetch_array($PO_Temp);
		$numrows=$PO_myrow[0];
		echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
		echo"<td rowspan='$numrows' scope='col' height='21' width='$Field[$m]' class='A0111' align='center'><input name='checkid[]' type='checkbox' id='checkid$i' value='$Pid'>
		</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col' align='center'>$j</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col' align='center'>$TypeName</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col'>$Remark</td>";
		//配件列表
		$m=$m+2;
		if($numrows>0){
			//从配件表和配件关系表中提取配件数据	  
			$StuffResult = mysql_query("SELECT A.Id,A.Sid,D.Name,A.Sprice,A.Simg FROM $DataIn.ywbj_pands A,$DataIn.ywbj_stuffdata D WHERE A.Pid='$Pid' AND D.Id=A.Sid ORDER BY A.Id",$link_id);
			$k=1;
			$Dir=anmaIn("download/ywbjimg/",$SinkOrder,$motherSTR);
			if($StuffMyrow=mysql_fetch_array($StuffResult)) {//如果设定了产品配件关系
				do{	
					$n=$m;
					$Name=$StuffMyrow["Name"];
					$Sprice=$StuffMyrow["Sprice"];
					$Id=$StuffMyrow["Id"];
					$Sid=$StuffMyrow["Sid"];
					$Simg=$StuffMyrow["Simg"];
					if($Simg==1){
						$Simg="S".$Id.".jpg";
						$Simg=anmaIn($Simg,$SinkOrder,$motherSTR);
						$Name="<span onClick='OpenOrLoad(\"$Dir\",\"$Simg\")' style='CURSOR: pointer;color:#FF6633'>$Name</span>";
						}
					if($k==1){
						echo"<td class='A0101' width='$Field[$n]' height='20'>$Name</td>";
						$n=$n+2;
						echo"<td class='A0101' width='$Field[$n]' align='center'>$Sprice</td>";
						$n=$n+2;
						echo"<td class='A0101' width='$Field[$n]' rowspan='$numrows' scope='col' align='center'>$Price</td>";
						$n=$n+2;
						echo"<td class='A0101' width='' rowspan='$numrows' scope='col' align='center'>$Date</td></tr>";
						}
					else{
						echo"<tr><td class='A0101' width='$Field[$n]' height='20'>$Name</td>";
						$n=$n+2;
						echo"<td class='A0101' width='' align='center'>$Sprice</td></tr>";
						$n=$n+2;
						}
					$k++;
					} while ($StuffMyrow = mysql_fetch_array($StuffResult));
					echo "";
				}//if($StuffMyrow=mysql_fetch_array($StuffResult)) {//如果设定了
			}//end if($numrows>0)
		$i++;
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
$ChooseFun="N";
include "../model/subprogram/read_model_menu.php";
?>
