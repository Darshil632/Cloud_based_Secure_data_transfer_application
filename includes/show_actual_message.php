<?php
$arr['msgid'] = "null";
if (isset($DATA_OBJ->find->msgid)) {

	$arr['msgid'] = $DATA_OBJ->find->msgid;
}
unset($arr['userid']);
$sql = "select * from messages where id = :msgid limit 1";
$result = $DB->read($sql, $arr);
if (is_array($result)) {
	$row = $result[0];
	// $encryption_key = base64_decode($_SESSION['encryption_key']);
	// $secret_key = $encryption_key;
	// $method = ENCRYPT_DECRYPT_METHOD;
	// $public_key = PUBLIC_KEY;
	// $decrypted_message = openssl_decrypt($row->message, $method, $secret_key, 0, $public_key);
	$row->actual_message = $row->message;
	echo json_encode($row);
}
