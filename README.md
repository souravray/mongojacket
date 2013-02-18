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
  * Callbacks
  * Exception Handling
* [Binding of Custom Methods](#binding-of-custom-methods)
* [Middleware](#middleware)
* [Validator](#validator)
* [ToDos](#todos)


## Quick Start

MongoJacke is not one of those bloated ODMs you encounter everyday. It esentially gives developers an interface similar to native Mongo driver. In addition to that, MongoJacke allows developer to add custom methods, callbacks and validation rules.

### Installation
MongoJacket is not ready for installation with Composer. For now you need to follow these manual steps to add MongoJacket to your project.

```php
// inclue MongoJacket file
    include_once("MongoJacket/index.php");
// register autoloader
    spl_autoload_register('MongoJacket\MongoJacketAutoloader');
```


## API
API is exacatly same as PHP-Mongo driver.

### Connect
Using native driver
```php
    $m = new MongoClient("mongodb://user:pas@localhost:27017/test"); 
```
Using Mongjacket
```php
    $jacket =  new MongoJacket\Jacket('localhost:27017', 'test', 'user', 'pass'); 
```

### Database
Using native driver
```php
    $db = $m->selectDB("rockband"); 
```
Using Mongjacket
```php
    $db =  $jacket->db('rockband'); 
```
### Collection
Using native driver
```php
    $collection = $db->selectCollection('bands'); 
```
Using Mongjacket
```php
    $collection =  $db->collection('bands'); 
```
### Command chaining
You can call commands in chain
```php
    $jacket->db('rockband')->collection('bands')->find();
```
### Queries
Currently only following query methods of native PHP-Mongo drive are suuported in MongoJacket.

``` find, findOne, insert, save, batchInsert, findAndModify, Update ```


##### [Back to Index](#table-of-contents)


## Binding of custom methods
##### [Back to Index](#table-of-contents)


## Middleware
##### [Back to Index](#table-of-contents))


## Validator
##### [Back to Index](#table-of-contents)


## ToDos
##### [Back to Index](#table-of-contents)