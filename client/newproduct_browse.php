<?php   
//电信-zxq 2016-03-24
include "../model/modelhead.php";
include "../model/subprogram/sys_parameters.php";
ChangeWtitle("New Product");
?>
<link rel='stylesheet' href='css/client_product.css'>
<img  style='display:none;' src='images/focus_s.png'/>
<img  style='display:none;' src='images/focus_s_1.png'/>

<body onkeydown='unUseKey()' oncontextmenu='event.returnValue=false' onhelp='return false;' style="margin:0;">
<form name='form1' id='checkFrom' enctype='multipart/form-data' method='post' action=''>
<?php 

   $imagePath= '../download/newarrival/';
   $Dir=anmaIn('download/newarrival/',$SinkOrder,$motherSTR);
   
   $checkEmail=mysql_query("SELECT  D.CompanyId,E.Name,E.GroupEmail   
				FROM  linkmandata M 
				LEFT JOIN trade_object D ON D.CompanyId=M.CompanyId 
				LEFT JOIN staffmain E ON E.Number=D.Staff_Number
				WHERE M.Id='$Login_P_Number'",$link_id);
				
	if($emailRow = mysql_fetch_array($checkEmail)){
		$Email=$emailRow['GroupEmail'];  
	} 

   $mEmail='candyzhang@ashcloud.com';
   $Email=$Email==''?$mEmail:$Email;
   
   
   $mySql = "SELECT A.Id,A.Description,A.Material,A.created,A.MOQ,A.Images,sum(if(F.Id is null,0,1)) as forward,
             YEARWEEK(CURDATE(),1) AS curWeek,YEARWEEK(A.created,1) AS creWeek,L.Liked  
			FROM new_arrivaldata A 
			LEFT JOIN trade_object C on C.CompanyId=A.CompanyId 
			LEFT JOIN currencydata D on C.Currency=D.Id 
			LEFT JOIN staffmain S on S.Number=A.creator 
			LEFT JOIN new_forward F on F.NewId=A.Id 
			LEFT JOIN new_liked L ON L.NewId=A.Id AND L.Liker='$Login_Id' 
			WHERE A.Id='$Id' LIMIT 1";
	
	$myResult = mysql_query($mySql,$link_id);
    if($myRow = mysql_fetch_array($myResult))
    {
      $Id = $myRow['Id'];
      $Description = $myRow['Description'];
      $MOQ  = $myRow['MOQ'];
      $Date = date('M d,Y',strtotime($myRow['created']));
      $Images = $myRow['Images'];
      $image = '';
      if ($Images!=''){
	     $imageArray = explode('|', $Images); 

	     $iNums  = count($imageArray);
	     $image  = $imagePath . $imageArray[$iNums-1];
	     
	     $f2=anmaIn($imageArray[$iNums-1],$SinkOrder,$motherSTR);
	     
	     $downImageFile="<a href=\"../admin/openorload.php?d=$Dir&f=$f2&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'><img  class='email' src='images/download_s.png'/></a>";
      }else{
	      $downImageFile="<img  class='email' src='images/download_s.png'/>";
      }
      
      $newSign  =" style='display:none;'";
      $dateSign = "";
      if ($myRow['creWeek']==$myRow['curWeek']){
	      $newSign  ='';
	      $dateSign =" style='display:none;'";
      }
      
      $foucsImage=$myRow['Liked']==1?'focus_s_1.png':'focus_s.png';
      
      $foucsClick=" onclick='focusClick(this,$Id)'";
      
      $EmailStr=$Email . '?cc=' . $mEmail .'&subject=' . $Description;
    }
?>
<div id='browse_top' class=''>
    <span style='margin-left:20px;'><?php echo $Description?></span>
    <span style='margin-left:50px;'>MOQ:<?php echo $MOQ?></span>
    <span style='float:right;margin-right:20px;'>Date:<?php echo $Date?></span>
</div>

<div id='browse'>
   <ul>
	   <li><img  class='email' src='images/Email_s.png' onclick="mailto('<?php echo $EmailStr?>')"/></li>
	   <li><?php echo $downImageFile?></li>
	   <li><img  class='email' src='images/<?php echo $foucsImage?>' <?php echo $foucsClick?>/></li>
   </ul>
   <img  src='<?php echo $image?>' style='width:100%;'>
</div>
<div id='browse_bottom' class=''>
    <table ><tr>
	   <td style='width:200px;'>Ash Cloud Co.,Ltd. Shenzhen</td>
	   <td style='width:160px;' class="vline">TEL:+86-755-61139580</td>
	   <td style='width:160px;' class="vline">FAX:+86-755-6113 9585</td>
	   <td class="vline">ADD:Building 48,Bao-Tian Industrial Zone,Qian-Jin 2Rd,XiXiang,Baoan,Shenzhen,China</td>
	   <td style='width:60px;float:right;'class="vline">518102</td>
   </table>

</div>
 </form>
 </body>
 
 <script>
   function focusClick(el,idx)
   {
       var LikedSign = 0;
       var dirs=el.src.split('/');
 
	   if (dirs[dirs.length-1]=='focus_s.png')
	   {
		   //el.src = 'images/foucs_1.png';
		   LikedSign =1;
	   }
	   /* 
	   else{
		   el.src = 'images/foucs.png';
	   }
	   */
	   
	 var url="newproduct_ajax.php?Action=1&Id="+idx+"&Liked="+LikedSign;
	 var ajax=InitAjax();
　	 ajax.open("GET",url,true);
	 ajax.onreadystatechange =function(){
	　　   if(ajax.readyState==4 && ajax.status ==200){
				 if(ajax.responseText=="Y"){
					
				    el.src =  LikedSign ==1?'images/focus_s_1.png':'images/focus_s.png';
			     }else{
				    // alert(ajax.responseText);
			     }
		   }
		}
　	  ajax.send(null);
   }

   
function mailto(email){
    window.open('mailto:'+email);
}
 </script>