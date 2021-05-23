<?php 
//电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 员工调动");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_turncompany";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"];
//步骤3：//需处理
$mainResult=mysql_query("SELECT M.Id,M.Number,M.Name,M.Nickname,M.Grade,M.BranchId,M.GroupId,M.JobId,M.cSign
FROM $DataPublic.staffmain M   WHERE M.Id='$Id' LIMIT 1",$link_id);
if($mainRow=mysql_fetch_array($mainResult)){
         $Number=$mainRow["Number"];
         $Name=$mainRow["Name"];
         $Nickname=$mainRow["Nickname"];
         $GroupId=$mainRow["GroupId"];
         $thiscSign=$mainRow["cSign"];
         if ($thiscSign>0){
                  $cSignResult = mysql_query("SELECT Db,CShortName FROM $DataPublic.companys_group WHERE cSign=$thiscSign ORDER BY Id",$link_id);
                   if($cSignRow = mysql_fetch_array($cSignResult)){
                       $CShortName=$cSignRow["CShortName"];
                 }
           }       
}



//步骤4：
$tableWidth=750;$tableMenuS=500;
include "../admin/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,Number,$Number,ActionId,49";
//if( $thiscSign!=$Login_cSign){
/*switch($cSign){
   case "7":$newDataBase = $DataIn;break;
   case "6":$newDataBase = $DataCP_1;break;
   case "3":$newDataBase = $DataOut;break;
   default :  $newDataBase = $DataIn; break;
}
*/
if(true){
 $newDataBase = $DataIn;
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="550" border="0" align="center" cellspacing="5" id="NoteTable">

          <tr>
            <td align="right" width="150" scope="col">姓&nbsp;&nbsp;&nbsp;&nbsp;名:</td>
            <td scope="col">&nbsp;<?php  echo $Name?></td>
          </tr>
		  <tr>
            <td align="right" width="150" scope="col">Number:</td>
            <td scope="col">&nbsp;<?php  echo $Number?></td>
          </tr>
          <tr>
            <td align="right" width="120" scope="col">目前所在公司:</td>
            <td scope="col">&nbsp;<?php  echo $CShortName?></td>
          </tr>
            <tr>
             <td align="right">调入公司</td>
             <td>    
             <?php  
			$CompanyStr  = " AND cSign!='$thiscSign'";
			
			$cSignResult = mysql_query("SELECT cSign,CShortName,Db FROM $DataPublic.companys_group WHERE Estate=1  AND cSign>0 $CompanyStr ORDER BY Id",$link_id);
			if($cSignRow = mysql_fetch_array($cSignResult)){
			    $cSignWidth= $cSignWidth==""?"200px": $cSignWidth;
			 echo"<select name='cSign' id='cSign' style='width:380px' onchange='cSignChanged(this)'><option value='' selected>--请选择--</option>";
		        do{
	                $theId=$cSignRow["cSign"];
	                $theName=$cSignRow["CShortName"];
	                $dbName=$cSignRow["Db"];
	                if($theId==$cSign){
	                   echo "<option value='$theId' selected>$theName</option>";
	                 }else{
	                   echo "<option value='$theId'>$theName</option>";         
	                 }
		        }while ($cSignRow = mysql_fetch_array($cSignResult));
		        echo "</select>&nbsp;";
		     }
                 
             ?></td>
		  </tr>
          
          <tr>
            <td align="right">工作地址</td>
            <td><?php 
             $SelectFrom="";
	  		 include "../model/subselect/WorkAdd.php";   
            ?>
            </td>
          </tr>          
          
          <tr>
            <td align="right">部&nbsp;&nbsp;&nbsp;&nbsp;门</td>
            <td>
			<?php 
			$selectResult = mysql_query("SELECT B.Id,B.Name FROM  $DataPublic.branchdata B  WHERE B.Estate=1 ORDER BY B.SortId,B.Id",$link_id);
			if($selectRow = mysql_fetch_array($selectResult)){
				$SelectListStr="<select name='BranchId' id='BranchId'  style='width:380px' dataType='Require' msg='未选择'>
				                <option value='' selected>--请选择--</option>";
				do{
					$theId=$selectRow["Id"];
					$theName=$selectRow["Name"];
					if ($theId==$BranchId){
						 $SelectListStr.="<option value='$BranchId' selected>$theName</option>";
						 if ($SelectTB!="") $SearchRows.=" AND $SelectTB.BranchId='$theId' ";
						}
					else{
						$SelectListStr.="<option value='$theId'>$theName</option>";
						}
				}while ($selectRow = mysql_fetch_array($selectResult));
				$SelectListStr.="</select>&nbsp;";
			}
			
			echo $SelectListStr;			 
            ?>            
            </td>
          </tr>
          <tr>
            <td align="right">小&nbsp;&nbsp;&nbsp;&nbsp;组</td>
            <td><?php 
		 $selectResult = mysql_query("SELECT GroupId,GroupName FROM $newDataBase.staffgroup WHERE Estate=1 order by GroupId",$link_id);
		 if($selectRow = mysql_fetch_array($selectResult)){
	     $SelectListStr="<select name='GroupId' id='GroupId'  style='width:380px' dataType='Require' msg='未选择'><option value='' selected>--请选择--</option>";
		  do{
			   $theId=$selectRow["GroupId"];
			   $theName=$selectRow["GroupName"];
			   $SelectListStr.="<option value='$theId'>$theName</option>";
			}while ($selectRow = mysql_fetch_array($selectResult));
			$SelectListStr.="</select>&nbsp;";
		  }
		 echo $SelectListStr;
            ?>
            </td>
          </tr>
          <tr>
            <td align="right">职&nbsp;&nbsp;&nbsp;&nbsp;位</td>
            <td>
			<?php 
             $SelectFrom="";
             include"../model/subselect/JobIdType.php";
            ?>               
            </td>
          </tr>
   </table>
</td></tr></table>
<?php 
//步骤5：
}
else {
	echo "请到目的系统做调动！";
}
include "../admin/subprogram/add_model_b.php";
?>
<script>
 
 function  cSignChanged(e){
	 
	 document.form1.submit();
 }
</script>