<?php

include_once ("weixin_api.php");

$weixin_api = new weixin_api();

$result = $weixin_api->get_material_list();

echo ($result->total_count);

for($i=0; $i<15; $i++){
	
	$item = $result->item[$i];
	
	echo $item->media_id.'||'.$item->content->news_item[0]->title.'||'.$item->content->news_item[0]->url.'</br>';
	
}