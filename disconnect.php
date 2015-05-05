<?php
	
	require_once 'CAS/config.php';
	require_once 'CAS/CAS.php';

	phpCAS::setDebug();

	phpCAS::client(CAS_VERSION_2_0, $cas_host, $cas_port, $cas_context);

	phpCAS::setNoCasServerValidation();

	try {
		$bdd = new PDO('mysql:host=localhost;dbname=skillshareinsa', 'root', '');
	} catch (Exception $e) {
		try {
			$bdd = new PDO('mysql:host=localhost;dbname=rprevost', 'rprevost', '');
			} catch (Exception $e) {
				die('Erreur : ' . $e->getMessage());
		}
	}

if (isset($_GET['user']) && sizeof( $_GET['user']) != null) {
		$name_user = $_GET['user'];

		$query = $bdd->prepare('UPDATE users SET connected = 0 WHERE name_user = ?');
		$query->execute(array($name_user));

		phpCAS::logout();
	}

?>