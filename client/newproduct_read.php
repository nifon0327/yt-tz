
<?php   
//电信-zxq 2016-03-24
include "../model/modelhead.php";
include "../model/subprogram/sys_parameters.php";
ChangeWtitle("New Product");
?>
<link rel='stylesheet' href='css/client_product.css'>

<body onkeydown='unUseKey()' oncontextmenu='event.returnValue=false' onhelp='return false;'>
<form name='form1' id='checkFrom' enctype='multipart/form-data' method='post' action=''>
<input  id='searchText'  type='text'   name='searchText'  value='<?php echo $searchText ?>'/>
<input  id='search_btn'  type='button' value='Search' onclick="document.form1.submit();"/>
<img  style='display:none;' src='images/focus.png'/>
<img  style='display:none;' src='images/focus_1.png'/>
<div id='main'>
<?php 
   //分页设置
   $pageSize =30;
   $listPages = 10;//显示页码数量
   
   $startPage=$startPage==''?1:$startPage;
   if ($moreSign>0){
      $startPage=$moreSign==1?$morePage1-$listPages:$morePage2;
      $startPage = $startPage<1?1:$startPage;
   }
    
   $m=($startPage-1)*$pageSize;
   
   $PageSTR= " LIMIT $m,$pageSize ";
   //echo $PageSTR;
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
   

   
   $SearchRows=$searchText!=''?"  AND A.Description LIKE '%$searchText%' ":"";
   
   $mySql = "SELECT A.Id,A.Description,A.Material,A.created,A.MOQ,A.Images,sum(if(F.Id is null,0,1)) as forward,
             YEARWEEK(CURDATE(),1) AS curWeek,YEARWEEK(A.created,1) AS creWeek,L.Liked  
			FROM new_arrivaldata A 
			LEFT JOIN trade_object C on C.CompanyId=A.CompanyId 
			LEFT JOIN currencydata D on C.Currency=D.Id 
			LEFT JOIN staffmain S on S.Number=A.creator 
			LEFT JOIN new_forward F on F.NewId=A.Id 
			LEFT JOIN new_liked L ON L.NewId=A.Id AND L.Liker='$Login_Id' 
			WHERE A.Estate >0 $SearchRows GROUP BY A.Id ORDER BY  A.Id  DESC";
	
	$myResult = mysql_query($mySql." $PageSTR",$link_id);
    while($myRow = mysql_fetch_array($myResult))
    {
      $Id = $myRow['Id'];
      $Description = $myRow['Description'];
      $MOQ  = $myRow['MOQ'];
      $Date = date('M d,Y',strtotime($myRow['created']));
      $Images = $myRow['Images'];
      $image = '';
      if ($Images!=''){
	     $imageArray = explode('|', $Images); 
	     $imageName  = basename($imageArray[0],'.jpg');

	     $image  =$imagePath . $imageName . '_thumb.jpg';
	     
	     $iNums  = count($imageArray);
	     $f2=anmaIn($imageArray[$iNums-1],$SinkOrder,$motherSTR);
	     
	     $downImageFile="<a href=\"../admin/openorload.php?d=$Dir&f=$f2&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'><img  class='email' src='images/download.png'/></a>";
      }else{
	      $downImageFile="<img  class='email' src='images/download.png'/>";
      }
      
      $newSign  =" style='display:none;'";
      $dateSign = "";
      if ($myRow['creWeek']==$myRow['curWeek']){
	      $newSign  ='';
	      $dateSign =" style='display:none;'";
      }
      
      $foucsImage=$myRow['Liked']==1?'focus_1.png':'focus.png';
      
      $foucsClick=" onclick='focusClick(this,$Id)'";
      
      $EmailStr=$Email . '?cc=' . $mEmail .'&subject=' . $Description;
?>

  <div class='image_div'>
     <img  class='newimage' src='images/new.png' <?php echo $newSign?>/>
     <span class='date'  <?php echo $dateSign?>><?php echo $Date?></span>
     <div  class='moq'>MOQ<span><?php echo $MOQ?></span></div>
     <div  class='name'><?php echo $Description?></div>
     <span class='line_h'></span>
     <ul>
          <li style='width:68%;'><img  class='email' src='images/Email.png' onclick="mailto('<?php echo $EmailStr?>')"/> <span>Email</span></li>
          <li style='width:15%;' class='vline'><?php echo $downImageFile?></li>
          <li style='width:15%;' class='vline'>
              <img  class='email' src='images/<?php echo $foucsImage?>'  <?php echo $foucsClick?>/>
          </li>
          </ul>
     <a href='newproduct_browse.php?Id=<?php echo $Id?>' target='_blank'><img  class='thumb' src='<?php echo $image?>'></a>

  </div>

<?php } ?>

