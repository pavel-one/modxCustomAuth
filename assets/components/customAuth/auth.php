<?php 
define('MODX_API_MODE', true);
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/index.php';

$modx->getService('error','error.modError');
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget('FILE');
if ($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {
  	$modx->sendRedirect($modx->makeUrl($modx->getOption('site_start'),'','','full'));
}

$out = array(
	'success' => false,
	'msg' => 'Неизвестная ошибка',
	//'user' => $modx->user->get('id')
);

$user = trim(htmlspecialchars($_POST['email']));
$password = trim(htmlspecialchars($_POST['password']));

$logindata = array(
  'username' => $user,
  'password' => $password,
  'rememberme' => true
);

$user = $modx->getObject('modUser', array(
	'username' => $user,
));
if (!$user) {
	$out['msg'] = 'Такого пользователя не существует';
	exit(json_encode($out));
}

if (!$user->isMember('Users')) {
	$out['msg'] = 'Пользователь не состоит в группе пользователей';
	exit(json_encode($out));
}


$response = $modx->runProcessor('/security/login', $logindata);

if ($response->isError()) {
  $modx->log(1, 'Login error. Message: '.$response->getMessage());
  $out['msg'] = $response->getMessage();
  exit(json_encode($out));
} else {
	$out['success'] = true;
	$out['msg'] = 'Авторизация успешна';
	$out['link'] = $modx->makeUrl(2);
	exit(json_encode($out));
}

exit(json_encode($out));



?>