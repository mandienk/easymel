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
 * Function getFormFromTable () called in getMyForm ()
 *
 * array	$arData	The field values
 * array	$arError Input errors (optional)
 * string	$databaseNane the database name (optional)
 * string	$tableName the database table name
 * array	$arNoFields the fields to ignore
 * array	$arMapFields the field labels (optional)
 * array	$arComboFields the drop-down lists (optional)
 * array	$arRelationFields the field relations : NOT IMPLEMENTED!
 * array	$arDisabledFields the disabled fields
 * boolean	$disabled true/false allow to disable all the form fields
 * string	$plus allow to add a message, link, etc.
 */
function getMyForm($obj, $db)
{
	// We build the form
	$easy = new _easyMFormGen($db);
	return $easy->getFormFromTable((isset($obj->values)? $obj->values : null), // field values
			(isset($obj->arErrorsProduit)?(object)$obj->arErrorsProduit:null), // Errors : Convert error array to object
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