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

	/*Vérifie le certificat. A decommenter et commenter la ligne du dessous
	pour le deployment*/
	//phpCAS::setCasServerCACert($cas_server_ca_cert_path);
	/*Ne verifie pas le certificat. A  remplacer par la ligne du haut*/
	phpCAS::setNoCasServerValidation();

	/*Deconnexion de l'utilisateur du serveur CAS et redirection vers la page de 
	deconnexion de l'user*/
	if (isset($_GET['logout'])) {
		phpCAS::logoutWithRedirectService('http://localhost:3000/logout');
	}
	else {
		try {
	    	phpCAS::forceAuthentication();
	    /* si l'utilisateur met trop de temps à cliquer sur >ici sur la page
	     cas.insa-toulouse une exception est reçue depuis forceAuthentication.
	     on déconnecte l'utilisateur du server CAS
	     */
		} catch (Exception $e) {
			phpCAS::logoutWithRedirectService(
				'http://localhost:3000/claimConnection?input='.
				cryptor(message_creator('',password_generator()))
			);
		}
		
		


		$login = phpCAS::getUser();
		$password = password_generator();

		/* compter le nombre d'utilisateur $login dans la base de donnée */
		$exist = $bdd->prepare('SELECT COUNT(*) AS count FROM users WHERE login = ?');
		$exist->execute(array($login));
				
		$exist = $exist->fetch()['count'];

		/* si l'utilisateur $login n'existe pas */
		if ($exist != 0) {
			/* on l'insère dans la base de donnée avec un mot de passe aléatoire
			 que l'on vient de générer*/
			$query = $bdd->prepare('UPDATE users SET password = ? WHERE login = ?');
			$query->execute(array($password,$login));
		}
		/* si l'utilisateur existe */
		else {
			/* on regénère un mot de passe aléatoire */
			$query = $bdd->prepare('INSERT INTO users (login, password) VALUES (?, ?)'); 
			$query->execute(array($login,$password));
		}
		/*redirection avec avec les paramètres pour Meteor cryptés */
		header(
			'Location: http://localhost:3000/claimConnection?input='.
			cryptor(message_creator($login,$password))
		);
	}
?>