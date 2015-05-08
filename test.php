<?php
	include('Crypt/Crypt/AES.php');

	$clef = "abcdefghijklmnop";

	$cipher = new Crypt_AES(CRYPT_AES_MODE_OFB);
	$cipher->setKey($clef);

	$texte_clair = "texte_clair";
	echo $texte_clair;
	echo $cipher->encrypt($texte_clair);
	echo $cipher->decrypt($cipher->encrypt($texte_clair));
?>