</div>
<?php 
   $limoveColor= "this.style.background='#CCCCCC';";
   $lioutColor = "this.style.background='#FFFFFF';";
   $seledColor = " style='background:#919191;' ";
   
   $totalResult=mysql_fetch_array(mysql_query('SELECT COUNT(1) AS Counts FROM new_arrivaldata A WHERE A.Estate >0 '. $SearchRows,$link_id));
   $RecordToTal= $totalResult['Counts'];
   $pages = ceil($RecordToTal/$pageSize);
   
   $prePageSign = $startPage==1?" disabled='disabled' ":'';
 
?>
<div id='pages'>
    <center>
      <ul>
        <?php
           $index = 1;  //起始页码

           if ($moreSign==1){
	             $index = $morePage1-$listPages;
	             $index = $index<1?1:$index;
	        }
	        else {
	           if ($moreSign==2){
		           $index = $morePage2;
		           $index = ($index>1 && $pages-$index<$listPages)?$pages-$listPages:$index; 
	           }
	       }
	       
	       if ($startPage<$index){//页码不在范围内
		       $index= $startPage;
	       }else{
		       if ($startPage>($index+$listPages)){
			      $index= $startPage; 
			      $index = ($index>1 && $pages-$index<$listPages)?$pages-$listPages:$index;
		       } 
	       }
           
           if ($startPage>1){
	            echo "<li id='prePage' onmousemove='$limoveColor' onmouseout='$lioutColor' onclick='selectPage(this,0)'><</li>"; 
           }else{
	            echo "<li id='prePage' disabled='disabled' style='color:#DDDDDD;'><</li>";  
           }
           
           
           $maxPages = $index+$listPages;
           $maxPages = $maxPages>$pages?$pages:$maxPages;
           
           for ($i=$index;$i<=$maxPages;$i++){
               $pageNo='Page_' . $i;
               
               if ($startPage==$i){
                   echo "<li id='$pageNo'  $seledColor>$i</li>";
               }else{
	              echo "<li id='$pageNo' onmousemove=$limoveColor onmouseout=$lioutColor onclick='selectPage(this,$i)'>$i</li>"; 
               }
	           
	          if ($i==$index+1 && $index>1){
		           echo " <li id='morePage' onmousemove='$limoveColor' onmouseout='$lioutColor' onclick='selectPage(this,1)'>...</li>";              echo " <input  id='morePage1'  type='hidden' name='morePage1'  value='$i'>";
	          }
	           
           }
           
           if ($pages>$maxPages+3){
                $morePage=$maxPages+1;
	            echo " <li id='morePage' onmousemove='$limoveColor' onmouseout='$lioutColor' onclick='selectPage(this,2)'>...</li>"; 
	            echo " <input  id='morePage2'  type='hidden' name='morePage2'  value='$morePage'>";
	            
	           for ($j=$pages-2;$j<=$pages;$j++){
	                $pageNo='Page_' . $j;
	                
	                if ($startPage==$j){
                         echo "<li id='$pageNo' $seledColor>$j</li>";
                     }else{
	                     echo "<li id='$pageNo' onmousemove=$limoveColor onmouseout=$lioutColor onclick='selectPage(this,$j)'>$j</li>"; 
	                }
	           }
           }
           
           if ($startPage<$pages){
	            echo " <li id='nextPage' onmousemove='$limoveColor' onmouseout='$lioutColor' onclick='selectPage(this,0)'>></li>"; 
           }else{
	            echo "<li  id='nextPage' disabled='disabled' style='color:#DDDDDD;'>></li>";  
           }
           
        ?>
     </ul>
    <center>
 </div>
    <input  id='startPage' type='hidden' name='startPage' value='<?php echo $startPage ?>'>
    <input  id='moreSign'  type='hidden' name='moreSign'  value='0'>
 </form>
 </body>
 
 <script>
   function focusClick(el,idx)
   {
       var LikedSign = 0;
       var dirs=el.src.split('/');
 
	   if (dirs[dirs.length-1]=='focus.png')
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
					
				    el.src =  LikedSign ==1?'images/focus_1.png':'images/focus.png';
			     }else{
				    // alert(ajax.responseText);
			     }
		   }
		}
　	  ajax.send(null);
   }
 
   function selectPage(el,idx)
   {
       var startPage  = document.getElementById('startPage');
       var startValue = startPage.value;
       if (el.id=='prePage'){
	       startValue--;
	       startPage.value=startValue;
       }
       else{
	       if (el.id=='nextPage'){
	           startValue++;
	           startPage.value=startValue;
	       }
	       else{
		       if (el.id=='morePage'){
		           document.getElementById('moreSign').value=idx;
		       }
		       else{
		           startPage.value=idx;
	          }
	       }
       }
       document.form1.submit();
   }
   
function mailto(email){
    window.open('mailto:'+email);
}
 </script>