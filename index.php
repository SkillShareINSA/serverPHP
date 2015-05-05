<?php
	
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

	if (isset($_GET['is_connected']) && sizeof( $_GET['is_connected']) != null) {
		$name_user = $_GET['is_connected'];

		$users = $bdd->prepare('SELECT * FROM users WHERE name_user = ?');
		$users->execute(array($name_user));
		
		$user = NULL;
		$user = $users->fetch();

		if ($user == NULL) {
			echo('0');
		}
		else {
			if ($user['connected'] == 1)
				echo('1');
			else 
				echo('0');
		}
	}


	else {

		$name_user = phpCAS::getUser();

		$exist = $bdd->prepare('SELECT COUNT(*) AS count FROM users WHERE name_user = ?');
		$exist->execute(array($name_user));
				
		$exist = $exist->fetch()['count'];

		if ($exist != 0) {
			$query = $bdd->prepare('UPDATE users SET connected = 1 WHERE name_user = ?');
			$query->execute(array($name_user));
		}
		else {
			$query = $bdd->prepare('INSERT INTO users (name_user, connected) VALUES (?, 1)'); 
			$query->execute(array($name_user));
		}

		header('Location: http://localhost:3000/postConnect?user='.$name_user);
	}

	
?>

