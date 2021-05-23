<?php

include_once ("weixin_api.php");

$weixin_api = new weixin_api();

$result = $weixin_api->upload_image_for_article('1.jpg');

var_dump($result);
