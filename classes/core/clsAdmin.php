<?
class clsAdmin {

	public function clsAdmin() {

	}

	/**
	* This function send e-mail to admin email
	*
	* @param $subj string - subject of e-mail
	* @param $to string - to e-mail (default - admin e-mail from config)
	* @param $params assoc array - contain data for replace placeholders
	* @param $type int. - e-mail type (1 -new user registration 2 -support request email 3 -error message notification)
	* @return
	* @Example:
	* require_once( COMMON_CLS_PATH . "clsAdmin.php" );
	* $objAdmin = New clsAdmin();
	* $objAdmin->SendAdminEmail('Test email 4 Admin','zzzzzz@gmail.com',1,array('{USER_ID}'=>'1234567','{USER_EMAIL}'=>'user@email.com','{USER_NAME}'=>'User Name','{USER_PASS}'=>'password'));
	*/
	public static function SendAdminEmail($subj, $to, $type, $params=array()){

		$i=0;

		$headers = "From:".ucfirst(SERVER_NAME)." on-line store <support@".SERVER_NAME.">\r\n";
		$headers .= "Content-Type: text/html; charset=utf-8\n";
    	$headers .= "Content-Transfer-Encoding: Quot-Printed\n\n";

    	$parser = new clsParser();
    	$parser->clear();
    	$tpl =array(1=>'admin_newuser.html',2=>'support_email.html',3=>'error_notice.html', 4=>'admin_user_password_recovery.html', 5 => 'admin_user_changed_password.html');
    	foreach($params as $k=>$v){
			$parser->setVar($k,$v);
			$i++;
		}
		$parser->setEmailTemplate($tpl[$type]);
		$body = $parser->getResult();
    	$res = clsCommon::SendEmail($to,$subj,$body,$headers);
    	if(!$res){
    		if(USE_DEBUG){
    			echo "Cannot send admin email";
    		}else{
    			// +++++ USE DEBUG +++++
    			//clsCommon::Log();
    		}
    	}
    	return true;
	}

	/**
	* This function send e-mail to user email
	*
	* @param $subj string - subject of e-mail
	* @param $to string - to e-mail (default - admin e-mail from config)
	* @param $params assoc array - contain data for replace placeholders
	* @param $type int. - e-mail type
	* @return
	* @Example:
	* require_once( COMMON_CLS_PATH . "clsAdmin.php" );
	* $objAdmin = New clsAdmin();
	* $objAdmin->SendUserEmail('Test email 4 user','zzzzzz@gmail.com',1,array('{USER_ID}'=>'1234567','{USER_EMAIL}'=>'user@email.com','{USER_NAME}'=>'User Name','{USER_PASS}'=>'password'));
	*/
	public static function SendUserEmail($subj, $to="", $type, $params=array()){
		$i=0;
		$headers = "From:".ucfirst(SERVER_NAME)." on-line store <support@".SERVER_NAME.">\r\n";
		$headers .= "Content-Type: text/html; charset=utf-8\n";
    	$headers .= "Content-Transfer-Encoding: Quot-Printed\n\n";

    	$parser = new clsParser();
    	$parser->clear();
    	$tpl = array(1=>'registration.html',2=>'support.html',3=>'rec_pass.html',4=>'adv_mail.html', 5 => 'user_changed_password.html');
		foreach($params as $k=>$v){
			$parser->setVar($k,$v);
			$i++;
		}
		//$parser->setVar("{BODY}",$body);
		$parser->setEmailTemplate($tpl[$type]);
		$body = $parser->getResult();
    	$res = clsCommon::SendEmail($to,$subj,$body,$headers);
    	if(!$res){
    		if(USE_DEBUG){
    			echo "Cannot send user email";
    		}else{
    			// +++++ USE DEBUG +++++
    			//clsCommon::Log();
    		}
    	}
    	return true;
	}
}
?>