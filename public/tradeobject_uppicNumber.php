<?php   
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新上传图片职责人");//需处理
$fromWebPage=$funFrom."_".$From;		
$nowWebPage =$funFrom."_picture";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
if($Id==""){ //来自直接的网页上
	$Id=$Mid;
}
//echo $toWebPage;
//echo "<br> Id='$Id'";
//步骤3：//需处理
$MainResult = mysql_query("SELECT 
A.Id,A.CompanyId,A.Letter,A.Forshort  
FROM $DataIn.trade_object A
WHERE A.ID='$Id'",$link_id);

if($MainRow = mysql_fetch_array($MainResult)) {
	$CompanyId=$MainRow["CompanyId"];
	$Forshort=$MainRow["Forshort"];
	}
	
//add by zx 2014-03-27 抓图片职责，找到最多人的那个作为职责
$PJobname="&nbsp;";
$checkPNumber=mysql_query("SELECT A.PicNumber,A.MoreCS FROM (
							SELECT S.PicNumber,SUM(1) as MoreCS 
							FROM $DataIn.bps  B
							LEFT JOIN $DataIn.stuffdata S ON S.StuffId=B.StuffId 
							WHERE B.CompanyId='$CompanyId' AND S.Estate>0 AND  S.Pjobid!=-1 group by S.PicNumber ) A  order by A.MoreCS desc
					   ",$link_id);

if($PNumberRow=mysql_fetch_array($checkPNumber)){
	$PicNumber=$PNumberRow["PicNumber"];	
}

$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="ActionId,-1,Mid,$Mid,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,chooseDate,$chooseDate,CompanyId,$CompanyId";

//步骤4：//需处理
?>
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr><td class="A0011">
        <table width="750" border="0" align="center" cellspacing="5" id="NoteTable">
		<tr>
            <td  height="25"  align="right" scope="col">供应商名称(<?php    echo $CompanyId?>):</td>
            <td scope="col"><?php    echo $Forshort?></td>
		</tr>
       
         <tr>
		  <td  align="right"  >图片上传:</td>
		  <td >
			<select name="PicJobid" id="PicJobid" style="width: 480px;" dataType="Require"  msg="未选择图片上传职位">
            
			<?php 
			   echo " <option value='0|0' selected>不需传图片</option>";
	          $mySql="SELECT j.Id,j.Name,m.Number,m.Name as staffname FROM $DataPublic.jobdata  j
			          LEFT JOIN $DataPublic.staffmain M on J.Id=M.JobId
	                  WHERE  J.Id in(3,4,6,7,32)  AND M.Estate>0 order by j.Id,j.Name";
	           $result = mysql_query($mySql,$link_id);
	           if($myrow = mysql_fetch_array($result)){
		      do{
		       	$jId=$myrow["Id"];
		       	$jobName=$myrow["Name"];
				$Number=$myrow["Number"];
				$staffname=$myrow["staffname"];
				 
			    //if ($jId==$PicJobid){
				if ($Number==$PicNumber){
			   	echo "<option value='$jId|$Number' selected>$jobName-$staffname</option>";
				 }
			    else{
				   echo "<option value='$jId|$Number'>$jobName-$staffname</option>";
				 }
		    	}while ($myrow = mysql_fetch_array($result));
		      }

		    ?>
           
			</select>          
          </td>
  		</tr>   
               
          
</table>
	</td></tr></table>
<?php   
//步骤6：表尾
include "../model/subprogram/add_model_b.php";
?>