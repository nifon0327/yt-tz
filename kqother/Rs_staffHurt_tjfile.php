<?php   
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 上传员工工伤报销费用单据");
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_tjfile";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：

$MainResult = mysql_query("SELECT 
	S.Id,S.Number,S.Month,S.Amount,S.Locks,S.Date,S.Operator,S.Estate,S.Mid,P.Name,B.Name AS BranchName,J.Name AS JobName,S.Remark,S.Attached ,S.tjType,P.ComeIn,S.CheckT,S.tjDate,S.HG
	 FROM $DataIn.cw18_workhurtsheet S
	LEFT JOIN $DataPublic.staffmain P ON S.Number=P.Number
    LEFT JOIN $DataPublic.branchdata B ON B.Id=P.BranchId
    LEFT JOIN $DataPublic.jobdata J ON J.Id=P.JobId
	  WHERE S.Id='$Mid'",$link_id);
$GDnumber= array("①","①","②","③","④","⑤","⑥","⑦","⑧","⑨","⑩");
if($MainRow = mysql_fetch_array($MainResult)) {
    $Number=$MainRow["Number"];
	$Name=$MainRow["Name"];
    $tjType=$MainRow["tjType"];
    $CheckT=$MainRow["CheckT"];
    $HG=$MainRow["HG"];
     $CheckTime=$GDnumber[$CheckT];
            switch($tjType){
                case "1":  $tjType="岗前体检".$CheckTime;  break;
                case "2":  $tjType="岗中体检".$CheckTime;  break;
                case "3":  $tjType="离职体检".$CheckTime;  break;
                }
	}
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="ActionId,127,Mid,$Mid,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,Number,$Number";

//步骤4：//需处理
?>
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr><td class="A0011">
        <table width="750" border="0" align="center" cellspacing="5" id="NoteTable">
		<tr>
            <td  height="25"  align="right" scope="col">姓名:</td>
            <td scope="col"><?php    echo $Name?></td>
		</tr>
	 <tr>
		  <td align="right"  height="25">体检类型:</td>
		  <td scope="col"><?php    echo $tjType?></td>
	    </tr>                 
		<tr>
		  <td align="right"  height="25">报告单上传:</td>
            <td scope="col"><input name="Attached" type="file" id="Attached" size="60" DataType="Filter" Accept="pdf" Msg="格式不对,请重选" Row="1" Cel="2"></td>
    	</tr>
</table>
	</td></tr></table>
<?php   
//步骤6：表尾
include "../model/subprogram/add_model_b.php";
?>