<?php 
//读取员工薪资明细
/*
	
	UNION ALL 
SELECT 
X.Id,X.KqSign,X.BranchId,X.Number,X.Dx,X.Gljt,X.Gwjt,X.Jj,'0' as Jbjj,X.Shbz,X.Zsbz,X.Jtbz,X.Jbf,X.Yxbz,X.taxbz,X.Jz,X.Sb,X.Kqkk,X.RandP,X.Otherkk,X.Amount,X.Remark,X.Gjj,X.Ct,
M.Name,B.Name AS Branch,B.TypeId,J.Name AS Job,P.Jj AS dJj 
FROM $DataOut.cwxzsheet X
LEFT JOIN $DataPublic.staffmain M ON M.Number=X.Number 
LEFT JOIN $DataPublic.branchdata B ON B.Id=X.BranchId
LEFT JOIN $DataPublic.jobdata J ON J.Id=X.JobId
LEFT JOIN $DataPublic.paybase P ON P.Number=X.Number
WHERE X.Month='$Month' AND X.Number='$LoginNumber' 
*/

$myResult = mysql_query("SELECT 
X.Id,X.KqSign,X.BranchId,X.Number,X.Dx,X.Gljt,X.Gwjt,X.Jj,X.Jbjj,X.Shbz,X.Zsbz,X.Jtbz,X.Jbf,X.Yxbz,X.taxbz,X.Jz,X.Sb,X.Kqkk,X.RandP,X.Otherkk,X.Amount,X.Remark,X.Gjj,X.Ct,
M.Name,B.Name AS Branch,B.TypeId,J.Name AS Job,P.Jj AS dJj 
FROM $DataIn.cwxzsheet X
LEFT JOIN $DataPublic.staffmain M ON M.Number=X.Number 
LEFT JOIN $DataPublic.branchdata B ON B.Id=X.BranchId
LEFT JOIN $DataPublic.jobdata J ON J.Id=X.JobId
LEFT JOIN $DataPublic.paybase P ON P.Number=X.Number
WHERE X.Month='$Month' AND X.Number='$LoginNumber' 

",$link_id);
if($myRow = mysql_fetch_array($myResult)) {
		$Name=$myRow["Name"];
		$Branch=$myRow["Branch"];	
		$TypeId=$myRow["TypeId"];
		$Job=$myRow["Job"];
		$Dx=$myRow["Dx"];
		$Jj=$myRow["Jj"];
		$Jbjj=$myRow["Jbjj"];
		$Gljt=$myRow["Gljt"];
		$Gwjt=$myRow["Gwjt"];
		$Shbz=$myRow["Shbz"];
		$Zsbz=$myRow["Zsbz"];
		$Jtbz=$myRow["Jtbz"];
		$Jbf=$myRow["Jbf"];
		$Yxbz=$myRow["Yxbz"];
		$taxbz=$myRow["taxbz"];
		$Kqkk=$myRow["Kqkk"];
		$RandP=number_format($myRow["RandP"],2);
		$Otherkk=$myRow["Otherkk"];
		$Jz=$myRow["Jz"];
		$Sb=$myRow["Sb"];
		$dJj=$myRow["dJj"];
		$Gjj=$myRow["Gjj"];
		$Ct=$myRow["Ct"];
		$Amount=$myRow["Amount"];
		$KqSign=$myRow["KqSign"];
		$Remark=$myRow["Remark"];
		
		$Total=$Dx+$Gljt+$Gwjt+$Jj+$Jbjj+$Shbz+$Zsbz+$Jtbz+$Jbf+$Yxbz+$taxbz-$Kqkk-$Otherkk;
		$BranchId=$myRow["BranchId"];
		//$TempsTR=$TypeId==1?"奖金":"加班费";
		
		 $jsonArray= array( 
                        array("姓名：","$Name"),
                        array("月份：","$Month"),
                        array("部门：","$Branch"),
                        array("职位：","$Job"),
                        array("底薪：","$Dx","R-Align"),
                        array("工龄津贴：","$Gljt","R-Align"),
                        array("岗位津贴：","$Gwjt","R-Align"),
                        array("奖金：","$Jj","R-Align"),
                        array("其它奖金：","$Jbjj","R-Align"),
                        array("加班费：","$Jbf","R-Align"),
                        array("生活补助：","$Shbz","R-Align" ),
                        array("住宿补助：","$Zsbz","R-Align"),
                        array("交通补助：","$Jtbz","R-Align"),
                        array("夜宵补助：","$Yxbz","R-Align"),
                        array("个税补助：","$taxbz","R-Align"),
                        array("考勤扣款：","$Kqkk","R-Align"),
                         array("小计：","$Total","R-Align","#0000FF"),
                        array("社保扣款：","-$Sb","R-Align","#FF0000"),
                        array("公积金：","-$Gjj","R-Align","#FF0000"),
                        array("借支扣款：","-$Jz","R-Align","#FF0000"),
                        array("个税：","-$RandP","R-Align","#FF0000"),
                         array("餐费扣款：","-$Ct","R-Align","#FF0000"),
                        array("其他扣款：","-$Otherkk","R-Align","#FF0000"),
                        array("实发：","¥$Amount","R-Align","#0000FF"),
                         array("备注：","$Remark")
                        );
}
?>