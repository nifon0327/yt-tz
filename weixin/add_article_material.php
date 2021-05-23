<?php

include_once ("weixin_api.php");

$weixin_api = new weixin_api();

$result = $weixin_api->add_article_material();

var_dump($result);
