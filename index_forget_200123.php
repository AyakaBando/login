<?php
require_once( dirname(__FILE__) . '/../inc/config.php' );
require_once( dirname(__FILE__) . '/../inc/contentsConfig.php' );
require_once( dirname(__FILE__) . '/../inc/lib/DB/.userDB.class.php' );

define( 'THE_DIR_NAME',   'login' );
define( 'THE_FILE_NAME',  'index_forget' );

/*
if( $_SERVER['REDIRECT_HTTPS'] != 'on' )
{
    header( 'Location: https://www.moritaalumi.co.jp/login/index_forget.php' );
    die;
}
*/

$form = new HTML_QuickForm( 'login', 'post' );

$userquery  = new userDB();

$form->addElement( 'text',      'eMail',            'メールアドレス',  array( 'class' => 'wS imeOn' ) );
//$form->addElement( 'submit',    'submitConf',       '確認',            array( 'class' => 'base01', 'id' => 'login' ) );
$form->addElement( 'submit',    'submitReg',        '送信',            array( 'class' => 'kakunin' ) );
//$form->addElement( 'submit',    'submitReturn',     '戻る',            array( 'class' => 'base02') );

$form->addRule( 'eMail',        'メールアドレスを入力してください。',       'required', null );

$form->setDefaults( $data );

if( $_POST['submitReg'] )
{
    if( $userquery->CheckMail( $_POST ) === false )
        $form->_errors['eMail']   = '入力されたメールアドレスは登録されていません。';
}

$form->setRequiredNote( '<span style="font-size:80%; color:#ff0000;">下記</span><span style="font-size:80%;">の項目は必ず入力してください。</span>' );
$form->setJsWarnings( '下記の項目は必ず入力してください。', "\n\n" . TITLE );

if ( $form->validate() )
{
/*
    //確認
    if( isset( $_POST['submitConf'] ) )
    {
        $form->freeze();
        $flg++;
    }
*/
    ///////処理///////
    if( isset( $_POST['submitReg'] ) )
    {
        $alphaNumericArray = array(
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
            '1', '2', '3', '4', '5', '6', '7', '8', '9', '0' );

        for( $i = 1; $i <= 10; $i++ )
             $passwordStr .= $alphaNumericArray[array_rand( $alphaNumericArray )];


        $userquery->MemberPassWordUpDate( $_POST['eMail'], $passwordStr );

        $mailSubject = 'パスワードの再発行';

        $mailFrom    = base64_encode( '森田アルミ工業株式会社' ) . '?= <' . $shopMailFrom . '>';

        //$mailBody .= $_POST['hatyusya']  .  '　様'  . "\n";
        $mailBody   .= '※本メールは自動配信メールです。'                                       . "\n";
        $mailBody   .= '┏━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┓'     . "\n";
        $mailBody   .= '　※本メールは、'                                                     . "\n";
        $mailBody   .= '　森田アルミ工業株式会社より会員ページへのログインパスワードを'       . "\n";
        $mailBody   .= '　変更された方にお送りしています。'                                   . "\n";
        $mailBody   .= '┗━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┛'     . "\n\n";
        $mailBody   .= '■新パスワード：' . $passwordStr                                      . "\n";
        $mailBody   .= 'ログインには上記パスワードをご利用ください。'                         . "\n";
        //$mailBody   .= '※パスワードは、MYページの「会員登録内容変更」よりご変更いただけます。' . "\n\n";
        //$mailBody   .= '今後ともどうぞをよろしくお願い申し上げます。'                 . "\n\n";
        $mailBody   .= '--------------------------------'                                     . "\n";


        $mailHeader   = "From: =?UTF-8?B?" . $mailFrom . "\n";
        $mailHeader  .= "Reply-To: "       . $mailFrom . "\n";
        $mailHeader  .= "Return-Path: "    . $mailFrom . "\n";
        $mailHeader  .= "MIME-Version: 1.0\n";
        $mailHeader  .= 'Content-Type: text/plain; charset=UTF-8' . "\n";
        $mailHeader  .= "Content-Transfer-Encoding: 8bit\n";
        $mailHeader  .= "X-mailer: PHP/" . phpversion();
        $mailSubject = "=?UTF-8?B?" . base64_encode( $mailSubject ) . "?=";
        $mailBody    = $mailBody;

        mail( $_POST['eMail'], $mailSubject, $mailBody, $mailHeader, null );

        header( 'Location: ./index_forget_thanks.php' );
        die;
    }
}



$smarty->template_dir = SMARTY_TEMPLATE_PATH    . THE_DIR_NAME;
$smarty->compile_dir  = SMARTY_TEMPLATE_C_PATH  . THE_DIR_NAME;

$renderer = new HTML_QuickForm_Renderer_ArraySmarty( $smarty );
$form->accept( $renderer );
$smarty->assign( 'form', $renderer->toArray() );

$smarty->assign( 'flg',          $flg );

$smarty->display( THE_FILE_NAME . '.html' );
?>