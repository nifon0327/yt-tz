<?php
//$cSignFrom=7;
//include "../basic/parameter.inc";
//电信-zxq 2012-08-01
//代码-EWEN
//方案1 分机处理：员工资料加入 工作地点，分机按工作地点
//方案2 分机按公司不同部门读取
//代码共享-EWEN 2012-08-19

defined('IN_COMMON') || include '../basic/common.php';

ob_start();
if ($DataIn==""){
    include "../basic/chksession.php";
    include "../basic/parameter.inc";
}
$Style="style='CURSOR: pointer'";
//分工作地点
echo"<TABLE cellSpacing=0 cellPadding=0 width=122 height='100%' align=center><TBODY><TR><TD vAlign=bottom height=42 onClick='RefreshTel();' style='CURSOR: pointer;'></TD></TR><tr><td> <div id='telDiv' style='width:100%;height:100%;overflow-x:hidden;overflow-y:scroll'>";
$CheckGroup=mysql_query("SELECT DISTINCT BranchId FROM $DataPublic.staffmain WHERE Estate=1 ORDER BY BranchId",$link_id);
if($CheckGroupbRow=mysql_fetch_array($CheckGroup)){
	echo "<table>";
	do{
		$BId=$CheckGroupbRow["BranchId"];
		$CheckSubSql=mysql_query("SELECT B.Mobile,B.Dh,A.Name,A.ExtNo,A.Mail,C.Name AS Branch FROM $DataPublic.staffmain A LEFT JOIN $DataPublic.staffsheet B ON B.Number=A.Number LEFT JOIN $DataPublic.branchdata C ON C.Id=A.BranchId WHERE A.Estate='1' AND A.BranchId='$BId'  AND A.ExtNo!='' ORDER BY A.ExtNo",$link_id);
		if($CheckSubRow=mysql_fetch_array($CheckSubSql)){
			$PreNo="";
			do{
				$AltSTR="";
				$Branch=$CheckSubRow["Branch"];	//部门
				$Name=$CheckSubRow["Name"];		//员工姓名
				$ExtNo=$CheckSubRow["ExtNo"];		//员工分机
				$AltSTR=$CheckSubRow["Dh"]==""?"":("短号:".$CheckSubRow["Dh"]);//短号
				$AltSTR=$CheckSubRow["Mobile"]==""?$AltSTR:$AltSTR."&#13;手机:".$CheckSubRow["Mobile"];//手机
				$strMail=$CheckSubRow["Mail"];
				$AltSTR=$CheckSubRow["Mail"]==""?$AltSTR:$AltSTR."&#13;邮件:".$strMail;//邮件地址
				$ExtNoStr=$ExtNo==$PreNo?"&nbsp;":"$ExtNo";
				//检查员工是否请假中
				$checkQjResult=mysql_query("SELECT Id FROM $DataPublic.kqqjsheet WHERE EndDate>NOW() AND Estate=0 AND Number='$Number'",$link_id);
				if ($checkQjResult){
					$checkQjSql=mysql_fetch_array($checkQjResult);
					if($checkQjSql["Id"]!=""){
						$Name="<span class=\"Qj\">$Name</span>";
						}
					}
				echo"<TR><TD height=20 width='38'>$ExtNoStr</TD><TD title='$AltSTR' style='CURSOR: pointer'><A href='#' onclick=checkIE('$strMail')>$Name</A></TD></TR>";
				$PreNo=$ExtNo;
				}while ($CheckSubRow=mysql_fetch_array($CheckSubSql));
			}
		}while($CheckGroupbRow=mysql_fetch_array($CheckGroup));
	echo"</table>";
	}
echo"<br></div></td></tr></TBODY></TABLE>";
echo "<SCRIPT language=JavaScript>window.parent.mainFrame.location='mcmain.php'</script>";
$content = ob_get_contents();//取得php页面输出的全部内容
$fp = fopen("exttel.inc", "w");
fwrite($fp, $content);
fclose($fp);

?>