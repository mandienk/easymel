<?php
/**
 * This example show you how simple is to generate your form from a batabase table.
 * 
 * Notice : For now it generates basic html form, but I will do my best to make it powerfull soon.
 * For any suggestion : m.kakez@gmail.com
 */

// First we need to include necessary class
require_once 'form/EasyMelFormGenerator.class.php';
require_once 'database/Database.class.php';

// Before creating our form we can get data to fill the form with
$db = new Database ('test', 'user', 'password', 'localhost'); // TODO : Implement singleton
$obj = $db->get ("users", "user_id", 1);
 
// Finaly we build the form and...
$easy = new _easyMFormGen($db);

// ...display it. That's all!
echo $easy->getFormFromTable(
		(isset($obj->objValues)? $obj->objValues : null), // field values object
		(isset($obj->arErrorsProduit)?(object)$obj->arErrorsProduit:null), // Errors : Convert error array to object
		'test', // database name
		'users', // table name
		array('user_id'), // Fields to hide
		array('[FIELD_ID]' => '[LABEL]'), // Labels to display
		array('sex' => 'sex',
			  'role' => 'roles'), // Drop-down lists
		null, // Relationships : NOT IMPLEMENTED
		array(), // Fields to disable
		"" // To disable the form set "disabled"
		);

?>