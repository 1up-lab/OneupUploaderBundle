Return custom data to the Frontend
==================================

There are some use cases where you need custom data to be returned to the frontend. For example the id of a generated Doctrine Entity or the like. To cover this, you can use `UploaderResponse` passed through all `Events` (except the `ValidationEvent`).

```php
namespace Acme\HelloBundle\EventListener;

use Oneup\UploaderBundle\Event\PostPersistEvent;

class UploadListener
{
    public function onUpload(PostPersistEvent $event)
    {
        $response = $event->getResponse();
    }
}
```

The `UploaderResponse` class implements the `ArrayAccess` interface, so you can just add data using it like an array:

```php
$response['key'] = 'value';
$response['foo'] = 'bar';
```

If you like to indicate an error, be sure to set the `success` property to `false` and provide an error message:

```php
$response->setSuccess(false);
$response->setError($msg);
```

> Do not use the keys `success` and `error` if you provide custom data, they will be overwritten by the internal properties of `UploaderResponse`.

Due to limitations of the `\ArrayAccess` regarding multi-dimensional array access, there is a method `addToOffset` which can be used to attach values to specific pathes in the array.
