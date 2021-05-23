<?php   
include "../model/modelhead.php";
$upDataMain="$DataIn.ch1_shipmain";
ChangeWtitle("$SubCompany 更新体检时间");
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_tjdate";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：

$MainResult = mysql_query("SELECT 
	S.Id,S.Number,S.Month,S.Amount,S.Locks,S.Date,S.Operator,S.Estate,S.Mid,P.Name,B.Name AS BranchName,J.Name AS JobName,S.Remark,S.Attached ,S.tjType,P.ComeIn,S.CheckT,S.tjDate
	 FROM $DataIn.cw17_tjsheet S
	LEFT JOIN $DataPublic.staffmain P ON S.Number=P.Number
    LEFT JOIN $DataPublic.branchdata B ON B.Id=P.BranchId
    LEFT JOIN $DataPublic.jobdata J ON J.Id=P.JobId
	  WHERE S.Id='$Mid'",$link_id);
$GDnumber= array("①","①","②","③","④","⑤","⑥","⑦","⑧","⑨","⑩");
if($MainRow = mysql_fetch_array($MainResult)) {
	$Name=$MainRow["Name"];
    $tjType=$MainRow["tjType"];
    $CheckT=$MainRow["CheckT"];
     $CheckTime=$GDnumber[$CheckT];
            switch($tjType){
                case "1":  $tjType="岗前体检".$CheckTime;  break;
                case "2":  $tjType="岗中体检".$CheckTime;  break;
                case "3":  $tjType="离职体检".$CheckTime;  break;
				case "4":  $tjType="健康体检".$CheckTime;  break;
                }
	}
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="ActionId,22,Mid,$Mid,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";

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
		  <td align="right" >体检类型:</td>
		  <td scope="col"><?php    echo $tjType?></td>
	    </tr>         
        <tr>        
               <td align="right">体检时间</td>
         <td><input name="tjDate" type="text" id="tjDate" style="width:230px" onfocus="WdatePicker()" dataType="Date" format="ymd" msg="日期不正确" readonly>
         </td>
         </tr>    
</table>
	</td></tr></table>
<?php   
//步骤6：表尾
include "../model/subprogram/add_model_b.php";
?>