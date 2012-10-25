# RestServer

This is the new version of the RestServer. 

Still a work in progress, reviewing and re-documenting.

Based on the [namespaces branch](http://github.com/diogok/restserver/tree/namespaces) of [Xavier](https://github.com/zeflasher/).

## Introduction

RestServer is a PHP library package for building RESTful webservices and websites.

It is a simple and easy way to create resources, and be able to handle every aspect of your rest api if needed.

This package features the following wrappers: Request, Response, Authenticator and some default Controllers and Views.

Please continue reading this documentation and also checkout the example for more information. The source is also well formatted, simple and documented for information.

Is is intended for PHP 5.3+, older version need old branch.

## Usage

### Server

The usage is very simple, first you'll need to include either the "phar" or the initial Rest/Server.php class.

    <?php 
    include 'restserver.phar'
    $api = new Rest\Server();
    // more logic to come here.
    $api->execute();
    ?>

You can instantiate the Rest\Server to start handling the requests, it will get the URI from the HTTP Request, but you can also provide a custom one for the Rest\Server($url) constructor.

After configuring the server you call the "execute" method so it can chain the request/response cicle.

### Mapping

Most of work is mapping URIs to functions or controllers.

You can map any method (even custom) and URI to a lambda/closure function or to a Controller class (that implements Rest\Controller) or a specific method of a controller. 

    <?php
        $api->addMap("GET","/some/resource",function($api){});
        $api->addMap("POST","/some/resource/:var",function($api){});
        $api->addMap("GET","/foo(bar)?","MyController",array("text/html"));
        $api->addMap("GET","/foo/:?bar","MyController::myMethod",array("text/html"));
    ?>

The URI parameter accepts any regex, and also name parameters (:name or :?name for optional parameter), and an array of accepted mime types.

### Controllers and Views

(Also apply to lambda/closure)

All controllers to be mapped must implement Rest\Controller, it implies in implementing a single default method as follow and a public constructor with no arguments(or optional arguments):

    <?php
        class MyController implements Rest\Controller {
            public function execute($rest) {
            }
        }
    ?>

Other method that might get mapped into the server must also receive this one parameter. 

This method parameter is an instance of the Rest\Server from where you can get the request details, data and response.

The methods can return the rest server, to end the cycle, or another Controller or View to follow the request to.

    <?php
        class MyController implements Rest\Controller {
            public function execute($rest) {
                return new MyViewOrOtherController($anyDataOrNot); //will foward the request
            }
        }
    ?>

The same apply to Views, but implements Rest\View.

### Default Controllers and Views

The Rest\Server comes with some default Controller and Views for most common use cases.

- Rest\Controller\BadRequest
- Rest\Controller\Created
- Rest\Controller\Forbidden
- Rest\Controller\InternalServerError
- Rest\Controller\MethodNotAllowed
- Rest\Controller\MovedPermanently($newLocation)
- Rest\Controller\NotAcceptable
- Rest\Controller\NotAuthorized
- Rest\Controller\NotFound
- Rest\Controller\NotModified
- Rest\Controller\Redirect($newLocation)
- Rest\View\Generic([$fileToRender,$data])
- Rest\View\JSon($object)

### Authenticator

The server packages an authenticator handler for both HTTP Basic and HTTP Digest authentication, acessible in the server getAuthenticator method.

This object can be used insider each controller/view/closure or globally. A global authenticator that denies the access will stop the request and respond with a proper NotAuthorized response.

Here are the functions:

    <?php
        $auth = $server->getAuthenticator();
        $user = $auth->getUser();
        $pass = $auth->getPassword(); //only in BASIC
        $auth->validate($user,$pass); // only in DIGEST
        $auth->forceDigest(true); 
        $auth->getRealm() ; $auth->setRealm($name);
        $auth->requireAuthentication(true); // Set if authentication if required for this request;
        $auth->isAuthenticated();
    ?>

### Request

The request object, accessible in the server getRequest method, holds all data from the user request.

### Response

The response object, accessible in the server getRequest method, holds all data from the user request.

### E-Tags and Cache

### Mime-Type handling

### Bulding and using a phar

## Full Example

## More on REST

## License

Distributed under the Eclipse Public License.

