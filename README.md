# RestServer

## Introduction

RestServer is a php library (or micro-framework) for building RESTful webservices and websites.

It allows you to map urls patterns to specific Controllers, and give you access to the http request property thru a RestRequest interface, and allow control of the response with the RestResponse object. It support call chaining, authentication and is very simple to use in your way, making it possible to combine it with your favorite frameworks. There is also a independent RestClient class that easy access to restful servers.

After donwloading the package you can navigate to docs folder for api documentation and to tests folder for tests and examples.

It is a stable package that is in use for almost 3 years (and counting), it is easy to extend and to adapt to your needs.

There are two published examples: 
[JobJoker](http://github.com/diogok/JobJoker) Is a Job/proccess control api/ui for php
[IdeasWall.org](http://github.com/diogok/ideaswallorg) Is an experiment on running php and restserver on google app engine (outdated, auth is different now, but rest is okey)

## Usage

### RestServer

Everything starts on the RestServer class, that must be instanciated. It works better with proper URLRewriting urls, but that is no mandatory. A example of .htaccess is :
[.htaccess](http://github.com/diogok/JobJoker/blob/master/.htaccess)

You start by instanciating the RestServer at your end point, you may pass in a parameter to use as the url or let it guess, this will already prepare the Request, Response and Authenticator objects.
[index.php line 29](http://github.com/diogok/JobJoker/blob/master/index.php#L28)
[index.php line 11](http://github.com/diogok/ideaswallorg/blob/master/war/index.php#L11)

You can also set global parameters objects that will be accessible on the controllers by using the "setParameter" and "getParameter" on the restserver. Next you must map your urls(using regex) to your controller (or to a specific method of a controller), also specify the http method used.
[index.php line 44~64](http://github.com/diogok/JobJoker/blob/master/index.php#L44-64)
[index.php line 34~42](http://github.com/diogok/ideaswallorg/blob/master/war/index.php#L34-42)

To let the RestServer work, you just them need to call "execute".
[index.php line 66](http://github.com/diogok/JobJoker/blob/master/index.php#L66)
[index.php line 58](http://github.com/diogok/ideaswallorg/blob/master/war/index.php#L58)

### RestAuthenticator

You can also use authentication on the global scope (or inside each controller) by using the Authenticator object (obtained using "getAuthenticator" on the RestServer).
[index.php line 31~37](http://github.com/diogok/JobJoker/blob/master/index.php#L31-37)

### Controllers and Views (Also RestRequest and RestResponse)

Next of you can build your controllers, they must implement the RestController interface, and their methods must receive a RestServer as parameter. They can return the RestServer of another controller/view to forward the request to. 

Use the methods "addResponse", "setResponse", "addHeader", "cleanHeaders" on the RestResponse object (from "getResponse" of RestServer) to prepare the response.

Use the methods from RestRequest:
- getGet, getPost, getPut retrieve the parameters from each request type
- getBody return the request body as a string
- getExtension acceptMime to know which type is requested
- getURI to get the url of parts of it (parameters like)
- isGet, isPost, isPut, isDelete 

[controllers](http://github.com/diogok/JobJoker/tree/master/controllers/)
[views](http://github.com/diogok/JobJoker/tree/master/views/)
[controllers](http://github.com/diogok/ideaswallorg/tree/master/war/controller/)
