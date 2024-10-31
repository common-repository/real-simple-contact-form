<?php
/*
Plugin Name: Real Simple Contact Form
Plugin URI: 
Description: Real Simple Contact Form for Wordpress
Version: 0.5
Author: Parasmani
Author URI: http://www.blogdemy.com
*/

register_activation_hook(__FILE__,"create_contact_page");
register_activation_hook(__FILE__,"set_contact_form_options");
register_deactivation_hook(__FILE__,"unset_contact_form_options");

add_action('admin_menu', 'contact_form_menu');

/*Add short code*/
add_shortcode( 'realsimplecontactform', 'contact_form_function' );

function contact_form_menu() {
	add_options_page('Real Simple Contact Form Options', 'Real Simple Contact Form', 8, 'real-simple-contact-form-options', 'real_simple_contact_form_options_page');
}

function set_contact_form_options() {
	global $user_email;
	$real_simple_contact_form_options = array('email_address' => $user_email);
	add_option('real_simple_contact_form_options',$real_simple_contact_form_options);	
}

function unset_contact_form_options() {
	delete_option('real_simple_contact_form_options');
}

function real_simple_contact_form_options_page() {
	$real_simple_contact_form_options = get_option('real_simple_contact_form_options');

	if (isset($_POST['real_simple_contact_form_settings']))
        {
		$real_simple_contact_form_options['email_address'] = isset($_POST['email_address']) ? $_POST['email_address'] : $real_simple_contact_form_options['email_address'] ;
                update_option('real_simple_contact_form_options', $real_simple_contact_form_options);
?>
                <p><strong>"Settings Saved"</strong></p>
<?php
        }
?>

        <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
	<h2>Real Simple Contact Form Settings</h2>
	<p>Set email address where you want contact form entries to be mailed. 
        <p>Email Address : 
	<input style="width:200px;" name="email_address" type="text" value="<?php echo $real_simple_contact_form_options['email_address']; ?>" />
        <br/>
      <div class="submit">
		<input type="submit" name="real_simple_contact_form_settings" value="<?php echo 'Save Changes'; ?>" />
        </div>
        <hr />
	</form>
<?php
}

/*Add contact form*/
function contact_form_function( $atts ){

	if (isset($_POST['real_simple_contact_form_data']))
	{
		if (empty($_POST['contact_name'])) 
		{
			echo "Please Enter Name";
		}
		elseif (empty($_POST['contact_email'])) 
		{
			echo "Please Enter Email Address";
		}
		elseif (!filter_var($_POST['contact_email'], FILTER_VALIDATE_EMAIL)) {
			echo "Please Enter Correct Email Address";
		}		
		elseif (empty($_POST['contact_subject'])) 
		{
			echo "Please Enter Subject";
		}
		elseif (empty($_POST['contact_desc'])) 
		{
			echo "Please Enter Description";
		}
		else {
			/*everything ok.. lets process */
			$name = $_POST['contact_name'];
			$email_id = $_POST['contact_email'];
			$subject = $_POST['contact_subject'];
			$desc = $_POST['contact_desc'];
			$retmsg = process_contact_form($name,$email_id,$subject,$desc);
		}

		$name = $_POST['contact_name'];
		$email_id = $_POST['contact_email'];
		$subject = $_POST['contact_subject'];
		$desc = $_POST['contact_desc'];

	}
	else {
		$name = "";
		$email_id = "";
		$subject = "";
		$desc = "";
	}

?>

        <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
        <p>Name : <br />
	<input style="width:250px;" name="contact_name" type="text" value="<?php echo $name; ?>" /> <br />
        <p>Email Address : <br />
	<input style="width:250px;" name="contact_email" type="text" value="<?php echo $email_id; ?>" /><br />
        <p>Subject : <br />
	<input style="width:250px;" name="contact_subject" type="text" value="<?php echo $subject; ?>" /><br />
        <p>Description : <br />
	<textarea name="contact_desc" rows="8" cols="50" ><?php echo $desc; ?></textarea>         <br />
<!--	<?php wp_nonce_field('ecfa261455','ecfnf'); ?> -->

      <div class="submit">
		<input type="submit" name="real_simple_contact_form_data" value="<?php echo 'Send'; ?>" />
        </div>
        <hr />
	</form>

<?php
}

/*Create contact form*/
function create_contact_page ()
{
	global $user_ID;

	$page['post_type']    = 'page';
	$page['post_content'] = '[realsimplecontactform]';
	$page['post_parent']  = 0;
	$page['post_author']  = $user_ID;
	$page['post_status']  = 'publish';
	$page['post_title']   = 'Contact Us';
	$pageid = wp_insert_post ($page);
	if ($pageid == 0) { /* Page Add Failed */ }
}

/*Delete page*/

/*Process Form*/
function process_contact_form ($name,$email_id,$subject,$desc)
{
//	echo "processing";
//	$msg = 'Name - ' . $name . '\n';
//	$msg = 'Message --' . '\n';

	$real_simple_contact_form_options = get_option('real_simple_contact_form_options');
	$to = $real_simple_contact_form_options['email_address'];

	$subject = $subject;
	$message = $desc;
	$headers = 'From: ' . $email_id . "\r\n" . 'X-Mailer: PHP/' . phpversion();

	mail($to, $subject, $message, $headers);
}

?>
