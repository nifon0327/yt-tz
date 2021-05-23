<?php

include_once ("weixin_api.php");

$weixin_api = new weixin_api();

$result = $weixin_api->add_none_article_material('0.jpg', 'image');

var_dump($result->url);
