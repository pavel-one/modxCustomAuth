<?php 
define('MODX_API_MODE', true);
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/index.php';

$modx->getService('error','error.modError');
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget('FILE');
if ($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {
  	$modx->sendRedirect($modx->makeUrl($modx->getOption('site_start'),'','','full'));
}

$username = trim(htmlspecialchars($_POST['email']));
$password = trim(htmlspecialchars($_POST['password']));
$password_confirm = trim(htmlspecialchars($_POST['repeat_password']));
$email = trim(htmlspecialchars($_POST['email']));
$phone = trim(htmlspecialchars($_POST['phone']));
$phone = str_replace(' ', '', $phone);
$phone = str_replace('-', '', $phone);

$fullname = trim(htmlspecialchars($_POST['name']));
$city = trim(htmlspecialchars($_POST['city']));

$out = array(
	'success' => false,
	'msg' => 'Неизвестная ошибка',
	//'user' => $modx->user->get('id')
);

if ($password !== $password_confirm) {
	$out['msg'] = 'Пароли не совпадают';
	exit(json_encode($out));
}

$count = $modx->getCount('modUser', array('username' => $username));
if($count){
	$out['msg'] = 'Пользователь с таким email уже существует';
	exit(json_encode($out));
}
/*
$count = $modx->getCount('modUser', array('email' => $email));
if($count){
	$out['msg'] = 'Пользователь с таким email уже существует';
	exit(json_encode($out));
}
*/
if(iconv_strlen($password) < 6){
	$out['msg'] = 'Ваш пароль менее 6 символов';
	exit(json_encode($out));
}
if (!$username || !$password || !$email || !$city || !$fullname) {
	$out['msg'] = 'Не заполнено одно из обязательных полей';
	exit(json_encode($out));
}

$user = $modx->newObject('modUser');
$user->set('username', $username);
$user->set('password', $password);
$user->set('active', false);
$user->save();
// создаем профиль
$profile = $modx->newObject('modUserProfile');
$profile->set('fullname', $fullname);
$profile->set('email', $email);
$profile->set('city', $city);
$profile->set('phone', $phone);
$user->addOne($profile);




if ($profile->save() && $user->save()) {
	$out['success'] = true;
	$out['msg'] = 'Регистрация прошла успешна, ожидайте проверки ваших данных';

	$groupsList = array('Users');
	$groups = array();
	foreach($groupsList as $groupName){
	  // получаем группу по имени
	  $group = $modx->getObject('modUserGroup', array('name' => $groupName));
	  // создаем объект типа modUserGroupMember
	  $groupMember = $modx->newObject('modUserGroupMember');
	  $groupMember->set('user_group', $group->get('id'));
	  $groupMember->set('role', 1); // 1 - это членство с ролью Member
	  $groups[] = $groupMember;
	}
	$user->addMany($groups);
	$user->save();


	$msg = array(
		'Email' => $email,
		'Фио' => $fullname,
		'Город' => $city,
		'Телефон' => $phone,
		'Ссылка' => '<a href="'.$modx->getOption('site_url').'manager/?a=security/user/update&id='.$user->get('id').'" target="_blank">перейти к редактированию</a>',
	);
	sendEmailAdmin($msg, 'Новый пользователь зарегестрировался и ожидает проверки!', $modx->getOption('ms2_email_manager'), 'Новый пользователь');
	unset($msg['Ссылка']);
	sendEmailAdmin($msg, 'Вы успешно зарегестрировались, ожидайте проверки!', $email, 'Успешная регистрация');
	exit(json_encode($out));
}

function sendEmailAdmin($msg, $text, $to, $subject) {
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

	$modx->mail->set(modMail::MAIL_BODY, $pdo->getChunk('regAdminSent', array(
		'data' => $msg,
		'text' => $text
	)));
	$modx->mail->setHTML(true);
	if (!$modx->mail->send()) {
	    $modx->log(modX::LOG_LEVEL_ERROR,'An error occurred while trying to send the email: '.$modx->mail->mailer->ErrorInfo);
	}
	$modx->mail->reset();
}


exit(json_encode($out));
?>