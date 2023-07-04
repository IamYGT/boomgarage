<?php 

//********************************************
//	ThemeForest Check
//***********************************************************
function themeforest_check(){
	$json = file_get_contents($_POST['url']);

    D($json);

    die;
	if(isset($json['error'])){
		echo "error";
	}  else {
		echo "success";
	}
	
	die;
}

add_action("wp_ajax_themeforest_check", "themeforest_check");
add_action("wp_ajax_nopriv_themeforest_check", "themeforest_check");

//********************************************
//	Contact Form
//***********************************************************
function send_contact_form(){
	global $awp_options;
	
	$to_Email       = (isset($awp_options['contact_email']) && !empty($awp_options['contact_email']) ? $awp_options['contact_email'] : get_bloginfo('admin_email')); //Replace with recipient email address
    $subject        = __('Message from contact form', 'automotive'); //Subject line for emails
    
    //check $_POST vars are set, exit if any missing
    if(!isset($_POST["userName"]) || !isset($_POST["userEmail"]) || !isset($_POST["userMessage"])) {
        die();
    }

    //Sanitize input data using PHP filter_var().
    $user_Name        = filter_var($_POST["userName"], FILTER_SANITIZE_STRING);
    $user_Email       = filter_var($_POST["userEmail"], FILTER_SANITIZE_EMAIL);
    $user_Message     = stripslashes($_POST["userMessage"]);
    
    header('Content-type: application/json');
    $return = array(
        "message" => "",
        "success" => "yes"
    );

    //additional php validation
    if(strlen($user_Name) < 4) {
        $return['message'] = __("Name is too short or empty.", "automotive");
        $return['success'] = "no";
    }

    if(!filter_var($user_Email, FILTER_VALIDATE_EMAIL)) {
        $return['message'] = __("Please enter a valid email.", "automotive");
        $return['success'] = "no";
    }

    if(strlen($user_Message) < 5) {
        $return['message'] = __("Too short message! Please enter something.", "automotive");
        $return['success'] = "no";
    }
    
    //proceed with PHP email.
    $headers = array();
    $headers[] = 'From: ' . $user_Email;
    
    if($return['success'] == "yes") {
    
        $sentMail = @wp_mail($to_Email, $subject, $user_Message .'  -'.$user_Name, $headers);

        if(!$sentMail)  {
            $return['message'] = __("Could not send mail.", "automotive");
            $return['success'] = "no";
        } else {
            $return['message'] = __('Hi ', 'automotive') . $user_Name . '. ' . __('Your email has been delivered.', 'automotive');
        }
    }

    echo json_encode($return);
	
	die;
}

add_action("wp_ajax_send_contact_form", "send_contact_form");
add_action("wp_ajax_nopriv_send_contact_form", "send_contact_form");


//********************************************
//  Ajax Login
//***********************************************************
function ajax_login(){
    $username = $_POST['username'];
    $password = $_POST['password'];
    $nonce    = $_POST['nonce'];
    $remember = (isset($_POST['remember_me']) && !empty($_POST['remember_me']) ? $_POST['remember_me'] : "");

    if ( wp_verify_nonce( $nonce, 'ajax_login_none' ) && !empty($username) && !empty($password) ) {
        $creds = array();

        $creds['user_login']    = sanitize_text_field($username);
        $creds['user_password'] = sanitize_text_field($password);
        $creds['remember_me']   = sanitize_text_field(($remember == "yes" ? true : false));

        $user = wp_signon( $creds, false );

        if ( ! is_wp_error($user) ) {
            echo "success";
        }
    }

    die;
}

add_action("wp_ajax_ajax_login", "ajax_login");
add_action("wp_ajax_nopriv_ajax_login", "ajax_login");

?>