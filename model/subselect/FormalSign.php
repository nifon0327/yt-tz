<?php 
/*
代码、数据库合并后共享-ZXQ 2012-08-08
 读取员工类别$FormalSign
 参数说明
1、$SelectFrom 这个选择框放在哪个页面 1:浏览页面；2新增页面，3更新页面，4查询页面
 		2、3为必选，1、4可选
2、$SelectTB 过滤条件的数据表别名	

例：
$SelectTB="M";
$SelectFrom=1;
  include "../model/subselect/FormalSign.php";
 */

switch($SelectFrom){
   case 1://来自于浏览页面
	      $selStr="selFlag" . $FormalSign;
	      $$selStr="selected";
	      $FormalSignSelect="<select name='FormalSign' id='FormalSign' onchange='ResetPage(this.name)'>
                                            <option value='' $selFlag>--类别--</option> 
                                            <option value='1' $selFlag1>正式工</option>
	                                        <option value='2' $selFlag2>试用期</option>
	                    </select>";
	       if ($FormalSign>0 && $SelectTB!="")  $SearchRows.=" AND $SelectTB.FormalSign='$FormalSign'";
	      break;
      
  case 4: //来自于查询页面
             $SelectWidth= $SelectWidth==""?"380px": $SelectWidth;
			 $FormalSignSelect="<select name=value[] id='value[]' style='width:$SelectWidth'>
			                                    <option value='' selected>--全部--</option>
						                        <option value='1'>正式工</option>
						                       <option value='2'>试用期</option>
			                  </select>";
      break;
          
   default://来自于新增或更新页面
			$SelectWidth= $SelectWidth==""?"380px": $SelectWidth;
			 $selStr="selFlag" . $FormalSign;
             $$selStr="selected";
			$FormalSignSelect="<select name='FormalSign'  id='FormalSign' style='width:$SelectWidth' dataType='Require' msg='未选择'>
			                                  <option value=''  $selFlag>--请选择--</option>
			                                  <option value='1' $selFlag1>正式工</option>
			                                  <option value='2' $selFlag2>试用期</option>
			                   </select>";
		break;     
}

echo $FormalSignSelect;
?>

