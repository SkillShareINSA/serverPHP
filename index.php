<?php
	/*Generateur de string aleatoire de taille définie par la variable locale $lenght*/
	function password_generator() {
		$length = 10;

		$randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);

		return $randomString;
	}
	
	/* Crypte le message reçu ave le chiffrement
	 *  Rijndael-128bits et retourne le message crypté au format Base64
	*/
	function cryptor($data) {
		$algo = MCRYPT_RIJNDAEL_128;
		$key = "fh2g 0f??qg+";
		$mode = MCRYPT_MODE_CBC;

		$keyHash = md5($key);
		$key = substr($keyHash, 0, mcrypt_get_key_size($algo, $mode));
	    $iv  = substr($keyHash, 0, mcrypt_get_block_size($algo, $mode));

	    return base64_encode(mcrypt_encrypt($algo,$key,$data,$mode,$iv));
	}

	/* Formate un message avec un mot de passe aléatoire suivi d'un salt et
	 * de l'identifiant utilisateur
	*/ 
	function message_creator($login,$password) {
		$salt = "£Cf1Asv( %";
		return $password.$salt.$login;
	}
	
	try {
		$bdd = new PDO('mysql:host=localhost;dbname=skillshareinsa', 'root', '');
	} catch (Exception $e) {
		try {
			$bdd = new PDO('mysql:host=localhost;dbname=rprevost', 'rprevost', '');
			} catch (Exception $e) {
				die('Erreur : ' . $e->getMessage());
		}
	}

	require_once 'CAS/config.php';
	require_once 'CAS/CAS.php';

	phpCAS::setDebug();

	phpCAS::client(CAS_VERSION_2_0, $cas_host, $cas_port, $cas_context);

	phpCAS::setNoCasServerValidation();
	phpCAS::forceAuthentication();

	if (isset($_REQUEST['logout'])) {
		phpCAS::logout();
	}


	$login = phpCAS::getUser();
	$password = password_generator();

	$exist = $bdd->prepare('SELECT COUNT(*) AS count FROM users WHERE login = ?');
	$exist->execute(array($login));
			
	$exist = $exist->fetch()['count'];

	if ($exist != 0) {
		$query = $bdd->prepare('UPDATE users SET password = ? WHERE login = ?');
		$query->execute(array($password,$login));
	}
	else {
		$query = $bdd->prepare('INSERT INTO users (login, password) VALUES (?, ?)'); 
		$query->execute(array($login,$password));
	}
	//echo('Location: http://localhost:3000/claimConnection/'.cryptor(message_creator($login,$password)));
	header('Location: http://localhost:3000/claimConnection?input='.cryptor(message_creator($login,$password)));
?>