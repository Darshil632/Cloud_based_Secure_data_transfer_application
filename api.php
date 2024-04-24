<?php

session_start();

$DATA_RAW = file_get_contents("php://input");
$DATA_OBJ = json_decode($DATA_RAW);

$info = (object)[];

//check if logged in
if (!isset($_SESSION['userid'])) {

	if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type != "login" && $DATA_OBJ->data_type != "signup") {

		$info->logged_in = false;
		echo json_encode($info);
		die;
	}
}
require_once("classes/autoload.php");
$DB = new Database();
if (isset($_SESSION['userid']) && !empty($_SESSION['userid'])) {
	$arr['userid'] = "null";
	$arr['userid'] = $_SESSION['userid'];
	$sql = "select * from users where userid = :userid limit 1";
	$result = $DB->read($sql, $arr);
	if (!is_array($result)) {
		unset($_SESSION['userid']);
		$info->logged_in = false;
		echo json_encode($info);
		die;
	}
}
$Error = "";

//proccess the data
if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "signup") {

	//signup
	include("includes/signup.php");
} elseif (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "login") {
	//login
	include("includes/login.php");
} elseif (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "logout") {
	include("includes/logout.php");
} elseif (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "user_info") {

	//user info
	include("includes/user_info.php");
} elseif (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "contacts") {
	//user info
	include("includes/contacts.php");
} elseif (isset($DATA_OBJ->data_type) && ($DATA_OBJ->data_type == "chats" || $DATA_OBJ->data_type == "chats_refresh")) {
	//user info
	include("includes/chats.php");
} elseif (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "settings") {
	//user info
	include("includes/settings.php");
} elseif (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "save_settings") {
	//user info
	include("includes/save_settings.php");
} elseif (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "send_message") {
	//send message
	include("includes/send_message.php");
} elseif (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "delete_message") {
	//send message
	include("includes/delete_message.php");
} elseif (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "delete_thread") {
	//send message
	include("includes/delete_thread.php");
} elseif (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "showDecryptedMessage") {
	//send message
	include("includes/show_actual_message.php");
}


function message_left($data, $row)
{
	$image = ($row->gender == "Male") ? "ui/images/user_male.jpg" : "ui/images/user_female.jpg";
	if (file_exists($row->image)) {
		$image = $row->image;
	}
	if ($data->is_encrypt == 1) {
		$encryption_key = base64_decode($_SESSION['encryption_key']);
		$secret_key = $encryption_key;
		$method = ENCRYPT_DECRYPT_METHOD;
		$public_key = PUBLIC_KEY;
		//$decrypted_message = openssl_decrypt($data->message, $method, $secret_key, 0, $public_key);
		$decrypted_message = openssl_encrypt($data->message, $method, $secret_key, 0, $public_key);
	} else {
		$decrypted_message = $data->message;
	}

	$a = "
	<div id='message_left'>
	<div></div>
		<img  id='prof_img' src='$image'>
		<b>$row->username</b><br>
		<span id='message_span'>$decrypted_message</span><br><br>";

	if ($data->files != "" && file_exists($data->files)) {
		$a .= "<img src='$data->files' style='width:100%;cursor:pointer;' onclick='image_show(event)' /> <br>";
	}
	$a .= "<span style='font-size:11px;color:white;'>" . date("jS M Y H:i:s a", strtotime($data->date)) . "<span>
	<img id='trash' src='ui/icons/trash.png' onclick='delete_message(event)' msgid='$data->id' />";
	if ($data->is_encrypt == 1) {
		$a .= "<img id='decrypt_message' src='ui/icons/decrypt.png' onclick='showActualMessage(event)' msgid='$data->id' />";
	}
	$a .= "</div> ";

	return $a;
}

function message_right($data, $row)
{
	$image = ($row->gender == "Male") ? "ui/images/user_male.jpg" : "ui/images/user_female.jpg";
	if (file_exists($row->image)) {
		$image = $row->image;
	}
	if ($data->is_encrypt == 1) {
		$encryption_key = base64_decode($_SESSION['encryption_key']);
		$secret_key = $encryption_key;
		$method = ENCRYPT_DECRYPT_METHOD;
		$public_key = PUBLIC_KEY;
		// $decrypted_message = openssl_decrypt($data->message, $method, $secret_key, 0, $public_key);
		$decrypted_message = openssl_encrypt($data->message, $method, $secret_key, 0, $public_key);
	} else {
		$decrypted_message = $data->message;
	}

	$a = "
	<div id='message_right'>

	<div>";

	if ($data->seen) {
		$a .= "<img src='ui/images/tick.png' style=''/>";
	} elseif ($data->received) {
		$a .= "<img src='ui/images/tick_grey.png' style=''/>";
	}

	$a .= "</div>

		<img id='prof_img' src='$image' style='float:right'>
		<b>$row->username</b><br>
		<span id='message_span'>$decrypted_message</span><br><br>";

	if ($data->files != "" && file_exists($data->files)) {
		$a .= "<img src='$data->files' style='width:100%;cursor:pointer;' onclick='image_show(event)' /> <br>";
	}
	$a .= "<span style='font-size:11px;color:#888;'>" . date("jS M Y H:i:s a", strtotime($data->date)) . "<span>

		<img id='trash' src='ui/icons/trash.png' onclick='delete_message(event)' msgid='$data->id' />";
	if ($data->is_encrypt == 1) {
		$a .= "<img id='decrypt_message' src='ui/icons/decrypt.png' onclick='showActualMessage(event)' msgid='$data->id' />";
	}
	$a .= "</div>";

	return $a;
}


function message_controls()
{

	return "
	</div>
	<span onclick='delete_thread(event)' style='color:purple;cursor:pointer;'>Delete this thread </span>
	<br>
	<span style='color:purple;'>
	<input id='is_encrypt_checkbox' name='is_encrypt_checkbox' type='checkbox' onclick='checkEncryptOptionChecked(this)'/> 
	<label>Encrypt Message</label>
	</span>
	<div style='display:flex;width:100%;height:31px;'>
		<label for='message_file'><img src='ui/icons/clip.png' style='opacity:0.8;width:25px;margin:5px;cursor:pointer;' ></label>
		<input type='file' id='message_file' name='file' style='display:none' onchange='send_image(this.files)' />
		<input id='message_text' onkeyup='enter_pressed(event)' style='flex:6;border:solid thin #ccc;border-bottom:none;font-size:14px;padding:4px;' type='text' placeHolder='type your message'/>
		<input type='hidden' name='is_encrypt' id='is_encrypt' />
		<input style='flex:1;cursor:pointer;' type='button' value='send' onclick='send_message(event)'/>
	</div>
	</div>";
}
