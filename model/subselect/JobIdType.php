<?php 
//代码 JobId by yang
/*
代码、数据库合并后共享
 读取部门信息  $JobId
 参数说明
1、$SelectFrom 这个选择框放在哪个页面 1:浏览页面；2新增页面，3更新页面，4查询页面
 		2、3为必选，1、4可选
2、$SelectTB 过滤条件的数据表别名	

例：
$SelectTB="M";
$SelectFrom=1;
  include "../model/subselect/JobId.php";
 */
$selectResult = mysql_query("SELECT Id,Name FROM $DataPublic.jobdata WHERE Estate=1 order by Id",$link_id);
if($selectRow = mysql_fetch_array($selectResult)){
            $SelectName="JobId";
			switch($SelectFrom){
			   case 1://来自于浏览页面
				      $SelectListStr="<select name=$SelectName id=$SelectName onchange='ResetPage(this.name)'>
				                                 <option value='' selected>--全部部门--</option>";
			              //       if ($FormalSign>0 && $FormalSignTB!="")  $SearchRows.=" AND $FormalSignTB.FormalSign='$FormalSign'";
				      break;
			      
			  case 4: //来自于查询页面
			            $SelectWidth= $SelectWidth==""?"380px": $SelectWidth;
						$SelectListStr="<select name=value[] id='value[]' style='width:$SelectWidth'><option selected  value=''>全部</option>";
			      break;
			          
			   default://来自于新增或更新页面
						$SelectWidth= $SelectWidth==""?"380px": $SelectWidth;
						$SelectListStr="<select name=$SelectName id=$SelectName  style='width:$SelectWidth' dataType='Require' msg='未选择'><option value='' selected>--请选择--</option>";
					break;     
			}
			do{
					$theId=$selectRow["Id"];
					$theName=$selectRow["Name"];
					if ($theId==$JobId){
						 $SelectListStr.="<option value='$JobId' selected>$theName</option>";
						 if ($SelectTB!="") $SearchRows.=" AND $SelectTB.BranchId='$theId' ";
						}
					else{
						$SelectListStr.="<option value='$theId'>$theName</option>";
						}
			}while ($selectRow = mysql_fetch_array($selectResult));
			$SelectListStr.="</select>";
}

echo $SelectListStr;
?>