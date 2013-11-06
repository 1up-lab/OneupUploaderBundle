Error Handlers
==============

Since version **0.9.6** of this bundle, the error management is using special error handler services. Its main purpose is to recieve an `UploadException` and the `Response` object and handle the error according to the frontend specification. You can define an own error handler for each entry in your configuration. The default handler is the so called `NoopErrorHandler` which does nothing, when recieving an exception.

To create your own error handler, implement the `ErrorHandlerInterface` and add your custom logic.

```php
<?php

namespace Acme\DemoBundle\ErrorHandler;

use Exception;
use Oneup\UploaderBundle\Uploader\ErrorHandler\ErrorHandlerInterface;
use Oneup\UploaderBundle\Uploader\Response\AbstractResponse;

class CustomErrorHandler implements ErrorHandlerInterface
{
    public function addException(AbstractResponse $response, Exception $exception)
    {
        $message = $exception->getMessage();
        $response['error'] = $message;
    }
}

```

Define a service for your class.

```xml
<services>
    <service id="acme_demo.custom_error_handler" class="Acme\DemoBundle\ErrorHandler\CustomErrorHandler" />
</services>
```

And configure the mapping to use your shiny new service.

```yml
oneup_uploader:
    mappings:
        gallery:
            error_handler: acme_demo.custom_error_handler
```

**Note**: As of [9dbd905](https://github.com/1up-lab/OneupUploaderBundle/commit/9dbd9056dfe403ce6f1273d2d75fe814d517731a) only the `BlueimpErrorHandler` is implemented. If you know how to implement the error handlers for the other supported frontends, please create a pull request or drop me a note.
