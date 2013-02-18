MongoJacket
=====================
A simple abstraction layer for [PHP MongoDB Driver](http://www.php.net/manual/en/book.mongo.php)
## Build Matrix
- Current Build: 0.0.1_nb
- Stability: Unstable
- Compatibility:
  - MongoDB >= 1.5.x
  - PHP >= 5.4.x


## Table of contents
* [Quick Start](#quick-start)
* [API](#api)
* [Callbacks](#callbacks)
* [Exception Handling](#exception-handling)
* [Binding of Custom Methods](#binding-of-custom-methods)
* [Middleware](#middleware)
* [Validator](#validator)
* [ToDos](#todos)


## Quick Start

MongoJacke is not one of those bloated ODMs you encounter everyday. It essentially gives developers an interface similar to native Mongo driver. In addition to that, MongoJacke allows developer to add custom methods, callbacks and validation rules.

### Installation
MongoJacket is not ready for installation with Composer. For now you need to follow these manual steps to add MongoJacket to your project.

```php
// inclue MongoJacket file
    include_once("MongoJacket/index.php");
// register autoloader
    spl_autoload_register('MongoJacket\MongoJacketAutoloader');
```


## API
API is exactly same as PHP-Mongo driver.

### Connect
Using native driver
```php
    $m = new MongoClient("mongodb://user:pas@localhost:27017/test"); 
```
Using MongoJacket
```php
    $jacket =  new MongoJacket\Jacket('localhost:27017', 'test', 'user', 'pass'); 
```

### Database
Using native driver
```php
    $db = $m->selectDB("rockband"); 
```
Using MongoJacket
```php
    $db =  $jacket->db('rockband'); 
```
### Collection
Using native driver
```php
    $collection = $db->selectCollection('bands'); 
```
Using MongoJacket
```php
    $collection =  $db->collection('bands'); 
```
### Command Chaining
You can call commands in chain
```php
    $jacket->db('rockband')->collection('bands')->find();
```
### Queries
Currently only following query methods of native PHP-Mongo drive are supported in MongoJacket.

``` find ```, ``` findOne ```, ``` insert ```, ``` save ```, ``` batchInsert ```, ``` findAndModify ```, ``` Update ```

MongoJackect query methods return the same value as native APIs or an exception object in case of any exception.
##### [Back to Index](#table-of-contents)


## Callbacks
As an additional parameter to any query method you can pass an anonymous function as callback. All the following syntax are correct. Callback method can return a value to the caller of the parent query.  
```php
// pass a callback method to find method 
// without any criteria or field specified
    $bands = $jacket->db('rockband')->collection('bands')->find(
                function($results,$error){
                    // data transformation logic
                    return $results; //return to the calling parameter
                }
            );

// pass a callback method to find method 
// with only criteria specified 
    $bands = $jacket->db('rockband')->collection('bands')->find(
                array("year" => array('gr' => 1963)) ,
                function($results,$error){
                    // data transformation logic
                    return $results; //return to the calling parameter
                }
            );

// pass a callback method to find method 
// with only criteria and field specified 
    $bands = $jacket->db('rockband')->collection('bands')->find(
                array("year" => array('gr' => 1963)) ,
                array("name", "album", "awards", "members") ,
                function($results,$error){
                    // data transformation logic
                    return $results; //return to the calling parameter
                }
            );
```
### Using $this and Infinite Command Sequencing
In the callback method you are allowed tho use ``` $this ```.
It is allowed to call another query method and pass a callback function inside a callback function. Theoretically it can be looped till infinite time. All inner queries and callbacks will be executed in sequence.
```php
// command/ callback sequencing
    $bands = $jacket->db('rockband')->collection('bands')->find(
                function($results,$error){
                    // check some condition
                        // add a new entry
                        $this->save( array(
                                            "name"=>"Velvet Revolver",
                                            "members"=> array("Scott Weiland",
                                                                "Slash",
                                                                "Dave Kushner",
                                                                "Matt Sorum",
                                                                "Duff McKagan"),
                                            "year"=>1984) ,
                                    function ($result, $error){
                                        // some code here 
                                    } );
                    return $results; //return to the calling parameter
                }
            );
```
##### [Back to Index](#table-of-contents)


## Exception Handling
MongoJacket exceptions are objects ```\MongoJacket\Exception```. Exceptions are not thrown in MongoJacket APIs. In case of an exception MongoJacket  will return an  ```\MongoJacket\Exception``` object instead of result when callback method is not available. You can validate the response like bellow.

```php
// no callback is added
    $isinseted = $jacket->db('rockband')->collection('bands')->save( array(
                                                    "name"=>"Velvet Revolver",
                                                    "members"=> array("Scott Weiland",
                                                                        "Slash",
                                                                        "Dave Kushner",
                                                                        "Matt Sorum",
                                                                        "Duff McKagan"),
                                                    "year"=>1984) );
    if(is_a($isinseted, '\MongoJacket\Exception' ){
        // some diagnostic action
    }
```
when a callback function is added. Exception object is passed as the second parameter to the function. If no exception is there then ``` null ``` is passed in that place.
```php
// a callback is added
    $bands = $jacket->db('rockband')->collection('bands')->find(
                function($results,$error){
                    if(is_null($error)
                        && is_a($error, '\MongoJacket\Exception' ){
                        // some diagnostic action
                    }
                }
            );
```
##### [Back to Index](#table-of-contents)


## Binding of Custom Methods
A custom method can be binded to a collection like bellow.
```php
    $jacket->db('rockband')->collection('bands')->bind(
                                                    "McKaganBands", 
                                                    function (){
                                                       return $this->find(array("member" => 'Duff McKagan'));
                                                    });
```
Calling custom method
```php
    $jacket->db('rockband')->collection('bands')->McKaganBands();
```
### Passing Parameters to Binded Methods
The function cannot accept multiple parameters in the definition. Any parameter passed during custom method call can be accessed using ``` func_get_args() ```.

##### [Back to Index](#table-of-contents)


## Middleware
MongoJacket support a mechanism for adding middleware. A simple middleware can be written like bellow.
```php
    class MyMiddleware extends \MongoJacket\Middleware{
        // your middileware logic should be here 
        public function call(){
            // here do some magic
            $this->next->call();
        }
    }
```
Middleware can be bind to following ``` Pre ``` or ``` Post ``` events:
``` init ```, ``` find ```, ``` save ```, ``` delete ```
At present only Collection implements middleware protocol, and the registry method is  a private method. MongoJacket will add ability to register third-party middlewars in future.
##### [Back to Index](#table-of-contents))


## Validator
MongoJacket ``` validator ``` is a Middleware. MongoJacket does not required any  schema definition for mapping. It allows to add validation rules to Document elements for a collection. The validator method get called during ``` Pre Save ``` event. If a validation fails then an the query fails and exception object is returned.

```php
// Validator-1 
    $jacket->db('rockband')->
    collection('bands')->
    validator('year',
                function ($var) { 
                    // no new band after 2010 is allowed
                    return ($var<2010);
                }
            );
// Validator-2
    $jacket->db('rockband')->
    collection('bands')->
    validator('name',
                function ($var) { 
                    //  Limp Bizkit is not allowed
                    return !($var=="Limp Bizkit");
                }
            );

// this will fail due to Validator 1
    $jacket->db('rockband')
    ->collection('bands')->insert(array(
            "name"=>"Modern Alarms",
            "members"=> array(  "Dominic Barber",
                                "David Fraser",
                                "Colm Feeley",
                                "Andy Gledhill"),
            "year"=>2012)
    );

// this will fail due to Validator 2
    $jacket->db('rockband')
    ->collection('bands')->insert(array(
            "name"=>"Limp Bizkit",
            "members"=> array(  "Fred Durst",
                                "Wes Borland",
                                "Sam Rivers",
                                "John Otto",
                                "DJ Lethal"),
            "year"=>1994)
    );
```
### Overriding Validor and Sequencing
By default if another validator is added to same entity, then the first validation rule will be over ridden by the lastly added rule.

```php
// Validator-1 
    $jacket->db('rockband')->
    collection('bands')->
    validator('year',
                function ($var) { 
                    // only bands formed before 2012 are allowed
                    return ($var<2012);
                }
            );
// Validator-2 overrides the rule
    $jacket->db('rockband')->
    collection('bands')->
    validator(year',
                function ($var) { 
                    // only bands formed after 1994 is allowed
                    return ($var>1994);
                }
            );

    // this will be success because the final rule: year > 1994
    $jacket->db('rockband')
    ->collection('bands')->insert(array(
            "name"=>"Modern Alarms",
            "members"=> array(  "Dominic Barber",
                                "David Fraser",
                                "Colm Feeley",
                                "Andy Gledhill"),
            "year"=>2012)
    );

    // this will fail because the final rule: year > 1994
    $jacket->db('rockband')
    ->collection('bands')->insert(array(
            "name"=>"Limp Bizkit",
            "members"=> array(  "Fred Durst",
                                "Wes Borland",
                                "Sam Rivers",
                                "John Otto",
                                "DJ Lethal"),
            "year"=>1994)
    );
```

In the above example if the second validator Boolean ``` false ``` is passed as second parameter to the ``` validator ``` method, then both the validation rules will be chained.

```php
// Validator-1 
    $jacket->db('rockband')->
    collection('bands')->
    validator('year',
                function ($var) { 
                    // only bands formed before 2012 are allowed
                    return ($var<2012);
                }
            );
// Validator-2 overrides the rule
    $jacket->db('rockband')->
    collection('bands')->
    validator(year',
                function ($var) { 
                    // only bands formed after 1994 is allowed
                    return ($var>1994);
                },
                false
            );

    // this will fail because the final rule: 1994 < year < 2012
    $jacket->db('rockband')
    ->collection('bands')->insert(array(
            "name"=>"Modern Alarms",
            "members"=> array(  "Dominic Barber",
                                "David Fraser",
                                "Colm Feeley",
                                "Andy Gledhill"),
            "year"=>2012)
    );

    // this will fail because the final rule: 1994 < year < 2012
    $jacket->db('rockband')
    ->collection('bands')->insert(array(
            "name"=>"Limp Bizkit",
            "members"=> array(  "Fred Durst",
                                "Wes Borland",
                                "Sam Rivers",
                                "John Otto",
                                "DJ Lethal"),
            "year"=>1994)
    );
```
##### [Back to Index](#table-of-contents)


## ToDos
##### [Back to Index](#table-of-contents)