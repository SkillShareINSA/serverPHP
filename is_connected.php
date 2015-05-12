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

if (isset($_GET['login']) && sizeof( $_GET['login']) != null) {
	$login = $_GET['login'];
		if (isset($_GET['password']) && sizeof( $_GET['password']) != null && $_GET['password'] != '') {
			$password = $_GET['password'];

			$query = $bdd->prepare('SELECT * FROM users WHERE login = ?');
			$query->execute(array($login));
			
			$user = NULL;
			$user = $query->fetch();

			if ($user == NULL) {
				echo('0');
			}
			else {
				if ($user['password'] == $password)
					echo('1');
				else 
					echo('0');
				

				$query = $bdd->prepare('UPDATE users SET password = \'\' WHERE login = ?');
				$query->execute(array($login));
			}
		}
		else {
			$query = $bdd->prepare('UPDATE users SET password = \'\' WHERE login = ?');
			$query->execute(array($login));

			echo('0');
		}
		
	}

?>