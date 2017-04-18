# easymel
EasyMel is a PHP library to automate and simplificate application creation. For example form/EasyMelFormGenerator.class.php generate form from a database's table for you

### EasyMelFormGenerator Class

This class allows the generation of an HTML form from a database table.

#### Let's see how it works with a simple example :

1. First we need to include necessary class
```php
require_once 'form/EasyMelFormGenerator.class.php';
require_once 'database/Database.class.php';
```

2. Before creating our form we can get data to fill the form with
```php
$db = new Database ('test', 'user', 'password', 'localhost'); // TODO : Implement singleton
$obj = $db->get ("users", "user_id", 1);
```
 
3. Finaly we build the form and...
```php
$easy = new _easyMFormGen($db);
```

4. ...display it. That's all!
```php
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
```
#### Let's see the live demo : [demo example-basic.php](http://mandien.hd.free.fr/prod/easymel/example-basic.php)

#### You can get the full example file here : [example-basic.php](https://github.com/mandienk/easymel/blob/master/example-basic.php)

#### Please give me you feedback at <m.kakez@gmail.com>

