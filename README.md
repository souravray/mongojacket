MongoJacket
=====================
A simple abstraction layer for [PHP MongoDB Driver](http://www.php.net/manual/en/book.mongo.php)
## Build Matrix
- Current Build: 0.0.1_nb
- Stability: Unstable
- Compatibility:
  - MongoDB >= 1.5.x
  - PHP >= 5.4.x


<a id="index-0"></a>
## Table of contents
* [Quick Start](#quickstart)
* [API](#quickstart-1)
  * Callbacks
  * Exception Handling
* [Binding of custom methods](#quickstart-2)
* [Middleware](#quickstart-3)
* [Validator](#quickstart-4)
* [ToDos](#quickstart-5)

<a id="quickstart"></a>
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

<a id="quickstart-1"></a>
## API
##### [Back to Index](#index-0)

<a id="quickstart-2"></a>
## Binding of custom methods
##### [Back to Index](#index-0)

<a id="quickstart-3"></a>
## Middleware
##### [Back to Index](#index-0)

<a id="quickstart-4"></a>
## Validator
##### [Back to Index](#index-0)

<a id="quickstart-5"></a>
## ToDos
##### [Back to Index](#index-0)