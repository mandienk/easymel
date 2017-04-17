<?php
/**
 * @package		easymel
 * @subpackage	form
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

}


/**
 * @package		easymel
 * @subpackage	form
 */
class EasyMelFormGenerator {

	private $databaseName;
	private $tableName;
	
	public function __construct($databaseName = null, $tableName = null)
	{
		$this->databaseName = $databaseName;
		$this->tableName = $tableName;
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
		
		// $ct  = CopixDB::getConnection ($this->databaseName);
		$db = new Database ('angularjs', 'angularjs', 'angularjs1', 'localhost'); // TODO : Implement singleton
		
		$request = "SELECT * FROM information_schema.COLUMNS
					WHERE TABLE_SCHEMA = '".$this->databaseName."'
					AND TABLE_NAME = '".$this->tableName."'";
		
		$stmt = $db->getPdo()->prepare($request);
		$stmt->execute();
		$result = $stmt->fetchAll();
		// $result = $ct->doQuery($request);
		if(isset($result)){return $result;}else{return false;}
	}

	/*	 
	 * Function to create a form from a database's table
	 *
	 * array	$arData	The field values
	 * array	$arError Input errors (optional)
	 * string	$databaseNane the database name (optional)
	 * string	$tableName the database table name
	 * array	$arNoFields the fields to ignore
	 * array	$arMapFields the field labels (optional)
	 * array	$arComboFields the drop-down lists (optional)
	 * array	$arRelationFields thie field relations
	 * array	$arDisabledFields the disabled fields
	 * boolean	$disabled true/false allow to disable all the form fields
	 * string	$plus allow to add a message, link, etc.
	 */
	public function getFormFromTable($arData, $arError = null, $databaseName = null, $tableName = null, $arNoFields = array(), $arMapFields = array(), $arComboFields = array(), $arRelationFields = array(), $arDisabledFields = array(), $disabled = "", $plus = "")
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
				$actionRelation = "";
				// Add the field relation if defined
				foreach($arRelationFields as $keyRelation => $valueRelation)
				{
					// echo "Colum name : ".$value->COLUMN_NAME." / id : ".$valueRelation[2]." => ";
					$actionRelation = ($value->COLUMN_NAME == $valueRelation[2])? "on".$valueRelation[0].' = "javaScript:'.$valueRelation[1].'(\''.$valueRelation[2].'\', \''.$valueRelation[3].'\');"' : "";
					$_SESSION[$valueRelation[2].$valueRelation[3].'-combo-fields'] = $arComboFields;
					$_SESSION[$valueRelation[2].$valueRelation[3].'-relation-fields'] = $arRelationFields;
					if($actionRelation) break;			
					// $id = ($value->COLUMN_NAME == $valueRelation[2])? 'id="'.$valueRelation[2].'"' : (($value->COLUMN_NAME == $valueRelation[3])? 'id="'.$valueRelation[3].'"' : "");					
					// echo "action relation : ".$actionRelation."<br />";
				}
				
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
				
				if(array_key_exists($value->COLUMN_NAME, $arComboFields))
				{
					// print_r($arRelationFields);
					// echo $actionRelation."<br />";				
					// We disable the field if it is to zero value
					if(isset($arData->$fieldName) && $arData->$fieldName == -1) // $fieldName == $value->COLUMN_NAME
					{
						$field = '<div id="'.$value->COLUMN_NAME.'" ><input '.$disabled.' type="text" disabled '.$actionRelation.' /><input type="hidden" value="-1" name="'.$value->COLUMN_NAME.'" size="15" /></div>';
					}
					else
					{
						$arOption = _dao($arComboFields[$value->COLUMN_NAME], $this->databaseName)->findAll();
					
						$field = "<div id=\"".$fieldName."\" ><select ".$disabled." name=\"".$fieldName."\" ".$actionRelation." >";
						$field .= "<option value=\"0\" >--choix ".$value->COLUMN_COMMENT."--</option>";
						foreach($arOption as $keyOption => $valueOption)
						{
							$tmp = ((array)$valueOption);						
							$myValue = array_shift($tmp);
							$name = array_shift($tmp);						
							$field .= "<option value=\"".$myValue."\" ".((isset($arData->$fieldName) && $arData->$fieldName == $myValue)?"selected":"")." >".$name."</option>";
						}
						$field .= "</select>".(($value->IS_NULLABLE == 'NO')?"&nbsp;<span style='color:red; font-weight:bold;'><img src='"._resource ("img/shared/required.png")."' /> ":"").(isset($arError->$fieldName)? "<img src='"._resource ("img/shared/required.gif")."' /> ".$arError->$fieldName:"").'</span></div>';
					}
				}
				else if($value->DATA_TYPE == "text")
				{
					$field = '<div id="'.$value->COLUMN_NAME.'" ><textarea '.$disabled.' name="'.$value->COLUMN_NAME.'" rows="3" cols="40">'.(isset($arData->$fieldName)?$arData->$fieldName:"").'</textarea>'.(($value->IS_NULLABLE == 'NO')?"&nbsp;<span style='color:red; font-weight:bold;'><img src='"._resource ("img/shared/required.png")."' /> ":"").(isset($arError->$fieldName)? "<img src='"._resource ("img/shared/required.gif")."' /> ".$arError->$fieldName:"").'</span></div>';
				}
				else
				{
					// echo $arData->$fieldName;
					$field = '<div id="'.$value->COLUMN_NAME.'" ><input '.$disabled.' '.$actionRelation.' type="text" size="'.(is_null($value->CHARACTER_MAXIMUM_LENGTH)?"10":(($value->CHARACTER_MAXIMUM_LENGTH < 50)?"10":$value->CHARACTER_MAXIMUM_LENGTH/3)).'" value="'.(isset($arData->$fieldName)?$arData->$fieldName:"").'" name="'.$value->COLUMN_NAME.'" />'.(($value->IS_NULLABLE == 'NO')?"&nbsp;<span style='color:red; font-weight:bold;'><img src='form/img/required.png' /> ":"").(isset($arError->$fieldName)? "<img src='form/img/required.png' /> ".$arError->$fieldName:"").'</span></div>';
				}
				
				$rows .= '<tr>
							<td style="padding:5px; font-weight: bold;">'.$fieldLabel.'</td>
							<td>'.$field.'</td>
						</tr>';				
			}
		}
		
		return $plus."<table>".$rows."</table>";
	}
}