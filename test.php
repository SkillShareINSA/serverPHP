<?php
	$algo = MCRYPT_RIJNDAEL_128;
	$key = "ma clef de crypt";
	$mode = MCRYPT_MODE_CBC;

	$keyHash = md5($key);
	$key = substr($keyHash, 0, mcrypt_get_key_size($algo, $mode));
    $iv  = substr($keyHash, 0, mcrypt_get_block_size($algo, $mode));

	$message = "Ho yeah bien ouèj !";

	?>
<?php echo(base64_encode(mcrypt_encrypt($algo,$key,$message,$mode,$iv))); ?>