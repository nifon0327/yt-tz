<?php

include_once ('configure.php');

include_once ("weixin_api.php");

$weixin_api = new weixin_api();

$url = 'http://cz.matechstone.com/weixin/produceconfirm/';
$appurl = 'https://www.pgyer.com/MpfI';
$url1 = 'http://cz.matechstone.com/weixin/QCconfirm/';
$url2 = 'http://cz.matechstone.com/weixin/warehouseconfirm/';

$post_array = array(
	'button' => array(
		array(
			'name' => '研砼治筑',
            'sub_button' => array(
                array(
                    'type' => 'media_id',
                    'name' => '研砼治筑',
                    'media_id' => '-Hs5E2XY9Yf8_cGxPqV9DmWc9hQON8uyh551AemgHLI'
                )
            )
		),
		array(
			'name' => '常州砼筑',
			'sub_button' => array(
				array(
					'type' => 'media_id',
					'name' => '常州砼筑',
					'media_id' => '-Hs5E2XY9Yf8_cGxPqV9Do4Bxfhi1k9Jpa2aHGqHU1Q'
				),
				array(
					'type' => 'view',
					'name' => '生产登记',
					'url' => $url
				),

                array(
                    'type' => 'view',
                    'name' => '质检登记',
                    'url' => $url1
                ),

                array(
                    'type' => 'view',
                    'name' => '入库登记',
                    'url' => $url2
                )

			)
		),
		array(
			'name' => '相关下载',
			'sub_button' => array(
				array(
					'type' => 'view',
					'name' => 'APP下载',
					'url' => $appurl
				)

			)
		)
	)
);

// array(
	// 'name' => '常州砼筑',
	// 'sub_button' => array(
		// array(
			// 'type' => 'media_id',
			// 'name' => '常州砼筑',
			// 'media_id' => '-Hs5E2XY9Yf8_cGxPqV9Do4Bxfhi1k9Jpa2aHGqHU1Q'
		// ),
		// array(
			// 'type' => 'view',
			// 'name' => '功能测试',
			// 'url' => $url
		// )
	// )
// )

echo json_encode($post_array, JSON_UNESCAPED_UNICODE);

$result = $weixin_api->create_menu($post_array);

var_dump($result->errcode);
