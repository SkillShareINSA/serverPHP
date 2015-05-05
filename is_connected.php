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

?>