#Request convertion and validation bundle#

[TOC]

## About ##
This bundle allow to easy map incoming request to defined DTO and validate it before controller starts works

## Documentation ##
### installation ###

to install converter, use composer

```
$ composer require request-converter-bundle
```

### how to use ###

at first, create your own request DTO model with validation rules


#### request model ####
```php
<?php

namespace App\RequestModel;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class MyRequestModel
 * @package App\RequestModel
 */
class MyRequestModel
{
    /**
     * @Assert\Type(type="integer")
     * @Assert\NotNull()
     * @Assert\GreaterThan(value="0")
     */
    public $firstNumber;
   

    // yes, you can use default values
    /**
     * @Assert\Type(type="integer")
     * @Assert\NotNull()
     * @Assert\GreaterThanOrEqual(value="-1")
     */
    public $secondNumber = 0;

}

```

then declare model using via @RequestDTO annotation in your controller


#### controller ####
```php
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use GepurIt\RequestConverterBundle\Annotations\RequestDTO;
use Symfony\Component\HttpFoundation\Response;
use App\RequestModel\MyRequestModel;

/**
 * Class MyController
 * @package App\Controller
 */
class MyController extends AbstractController
{
     /**
     * @Route("/my_path", name="my_route")
     * @return Response
     * @RequestDTO("App\RequestModel\MyRequestModel")
     */
    public function myAction(MyRequestModel $requestModel)
    {
        // here you can work with object $requestModel.
        doSomething($requestModel->firstNumber);
        ...
    }  
}

```

### change function argument name ###

by default, requestModel argument name called 'requestModel', you can change it, defined 'name' parameter in annotatnion

```php
...

     /**
     * @Route("/my_path", name="my_route")
     * @return Response
     * @RequestDTO(model="App\RequestModel\MyRequestModel", name="myName")
     */
    public function myAction(MyRequestModel $myName)
    {
        // here you can work with object $myName.
        doSomething($myName->firstNumber);
        ...
    }  

...

```

### disable validation ###
to disable request validation, set "validate" annotation parameter to false

```php
...

     /**
     * @Route("/my_path", name="my_route")
     * @return Response
     * @RequestDTO(model="App\RequestModel\MyRequestModel", validate=false)
     */
    public function myAction(MyRequestModel $myName)
    {
        // here you can work with object $myName.
        doSomething($myName->firstNumber);
        ...
    }  

...

```

### annotaion parameters ###

  * **model** -     contains class name of request`s model class, *required*
  * **name**  -     contains argument`s name for controller method, by default *"requestModel"*
  * **validate** -  shouls request be validated before action. by default *false*.


### post-construction handling ###

you can calculate some additional params to validate after request constructed.

example: request has two integer parameters, but you need to validate custom rule - sum of that parameters should not be greated than 10.

#### interface implementation ####

at first, implement "GepurIt\RequestConverterBundle\Contract\RequestModelServiceInterface interface" on your request model,

```php
<?php

namespace App\RequestModel;

use Symfony\Component\Validator\Constraints as Assert;
use GepurIt\RequestConverterBundle\Contract\RequestModelServiceInterface;

/**
 * Class MyRequestModel
 * @package App\RequestModel
 */
class MyRequestModel implements RequestModelServiceInterface
{
    /**
     * @Assert\Type(type="integer")
     * @Assert\NotNull()
     * @Assert\GreaterThan(value="0")
     */
    public $firstNumber;
   

    // yes, you can use default values
    /**
     * @Assert\Type(type="integer")
     * @Assert\NotNull()
     * @Assert\GreaterThanOrEqual(value="-1")
     */
    public $secondNumber = 0;

    /**
     * {@inheritDoc}
     **/
    public function handle()
    {
        //calculate here
    }

}

```

#### custom fields handling ####

as you see, RequestModelServiceInterface provides "handle" method.
this method will be called immediately after request model construction.
so, you can add your custom field with validation rules, and calculate they in "handle()" method

```php
<?php

...

class MyRequestModel implements RequestModelServiceInterface
{
    ...


    /**
     * @Assert\LessThanOrEqual(value="10")
     */
    public $sum = 0;


    /**
     * {@inheritDoc}
     **/
    public function handle()
    {
        $this->sum = $this->firstNumber + $this->secondNumber;
    }
}

```

### Dependency injection ###

for use dependency injection in your request, **think twice**  
if you steel need dependency injection, **think more times**, maybe, you need just a [custom validation](https://symfony.com/doc/current/validation/custom_constraint.html)?

But if you shure, you really need dependency in your request model, so you can define it as container service

⚠️ **Warning**: Do not use any domain logic or buisness here, is need only for validation

* Request model Services MUST implemets RequestModelServiceInterface
* to register this service in converter, tag it with 'request_model_service' tag


#### implement interface and dependencies ####
```php
<?php

...

class MyRequestModel implements RequestModelServiceInterface
{
    /** @var MyCalculator $calculator */
    private $calculator;


    ...

    
    public function __construct(MyCalculator $calculator) 
    {
        $this->calculator = $calculator;
    }

    /**
     * @Assert\LessThanOrEqual(value="10")
     */
    public $sum = 0;


    /**
     * {@inheritDoc}
     **/
    public function handle()
    {
        $this->sum = $this->calculator->sum($this->firstNumber, $this->secondNumber);
    }
}

```

#### register this service in converter ####

```yaml
services:
  App\RequestModel\MyRequestModel:
    autowire: true
    tags: ["request_model_service"]

```