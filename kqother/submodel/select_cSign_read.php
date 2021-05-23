<?php 
/*
 读取公司标识cSign
 */
$CompanyStr=$CompanyHidden==1?"  AND cSign!='$cSign'":"";
$cSignResult = mysql_query("SELECT cSign,CShortName,Db FROM $DataPublic.companys_group WHERE Estate=1  AND cSign>0 $CompanyStr ORDER BY Id",$link_id);
if($cSignRow = mysql_fetch_array($cSignResult)){
    $cSignWidth= $cSignWidth==""?"200px": $cSignWidth;
    //echo"<select name='cSign' id='cSign' onchange='cSignChanged(this)' style='width:$cSignWidth'>
 echo"<select name='cSign' id='cSign' style='width:$cSignWidth'>
         <option value='' selected>--请选择--</option>";
        do{
                $theId=$cSignRow["cSign"];
                $theName=$cSignRow["CShortName"];
                $dbName=$cSignRow["Db"];
                if ($theId==$cSign){
                       $DataIn=$dbName;
                        echo "<option value='$theId' selected>$theName</option>";
                        }
                else{
                        echo "<option value='$theId'>$theName</option>";
                        }
        }while ($cSignRow = mysql_fetch_array($cSignResult));
        echo "</select>&nbsp;";
}
?>
<script type="text/JavaScript">
function cSignChanged(e)
{
     cSign=e.value;
     if (document.getElementById("BranchId")){
	      url="staff_info_ajax.php?cSign="+cSign+"&Action=BranchId";
	     var ajax=InitAjax(); 
	     ajax.open("GET",url,true);
	     ajax.onreadystatechange =function(){
			 if(ajax.readyState==4){
				     var BackData=ajax.responseText;
				     Main_SelectChanged("BranchId",BackData);
				     get_GroupData(cSign);
			   }  
	      }  
	     ajax.send(null);
      } 
      else{
	      get_GroupData(cSign);
      }
}

function get_GroupData(cSign)
{
    if (document.getElementById("GroupId")){
	      url="staff_info_ajax.php?cSign="+cSign+"&Action=GroupId";
	     var ajax=InitAjax(); 
	     ajax.open("GET",url,true);
	     ajax.onreadystatechange =function(){
			 if(ajax.readyState==4){
				     var BackData=ajax.responseText;
				     Main_SelectChanged("GroupId",BackData);
				     get_JobData(cSign);
			   }  
	      }  
	     ajax.send(null);
      }
      else{
	      get_JobData(cSign);
      }
}

function get_JobData(cSign)
{
   if (document.getElementById("JobId")){
	      url="staff_info_ajax.php?cSign="+cSign+"&Action=JobId";
	     var ajax=InitAjax(); 
	     ajax.open("GET",url,true);
	     ajax.onreadystatechange =function(){
			 if(ajax.readyState==4){
				     var BackData=ajax.responseText;
				     Main_SelectChanged("JobId",BackData);
			   }  
	      }  
	     ajax.send(null);
      }
}

function Main_SelectChanged(selectObj,OptionList){
   if (OptionList=="") return;
    //json数据 序列化成js对象
   OptionList=eval(OptionList);
  // alert(OptionList);
   if (typeof selectObj != 'object')
     {
       selectObj = document.getElementById(selectObj);
     }
     
    // 清空选项
    var slen = selectObj.options.length;
 
    for (var i=0; i < slen; i++)
    {
        // 移除当前选项
        selectObj.options[0] = null;
    }
    
    var len = OptionList.length;
    selectObj.options[0] = new Option('--请选择--', '');
    
    var n=1;
    for (var i=0; i < len; i++)
    {
        // alert(OptionList[i][0]);
          selectObj.options[n] = new Option(OptionList[i][1],OptionList[i][0]);
           n++;

    }
    
}
</script>