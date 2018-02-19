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

$userid = trim(htmlspecialchars($_POST['userid']));
$hash = trim(htmlspecialchars($_POST['hash']));
$newpass = trim(htmlspecialchars($_POST['newpass']));

if ($_SESSION['pass_hash'] != $hash) {
	$out['msg'] = 'Неправильный хэш';
	exit(json_encode($out));
}

if (iconv_strlen($newpass) < 6) {
	$out['msg'] = 'Пароль не может быть менее 6 символов';
	exit(json_encode($out));
}

$user = $modx->getObject('modUser', $userid);

if (!$user) {
	$out['msg'] = 'Не найден пользователь';
	exit(json_encode($out));
}

$user->set('password', $newpass);

if ($user->save()) {
	$out['msg'] = 'Пароль успешно изменен, пожалуйста авторизуйтесь';
	$out['success'] = true;
	$out['link'] = $modx->makeUrl($modx->getOption('site_start'));
	unset($_SESSION['pass_hash']);
	exit(json_encode($out));
}


exit(json_encode($out));

?>