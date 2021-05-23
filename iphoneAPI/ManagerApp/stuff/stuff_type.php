<?php 

/*

NSString *const key_stuff_count =@"stuff_count";
NSString *const key_typename =@"typename";
NSString *const key_typeid=@"typeid";*/
  $sql = mysql_query("select sum(1) as stuff_count,T.TypeName as typename,T.TypeId as typeid 
  from  $DataIn.stuffdata D
left join $DataIn.stufftype T on T.typeid = D.TypeId 
 where D.Estate>0 AND T.Estate=1  group by D.TypeId order by stuff_count desc");
 $jsonArray = array();
 while ($row = mysql_fetch_assoc($sql)) {
	 
	 $stuff_count = $row["stuff_count"];
	 $typename = $row["typename"];
	 $typeid = $row["typeid"];
	 $jsonArray[]= array("stuff_count"=>"$stuff_count","typename"=>"$typename","typeid"=>"$typeid");
 }
 
 
?>