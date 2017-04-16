<?php
/**
 * @package		easymel
 * @subpackage	datasource
 * @author		Ma'ndien KAKEZ
 * @copyright	Ma'ndien KAKEZ (2014)
 * @link		
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @experimental
 */

/**
 * @package		easymel
 * @subpackage	datasource
 */
class _easyData extends EasymelDatasource {

}

class EasymelDatasource {

	private $databaseName;
	private $tableName;
	
	public function __construct($databaseName = null, $tableName = null)
	{
		$this->databaseName = $databaseName;
		$this->tableName = $tableName;
	}
	
	/**
	 * Permet la récupération de la description d'une table
	 */
	public function getTableDescription($databaseName = null, $tableName = null)
	{
		if($databaseName != null && $tableName != null)
		{
			$this->databaseName = $databaseName;
			$this->tableName = $tableName;
		}
		
		$ct  = CopixDB::getConnection ($this->databaseName);	
		$request = "SELECT * FROM information_schema.COLUMNS
					WHERE TABLE_SCHEMA = '".$this->databaseName."'
					AND TABLE_NAME = '".$this->tableName."'";
		$result = $ct->doQuery($request);
		if(isset($result)){return $result;}else{return false;}
	}
	
	/*
	 * permet la création du formulaire EXTJS correspondant à une table
	 */	
	public function getExtjsFormFromTable($arData, $arError = null, $databaseName = null, $tableName = null, $arNoFields = array(), $arMapFields = array())
	{
		$fields = "";		
		if($databaseName != null && $tableName != null)
		{
			$this->databaseName = $databaseName;
			$this->tableName = $tableName;
		}
		
		$arTableDescription = $this->getTableDescription($this->databaseName, $this->tableName);
				
		foreach($arTableDescription as $key => $value)
		{
			// Exclut les champs passés en paramètres
			if(!in_array($value->COLUMN_NAME, $arNoFields))
			{
				$fieldLabel = array_key_exists($value->COLUMN_NAME, $arMapFields)?$arMapFields[$value->COLUMN_NAME]:$value->COLUMN_NAME;
				$fieldLabel = ucfirst(str_replace('_', ' ', $fieldLabel));
				$fieldName = $value->COLUMN_NAME;
				if(isset($fields)) $fields .= ",";
				$fields .= "{
	    	                xtype: 'fieldcontainer',	    	                
	    	                id: 'customer-".$fieldName."-container',
	    	                layout: 'hbox',
							items: [{
								xtype: 'textfield',
								name: '".$fieldName."',
								fieldLabel: '".$fieldLabel."',
								labelWidth: 150,
								margin: '0 2 0 0',
								value: '".(isset($arData->$fieldName)?$arData->$fieldName:"")."',
								width: ".(is_null($value->CHARACTER_MAXIMUM_LENGTH)?"250":(($value->CHARACTER_MAXIMUM_LENGTH < 50)?"250":$value->CHARACTER_MAXIMUM_LENGTH+300))."
								// width: 350
							},{
								name: 'customer-".$fieldName."-validation',
								xtype: 'displayfield',
								margins: '0 0 0 5',
								value : '".addslashes((($value->IS_NULLABLE == 'NO')?"&nbsp;<span style='color:red; font-weight:bold;'>* </span>":"").(isset($arError->$fieldName)?"&nbsp;<span style='color:red; font-weight:bold;'>".$arError->$fieldName."</span>":""))."'
							}]}";
						  
				/*
				// Renomme les champs du formulaire
				$fieldLabel = array_key_exists($value->COLUMN_NAME, $arMapFields)?$arMapFields[$value->COLUMN_NAME]:$value->COLUMN_NAME;
				// Supprimme les "_" et met le premier caractère en majuscule
				$fieldLabel = ucfirst(str_replace('_', ' ', $fieldLabel));
				$fieldName = $value->COLUMN_NAME;
				$rows .= '<tr><td style="padding:5px; font-weight: bold;">'.$fieldLabel.'</td><td><input type="text" value="'.(isset($arData->$fieldName)?$arData->$fieldName:"").'" name="'.$value->COLUMN_NAME.'">'.(($value->IS_NULLABLE == 'NO')?"&nbsp;<span style='color:red; font-weight:bold;'>* ":"").(isset($arError->$fieldName)?$arError->$fieldName:"").'</span></td></tr>';				
				*/
			}
		}
		
