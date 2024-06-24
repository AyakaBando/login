<?php
require_once( dirname(__FILE__) . '/../inc/config.php' );
require_once( dirname(__FILE__) . '/../inc/contentsConfig.php' );
require_once( dirname(__FILE__) . '/../inc/lib/DB/.userDB.class.php' );

define( 'THE_DIR_NAME',   'login' );
define( 'THE_FILE_NAME',  'index' );


if( is_numeric( $_SESSION['member']['flg'] ) )
{
    header( 'Location: /' );
    die;
}

/*
if( $_SERVER['REDIRECT_HTTPS'] != 'on' )
{
    header( 'Location: https://www.moritaalumi.co.jp/login/?id=' . $_REQUEST['id'] );
    die;
}
*/

$userquery  = new userDB();

$form = new HTML_QuickForm( 'login', 'post' );

$form->addElement( 'text',        'account',     'アカウント', array( 'class' => 'wS imeOff' ) );
$form->addElement( 'password',    'password',    'パスワード', array( 'class' => 'wS imeOff' ) );

$form->addElement( 'submit',      'submitReg',   'ログイン',   array( 'class' => 'kakunin' ) );

if( $_REQUEST['id'] ) $form->addElement( 'hidden',  'id',     $_REQUEST['id'] );

$form->setDefaults( $data );

$form->addRule( 'account',   'メールアドレスを入力してください。', 'required', null );
$form->addRule( 'password',  'パスワードを入力してください。', 'required', null );

if( isset( $_POST['submitReg'] ) )
{
    if( $_POST['account'] && $_POST['password'] && $userquery->CheckLogin( $_POST ) === false )
        $form->_errors['account'] = 'メールアドレスかPASSWORDが正しく入力されていません。';
}

$form->setRequiredNote( '<span style="font-size:80%; color:#ff0000;">下記</span><span style="font-size:80%;">の項目は必ず入力してください。</span>' );
$form->setJsWarnings( '下記の項目は必ず入力してください。', "\n\n" . TITLE );

if( $form->validate() && isset( $_POST['submitReg'] ) )
{
    //setcookie( 'forget', $_POST['account'], time() + 3600 );
    $_SESSION['member'] = $userquery->GetMemberData( $_POST );
    $_SESSION['member']['flg'] = 1;

    if( $_POST['id'] )
        header( 'Location: ../product/detail.php?id=' . $_POST['id'] );
    else
        header( 'Location: ../' );
    die;
}

$smarty->template_dir = SMARTY_TEMPLATE_PATH    . THE_DIR_NAME;
$smarty->compile_dir  = SMARTY_TEMPLATE_C_PATH  . THE_DIR_NAME;

$renderer = new HTML_QuickForm_Renderer_ArraySmarty( $smarty );
$form->accept( $renderer );
$smarty->assign( 'form', $renderer->toArray() );

$smarty->assign( 'passwordStr',  $passwordStr );
$smarty->assign( 'flg',          $flg );


$smarty->display( THE_FILE_NAME . '.html' );
?>