# RestServer

RestServer is a PHP library/framework for building RESTful webservices and also websites.

It allows you to route *Method*,*URI* and *MIME-TYPE* to specific *Resource*s handlers, access the *Request*ed data and provide a proper *Response*.

Here it's decribe its documentation, together with the example folder and tests for reference.

This version is based on the [namespaces branch](http://github.com/diogok/restserver/tree/namespaces) of [Xavier](https://github.com/zeflasher/).

## Usage
    
The simplest way is using *composer*, just declare the restserver dependency in you composer.json:
    {
        "require":{
            "diogok/restserver": "*"
        }
    }

If not using composer, you can use the *restserver.phar*: just download and require it in your application.

### The Server

The first step to defining you REST service is to instantiate the server, as follows:

    $rest = new Rest\Server

This way the server will grab the requested URI to work with, or you can specify a URI if you are not working with UrlRewrite.

For example, if the resource to load is coming from $\_GET["q"]:

    $rest = new Rest\Serve($_GET['q']);

This way restserver will treat $\_GET['q'] as the requested URI.

### Creating resources

This is the main funcionality of this package, is to define the available resources.

    $rest->addMap("GET","/url","action");

Dissecating this, you get a METHOD, a URI and an ACTION. Any method is available, even custom ones. URIs can be any URI and it support variable parameters, as follow:

    $rest->addMap("POST","/user/:id","action");
    $rest->addMap("GET","/user/:id/posts","action");

Will give you a map to the "id" parameter (more on this later).

On action, it can be a Rest\Controller, a Rest\View, a method within any of these or a anonymous function, as such:
    
    $rest->addMap("GET","/uri","UserController");
    $rest->addMap("GET","/uri","UserController::update");
    $rest->addMap("GET","/uri",function($rest) {
            return $rest;
        });

You can also map specific mime types to be accessible, according to the requested "accepts":
    
    $rest->addMap("GET","/uri","action",array("application/json"));
    $rest->addMap("GET","/uri","action2",array("application/xml"));

After setting the resources, you must execute the server:

    echo $rest->execute();

This will trigger the server to work. 

More on resources handling on future topics.

### Global utilities
    
The server have a few utilities to deal with the global server scope.

You can set variables to be acessible at any rest resource as follow:
    
    $rest->setParameter("key",$value);
    $key = $rest->getParamter("key");

You can also get all setted parameters with:

    $params = $rest->getParameters();

You can set global accepted mime-types:

    $rest->setAccept(array("application/json","text/html"));

You can dinamically set/get the requested URI:

    $rest->setQuery("/uri");
    $rest->getQuery();
    $rest->getBaseUrl();

### Authenticator

RestServer support both BASIC and DIGEST http authentication mechanisms. You can access the Authenticator object as follow:

    $auth = $rest->getAuthenticator();

It will get the user/password provided on the request. For BASIC auth, you can get it simply:

    $user = $auth->getUser();
    $pass = $auth->getPassword();

Or for Digest:

        $user = $auth->getUser();
        // your logic to lookup user pass
        $auth->validate($user,$pass);

You can them test or set the authentication status:
        
        $auth->setAuthenticated(true);
        $ok = $auth->isAuthenticated();

If by "execute" time authentication fail, server will respond properly.

### Request

To deal with the request, withing a rest action, you can access it as follows:

    $req = $rest->getRequest();

The request contains the many thing the user may have sent to and requested from the server. Here are the options:

    $req->isGet();
    $req->isPost();
    $req->isPut();
    $req->isDelete();
    $req->getMethod();
    $req->getGet(); // whole $_GET
    $req->getGET($key);
    $req->getPOST();
    $req->getPOST($key);
    $req->getFiles(); // from multipart/form-data POSTs
    $req->getFiles($key); // from multipart/form-data POSTs
    $req->getInput(); // from PUTs
    $req->getHeader($header);
    $req->getETag(); // The 'if-match', good for your caching
    $req->getParameter($ket); // from the resource configured URI
    $req->getURI(); // the requested URI
    $req->getURI($i); // the "part" of the URI (count each "/")
    $req->getExtension(); // the extension of the URI
    $ok = $req->acceptMime("mime/type"); // If mime is acceptable
    $req->getSession($k); 
    $req->setSession($k,$v);
    $req->getCookie($k); 
    $req->setCookie($k,$v);

And that's what you get.

### Response

With the response object, you can set what to return for the user.

    $res = $rest->getResponse();

And here are it's methods:

    $res->addHeader("Content-Type: application/json");
    $res->cleanHeader(); // undo headers
    $res->setResponse($content); // body of the response
    $res->sendResponse($content); // sends partial response already, better for big ones
    $res->cleanResponse($content); // clean setted response texts
    $res->appendResponse($content);
    $res->getResponse(); 

The response will be sent to client at the end of the restserver executing cycle.

### Controllers and Views

Controllers and views are the default option for action on a request. A controller or view to be used on RestServer must implement the Rest\Controller or Rest\View interface, which require a single public method:
    
    class MyController implements \Rest\Controller {
        public function execute(\Rest\Server $rest) {
            // your logic here
            return $rest;
        }
    }

The execute method (or any other method to be used in restserver) will receive the RestServer object and must return the same restserver, to end the request, or another action(controller or view) to forward the request control to.

    class MyController implements \Rest\Controller {
        public function execute(\Rest\Server $rest) {
            $rest->getResponse()->setResponse("Hello, world!");
            return $rest;
        }

        public function other(\Rest\Server $rest) {
            return new MyView();
        }
    }

#### Defaults

The package comes with a few controller and a view implementation for generic usage, as follow:
    
    new \Rest\Controller\BadRequest;
    new \Rest\Controller\Created;
    new \Rest\Controller\Fobidden;
    new \Rest\Controller\InternalServerError;
    new \Rest\Controller\MethodNotAllowed;
    new \Rest\Controller\MovedPermanently($newLocation);
    new \Rest\Controller\NotAcceptable;
    new \Rest\Controller\NotAuthorized;
    new \Rest\Controller\NotFound;
    new \Rest\Controller\NotModified;
    new \Rest\Controller\Redirect($location);

    new \Rest\View\Generic($template_path,$data);
    new \Rest\View\JSon($data);


Lot's of these controllers are used automatically, like NotFound, MethodNotAllowed and a few others.

#### Anonymous functions 

The server can also use anonymous functions to deal with the resources, work the same way as the execute method of a generic controller (that's is what happens).

    $rest->addMap("GET","/users",function($rest) {
            // your logic
            return $rest; // or return new \Rest\View\JSON($data) for example
            });

### E-Tags and Cache

A simple new feature is to deal with e-tags for caching, a common case would be:

    $rest->addMap("GET","/user/:uid",function($rest) {
        $id = $rest->getREquest()->getParameter("uid");
        $etag = $rest->getREquest()->getEtag();
        // check if etag is still same data, could be a md5 of user data or something
        if($same) {
            return new \Rest\Controller\NotModified;
        } else {
            $rest->getREsponse()->setHeader("E-Tag",$newEtag);
            return new \Rest\View\JSon($user);
        }
    });

### Mime-Type handling

RestServer can route and respond properly for requests of different mime-types. 

You can define mimes to accept both globally or by resources, as follows:

    $rest->setAccepts(array("*","text/html"));
    $rest->addMap("GET","/user/:uid","UserContrller",array("application/json"));
    $rest->addMap("GET","/user/:uid/profile","UserContrller",array("text/html"));

In such situation, the first resource would respond only to application/json requests (accept-mime header), the second only to text/html and the rest to anything or text/html.

## Full Example

A full example can be found on source-code folder "example", and is very complete on resource usage and heavily commented and tested.

The tests and the example tests also can be used as a resource for information too.

## More on REST

Soon.