		return $fields;
		// return "<table>".$rows."</table>";
	}

	/*
	 * permet la création du formulaire HTML correspondant à une table
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
	public function getFormFromTable($arData, $arError = null, $databaseName = null, $tableName = null, $arNoFields = array(), $arMapFields = array(), $arComboFields = array(), $arRelationFields = array(), $arDisabledFields = array(), $disabled = "", $plus = "")
	{
		// print_r($arDisabledFields);
	
		$checkIfDisabled = false;
		if(empty($disabled)) $checkIfDisabled = true;
	
		$rows = "";		
		if($databaseName != null && $tableName != null)
		{
			$this->databaseName = $databaseName;
			$this->tableName = $tableName;
		}
		
		$arTableDescription = $this->getTableDescription($this->databaseName, $this->tableName);
				
		// print_r($arTableDescription);
		// echo "<hr />";
		// die();
				
		foreach($arTableDescription as $key => $value)
		{
			// echo "tour : ".$key." *** <br />";
			// Exclut les champs passés en paramètres
			if(!in_array($value->COLUMN_NAME, $arNoFields))
			{
				// Renomme les champs du formulaire
				$fieldLabel = array_key_exists($value->COLUMN_NAME, $arMapFields)?$arMapFields[$value->COLUMN_NAME]:$value->COLUMN_NAME;
				// Supprimme les "_" et met le premier caractère en majuscule
				$fieldLabel = ucfirst(str_replace('_', ' ', $fieldLabel));
				$fieldName = $value->COLUMN_NAME;
				/*
				$rows .= '<tr><td style="padding:5px; font-weight: bold;">'.$fieldLabel.'</td><td><input type="text" value="'.(isset($arData->$fieldName)?$arData->$fieldName:"").'" name="'.$value->COLUMN_NAME.'">'.(($value->IS_NULLABLE == 'NO')?"&nbsp;<span style='color:red; font-weight:bold;'>* ":"").(isset($arError->$fieldName)?$arError->$fieldName:"").'</span></td></tr>';				
				*/
				
				$actionRelation = "";
				// Ajoute la relation du champ si définit
				foreach($arRelationFields as $keyRelation => $valueRelation)
				{
					// echo "Colum name : ".$value->COLUMN_NAME." / id : ".$valueRelation[2]." => ";
					$actionRelation = ($value->COLUMN_NAME == $valueRelation[2])? "on".$valueRelation[0].' = "javaScript:'.$valueRelation[1].'(\''.$valueRelation[2].'\', \''.$valueRelation[3].'\');"' : "";
					CopixSession::set ($valueRelation[2].$valueRelation[3].'-combo-fields', $arComboFields);
					CopixSession::set ($valueRelation[2].$valueRelation[3].'-relation-fields', $arRelationFields);
					if($actionRelation) break;
					
					// $id = ($value->COLUMN_NAME == $valueRelation[2])? 'id="'.$valueRelation[2].'"' : (($value->COLUMN_NAME == $valueRelation[3])? 'id="'.$valueRelation[3].'"' : "");
					
					// echo "action relation : ".$actionRelation."<br />";
				}
				
				// echo " ==> relation ".$actionRelation."<br />";
				
				if($checkIfDisabled)
				{
					// On réinitialise
					$disabled = "";
					// Si le champ doit être désactivé
					if(in_array($value->COLUMN_NAME, $arDisabledFields))
					{						
						$disabled = "disabled";
					}					
				}					
				
				if(array_key_exists($value->COLUMN_NAME, $arComboFields))
				{
					// print_r($arRelationFields);
					// echo $actionRelation."<br />";
				
					// On désactive le champ si il est à la valeur zéro					
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
					$field = '<div id="'.$value->COLUMN_NAME.'" ><input '.$disabled.' '.$actionRelation.' type="text" size="'.(is_null($value->CHARACTER_MAXIMUM_LENGTH)?"10":(($value->CHARACTER_MAXIMUM_LENGTH < 50)?"10":$value->CHARACTER_MAXIMUM_LENGTH/3)).'" value="'.(isset($arData->$fieldName)?$arData->$fieldName:"").'" name="'.$value->COLUMN_NAME.'" />'.(($value->IS_NULLABLE == 'NO')?"&nbsp;<span style='color:red; font-weight:bold;'><img src='"._resource ("img/shared/required.png")."' /> ":"").(isset($arError->$fieldName)? "<img src='"._resource ("img/shared/required.gif")."' /> ".$arError->$fieldName:"").'</span></div>';
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