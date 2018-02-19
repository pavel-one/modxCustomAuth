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

$_SESSION['pass_hash'] = getHash(12);


if ($_SESSION['pass_hash']) {
	sendEmail($_SESSION['pass_hash'], $user->get('username'), 'Восстановление пароля', $user->get('id'));
	$out = array(
		'success' => true,
		'msg' => 'На почту отправлено письмо с инструкцией',
		//'user' => $modx->user->get('id')
	);
	exit(json_encode($out));
}




function getHash($number) {
	$arr = array('a','b','c','d','e','f',
	             'g','h','i','j','k','l',
	             'm','n','o','p','r','s',
	             't','u','v','x','y','z',
	             'A','B','C','D','E','F',
	             'G','H','I','J','K','L',
	             'M','N','O','P','R','S',
	             'T','U','V','X','Y','Z',
	             '1','2','3','4','5','6',
	             '7','8','9','0');

	$pass = '';
	for($i = 0; $i < $number; $i++)	{
	  $index = rand(0, count($arr) - 1);
	  $pass .= $arr[$index];
	}
	return $pass;
}

function sendEmail ($hash, $to, $subject, $id) {
	global $modx;

	$to = str_replace(' ', '', $to);
	$to = explode(',',$to);
	$from = $modx->getOption('emailsender');
	$project_name = $modx->getOption('site_name');
	$pdo = $modx->getService('pdoTools');

	$modx->getService('mail', 'mail.modPHPMailer');
	$modx->mail->set(modMail::MAIL_FROM, $from);
	$modx->mail->set(modMail::MAIL_FROM_NAME, $project_name);
	foreach ($to as $item) {
	  $modx->mail->address('to', $item);
	}
	$modx->mail->set(modMail::MAIL_SUBJECT, $subject);

	$modx->mail->set(modMail::MAIL_BODY, $pdo->getChunk('rememberPassword', array(
		'hash' => $hash,
		'id' => $id,
	)));
	$modx->mail->setHTML(true);
	if (!$modx->mail->send()) {
	    $modx->log(modX::LOG_LEVEL_ERROR,'An error occurred while trying to send the email: '.$modx->mail->mailer->ErrorInfo);
	}
	$modx->mail->reset();
}
exit(json_encode($out));



?>