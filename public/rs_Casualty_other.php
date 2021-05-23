<?php 
//电信---yang 20120801
//代码共享-EWEN 2012-08-19
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 意外险的其它功能操作");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_other";	
$toWebPage  =$funFrom."_other_up";	
$_SESSION["nowWebPage"]=$nowWebPage; 
unset($_SESSION['sSearch']);
unset($_SESSION['SearchRows']);
//步骤3：
$SaveSTR="NO";
$ResetSTR="YES";
$tableWidth=750;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="funFrom,$funFrom,From,$From,CompanyId,$CompanyId,ProductType,$ProductType,Pagination,$Pagination,Page,$Page";
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
     <table width="100%" height="139" border="0" align="center" cellspacing="0">
    <tr>
      <td   width="41"  height="24" class="A0100">&nbsp;</td>
      <td align="right" class="A0100"><span class="redB">本页操作请谨慎</span></td>
    </tr>
    <tr>
      <td  height="27" colspan="2" class="A0000">1、员工意外险替换：将员工A的意外险替换为员工B的 </td>
    </tr>
    <tr>
    <td class="A0100">&nbsp;</td>
      <td height="27"  align="left" class="A0100">
          将月份为:
            <input name="Month" type="text" id="Month"  size="12">
            &nbsp;员工姓名为
            <input name="OldName" type="text" id="OldName" onclick="SearchData('<?php  echo $funFrom?>',1,110,1)" size="15" readonly>
            的意外险替换姓名为<input name="NewName" type="text" id="NewName" onclick="SearchData('<?php  echo $funFrom?>',1,111,2)" size="15" readonly>的意外险&nbsp;&nbsp;&nbsp;&nbsp; <input type="button" name="Submit" value="开始替换" onClick="CheckForm()">
      </td>
    </tr>
<tr><td colspan="2" height="5">&nbsp;</td></tr>

 <tr><td colspan="2">
          <table><tr>
                     <td align="right" valign="top" >2、请选择:</td>
                     <td  valign="top"><select name="ListId[]" size="16" id="ListId" multiple style="width: 200px;"  datatype="autoList" readonly>
                    </select></td>
                    <td><span id='uploadSpan'> 上传单据:<input name="Attached" type="file" id="Attached" onchange='checkFileType(this.value);'><input type="button" name="Submit" value="开始上传" onClick="UpLoad()"></span></td>
                  </tr>
            <tr>
               <td align="right"  >&nbsp;</td>
             <td >&nbsp;&nbsp;&nbsp;&nbsp;<input type="button"  value="新&nbsp;增&nbsp;" onclick="SearchRecord('Rs_Casualty','<?php  echo $funFrom?>',2,61)"/> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button"  value="删&nbsp;除"  onClick="delListRow()"></td>
  </tr>
            </table></td>
 </tr>
<tr><td colspan="2" height="15" class="A1000">&nbsp;</td></tr>
  </table>	  
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script language = "JavaScript"> 
function SearchData(fSearchPage,SearchNum,Action,TimeId){//来源页面，可取记录数，动作（因共用故以参数区别）
	var num=Math.random();  
			var tSearchPage="../public/staff";
			BackData=window.showModalDialog(tSearchPage+"_s1.php?r="+num+"&Action="+Action+"&tSearchPage="+tSearchPage+"&fSearchPage="+fSearchPage+"&SearchNum="+SearchNum,"BackData","dialogHeight =500px;dialogWidth=930px;center=yes;scroll=yes");
			if(!BackData){  
		            if(document.getElementById('SafariReturnValue')){
			        var SafariReturnValue=document.getElementById('SafariReturnValue');
			        BackData=SafariReturnValue.value;
			        SafariReturnValue.value="";
			        }
		      }	
			  //alert (TimeId);
			  TimeId=TimeId*1;
                if(BackData){
                         switch(TimeId){
                              case 1: 
							    //alert (BackData);
							  	document.getElementById("OldName").value=BackData;
							  	break;
                              case 2: 
							  	document.getElementById("NewName").value=BackData;
							 	 break;
                               }
                   }
	}
function CheckForm(){
         var checkMonth=yyyymmCheck(document.form1.Month.value);
         var OldName=document.getElementById("OldName").value;
         var NewName=document.getElementById("NewName").value;
           if(checkMonth){
                  if(OldName!="" && NewName!=""){
                            document.form1.action="Rs_Casualty_other_up.php?Action=1";
                            document.form1.submit();
                        }
                 }
          else{
		          alert("格式不对(YYYY-MM)");
                 }
	}

  function checkFileType(str){   
        var pos = str.lastIndexOf(".");  
        var lastname = str.substring(pos,str.length);  
        var resultName=lastname.toLowerCase();
        var jpg = ".jpg";
        
        if (jpg != resultName.toString() ){
            alert("请上传JPG格式");
             resetFile();  
           }  
    } 
   var html=document.getElementById('uploadSpan').innerHTML;   

    function resetFile(){   
        document.getElementById('uploadSpan').innerHTML=html;   
    }  
function UpLoad(){
               var Attached= document.getElementById("Attached");
               var cList = document.getElementById("ListId");
               if(cList.length==0 ){
                      alert("请选择要上传单据,或者相应记录");return false;
                      }
               else{
                       var PassData="";
                       for(var i=0; i<cList.length; i++){
                           if(PassData=="")PassData=cList.options[i].value;
                           else PassData=PassData+"|"+cList.options[i].value;
                         }
                       document.form1.action="Rs_Casualty_other_up.php?Action=2&PassData="+PassData;
                        document.form1.submit();
                    }
}


function delListRow(){
   var cList = document.getElementById("ListId");
   for(var i=0; i<cList.length; i++){
      if(cList.options[i].selected){
       cList.options[i]=null;
	   i=i-1;
	  }
   }
}
</script>
