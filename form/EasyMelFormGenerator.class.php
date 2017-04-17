<?php
/**
 * This file contains all necessary stuff to generate form
 * @package		easymel
 * @subpackage	form
 * @name		EasyMelFormGenerator (class name)
 * @author		Ma'ndien KAKEZ
 * @link		https://github.com/mandienk/easymel
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

require_once 'database/Database.class.php';

/**
 * @package		easymel
 * @subpackage	form
 */
class _easyMFormGen extends EasyMelFormGenerator {
	// Used as alias / shortname
}

/**
 * @package		easymel
 * @subpackage	form
 */
class EasyMelFormGenerator {

	private $params;
	
	public function __construct(iDatabase $database)
	{
		$this->params = $database->getParams();
	}
	
	/**
	 * Function to get the table description
	 */
	public function getTableDescription($databaseName = null, $tableName = null)
	{
		if($databaseName != null && $tableName != null)
		{
			$this->databaseName = $databaseName;
			$this->tableName = $tableName;
		}
		
		$db = new Database ($this->params->db, $this->params->user, $this->params->pass, $this->params->host); // TODO : Implement singleton
		
		$request = "SELECT * FROM information_schema.COLUMNS
					WHERE TABLE_SCHEMA = '".$this->databaseName."'
					AND TABLE_NAME = '".$this->tableName."'";
		
		$stmt = $db->getPdo()->prepare($request);
		$stmt->execute();
		$result = $stmt->fetchAll();
		if(isset($result)){return $result;}else{return false;}
	}

	/*	 
	 * Function to create a form from a database's table
	 *
	 * object	$objData			The field values
	 * array	$arError			Input errors (optional)
	 * string	$databaseNane		the database name (optional)
	 * string	$tableName			the database table name
	 * array	$arNoFields			the fields to ignore
	 * array	$arMapFields		the field labels (optional)
	 * array	$arComboFields		the drop-down lists (optional)
	 * array	$arRelationFields	the field relations : NOT IMPLEMENTED!
	 * array	$arDisabledFields	the disabled fields
	 * boolean	$disabled			true/false allow to disable all the form fields
	 * string	$plus				allow to add a message, link, etc.
	 * boolean	$noForm				true/false To add HTML form tag. default to false
	 * string	$action				the action script
	 * string	$method				post or get (optional)
	 */
	public function getFormFromTable($objData, $arError = null, $databaseName = null, $tableName = null, $arNoFields = array(), $arMapFields = array(), $arComboFields = array(), $arRelationFields = array(), $arDisabledFields = array(), $disabled = "", $plus = "", $noForm = false, $action = "", $method = "")
	{
		$checkIfDisabled = false;
		if(empty($disabled)) $checkIfDisabled = true;
	
		$rows = "";		
		if($databaseName != null && $tableName != null)
		{
			$this->databaseName = $databaseName;
			$this->tableName = $tableName;
		}
		
		$arTableDescription = $this->getTableDescription($this->databaseName, $this->tableName);
		
		// Adding form tag
		$formTagStart = $formTagEnd = "";
		if(!$noForm)
		{
			$formTagStart = "<form action = '".$action."' method = '".$method."'>";
			$formTagEnd = "<input type='submit' value='submit'/></form>";
		}
		
		foreach($arTableDescription as $key => $value)
		{
			// Excludes fields passed in parameters
			if(!in_array($value->COLUMN_NAME, $arNoFields))
			{
				// Rename form fields
				$fieldLabel = array_key_exists($value->COLUMN_NAME, $arMapFields)?$arMapFields[$value->COLUMN_NAME]:$value->COLUMN_NAME;
				// Deletes "_" and set to uppercase the first character
				$fieldLabel = ucfirst(str_replace('_', ' ', $fieldLabel));
				$fieldName = $value->COLUMN_NAME;				
				$actionRelation = ""; // Not implemented
				
				if($checkIfDisabled)
				{
					// We reset
					$disabled = "";
					// If the field must be disabled
					if(in_array($value->COLUMN_NAME, $arDisabledFields))
					{						
						$disabled = "disabled";
					}					
				}					
				
				// If drop-down fields
				if(array_key_exists($value->COLUMN_NAME, $arComboFields))
				{
					$db = new Database ($this->databaseName, $this->params->user, $this->params->pass, $this->params->host); // TODO : Implement singleton					
					$request = "SELECT * FROM ".$arComboFields[$value->COLUMN_NAME];						
					
					$stmt = $db->getPdo()->prepare($request);
					$stmt->execute();
					$arOption = $stmt->fetchAll();
				
					$field = "<div id=\"".$fieldName."\" ><select ".$disabled." name=\"".$fieldName."\" ".$actionRelation." >";
					$field .= "<option value=\"0\" >--choose ".$value->COLUMN_COMMENT."--</option>";
					foreach($arOption as $keyOption => $valueOption)
					{
						$tmp = ((array)$valueOption);						
						$myValue = array_shift($tmp);
						$name = array_shift($tmp);						
						$field .= "<option value=\"".$myValue."\" ".((isset($objData->$fieldName) && $objData->$fieldName == $myValue)?"selected":"")." >".$name."</option>";
					}
					$field .= "</select>".(($value->IS_NULLABLE == 'NO')?"&nbsp;<span style='color:red; font-weight:bold;'><img src='form/img/required.png' /> ":"").(isset($arError->$fieldName)? "<img src='form/img/required.png' /> ".$arError->$fieldName:"").'</span></div>';
				}
				else if($value->DATA_TYPE == "text") // Textarea
				{
					$field = '<div id="'.$value->COLUMN_NAME.'" ><textarea '.$disabled.' name="'.$value->COLUMN_NAME.'" rows="3" cols="40">'.(isset($objData->$fieldName)?$objData->$fieldName:"").'</textarea>'.(($value->IS_NULLABLE == 'NO')?"&nbsp;<span style='color:red; font-weight:bold;'><img src='"._resource ("img/required.png")."' /> ":"").(isset($arError->$fieldName)? "<img src='"._resource ("img/required.png")."' /> ".$arError->$fieldName:"").'</span></div>';
				}
				else // classical field : input tag
				{
					$field = '<div id="'.$value->COLUMN_NAME.'" ><input '.$disabled.' '.$actionRelation.' type="text" size="'.(is_null($value->CHARACTER_MAXIMUM_LENGTH)?"10":(($value->CHARACTER_MAXIMUM_LENGTH < 50)?"10":$value->CHARACTER_MAXIMUM_LENGTH/3)).'" value="'.(isset($objData->$fieldName)?$objData->$fieldName:"").'" name="'.$value->COLUMN_NAME.'" />'.(($value->IS_NULLABLE == 'NO')?"&nbsp;<span style='color:red; font-weight:bold;'><img src='form/img/required.png' /> ":"").(isset($arError->$fieldName)? "<img src='form/img/required.png' /> ".$arError->$fieldName:"").'</span></div>';
				}
				
				$rows .= '<tr>
							<td style="padding:5px; font-weight: bold;">'.$fieldLabel.'</td>
							<td>'.$field.'</td>
						</tr>';				
			}
		}
		
		return $plus.$formTagStart."<table>".$rows."</table>".$formTagEnd;
	}
}