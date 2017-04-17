<?php 
require_once 'form/EasyMelFormGenerator.class.php';
require_once 'database/Database.class.php';

$db = new Database ('test', 'user', 'password', 'localhost'); // TODO : Implement singleton

$request = "SELECT * FROM users
			WHERE users.user_id = 1";					

$stmt = $db->getPdo()->prepare($request);
$stmt->execute();
$result = $stmt->fetchAll();

$obj = new stdClass();
$obj->values = new stdClass();
$obj->values = $result[0];

echo getMyForm($obj, $db);

/*
 * This function create the form from the database table
 *
 * array	$arData	Les valeurs des champs
 * array	$arError Les erreurs de saisie (facultatif)
 * string	$databaseNane Le nom de la base de données (facultatif)
 * string	$tableName Le nom de la table (facultatif)
 * array	$arNoFields les champs à ignorer (facultatif)
 * array	$arMapFields Le libellé des champs (facultatif)
 * array	$arComboFields les listes déroulantes
 * array	$arRelationFields les relations entre champs
 * array	$arDisabledFields les champs désactivés
 * boolean	$disabled true/false permet de désactiver tous les champs du formulaire
 * string	$plus permet l'ajout d'un message, lien, etc.
 */
function getMyForm($obj, $db)
{
	// We build the form
	$easy = new _easyMFormGen($db);
	return $easy->getFormFromTable((isset($obj->values)? $obj->values : null),
			(isset($obj->arErrorsProduit)?(object)$obj->arErrorsProduit:null), // Convert error array to object
			'test', // database
			'users', // database table
			array('user_id'), // Fields to hide
			array('[FIELD_ID]' => '[LABEL]',					
			), // Labels to display
			array('sex' => 'sex',
					'role' => 'roles'					
			), // Drop-down lists
			null, // Relationships : NOT IMPLEMENTED
			array(), // Fields to disable
			"" // To disable the form set "disabled"
			);
}

?>