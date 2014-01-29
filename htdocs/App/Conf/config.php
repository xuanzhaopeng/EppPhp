<?php
return array(
    'URL_MODEL'                 =>  2, 
    'DB_TYPE'                   =>  'mysql',
    'DB_HOST'                   =>  '127.0.0.1',
    'DB_NAME'                   =>  'eclipseplusplus',
    'DB_USER'                   =>  'root',
    'DB_PWD'                    =>  '',
    'DB_PORT'                   =>  '3306',
    'DB_PREFIX'                 =>  'epp_',
    'APP_AUTOLOAD_PATH'         =>  '@.TagLib',
    'APP_GROUP_LIST'            =>  'Home,Admin,Mobile',
    'DEFAULT_GROUP'             =>  'Home',
    'APP_GROUP_MODE'            =>  1,
    'SHOW_PAGE_TRACE'           =>  1,// Shows debug informations
	'LANG_SWITCH_ON' 			=> 	true, //turn on the language switch
	'DEFAULT_LANG' 				=> 	'fr', // default language
	'LANG_AUTO_DETECT' 			=> 	true, //automatically detect the language
	'LANG_LIST'					=>	'fr', //language list
	'USER_AUTH_KEY'				=>  'userAuthKey',
	'AGENCY_AUTH_KEY' 			=> 	'agencyAuthKey',
	'ADMIN_AUTH_KEY'			=>  'adminAuthKey',
	'USER_GROUP'				=>  'userGroup',
	'TMPL_ACTION_ERROR'         =>  'Public:msgPage', // message page tpl
	'TMPL_ACTION_SUCCESS'       =>  'Public:msgPage', // message page tpl
	'PSE_SYS_URL_KEY'			=>  'pse_sys_url_key',
	
	'PAYPAL_ACCOUNT'			=> 	'basile.camphuis@essec.edu',
	'PAYPAL_QUANTITY'			=>  '1',
	'PAYPAL_AMOUNT'				=>  '0.01',
	'PAYPAL_CURRENCY_CODE'		=>  'EUR',
		
	'THINK_EMAIL' => array(
			'SMTP_HOST'   => 'smtp.gmail.com', //SMTP服务器
			'SMTP_PORT'   => 465, //SMTP服务器端口   这个地方可以不用设置如果QQ邮箱的话
			'SMTP_USER'   => 'xuanzhaopeng', //SMTP服务器用户名
			'SMTP_PASS'   => 'xzp1990128', //SMTP服务器密码
			'SMTP_Secure' => 'ssl',
			'FROM_EMAIL'  => 'xuanzhaopeng@gmail.com', //发件人EMAIL
			'FROM_NAME'   => 'xuan zhaopeng', //发件人名称
			'REPLY_EMAIL' => '', //回复EMAIL（留空则为发件人EMAIL）
			'REPLY_NAME'  => '', //回复名称（留空则为发件人名称）
	),
);
