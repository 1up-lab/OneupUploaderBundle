Process uploaded files using custom logic
=========================================

In almost every use case you need to further process uploaded files. For example if you want to add them to a Doctrine Entity or the like. To cover this, the OneupUploaderBundle provides some useful Events you can listen to.

* `PostUploadEvent`: Will be dispatched after a file has been uploaded and moved.
* `PostPersistEvent`: The same as `PostUploadEvent` but will only be dispatched if no `Orphanage` is used.

> You'll find more information on this topic in the [Event documentation](events.md)

To listen to one of these events you need to create an `EventListener`.

```php
namespace Acme\HelloBundle\EventListener;

use Oneup\UploaderBundle\Event\PostPersistEvent;

class UploadListener
{
    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
    }
    
    public function onUpload(PostPersistEvent $event)
    {
        //...
    }
}
```

And register it in your `services.xml`.

```xml
<services>
    <service id="acme_hello.upload_listener" class="Acme\HelloBundle\EventListener\UploadListener">
        <argument type="service" id="doctrine" />
        <tag name="kernel.event_listener" event="oneup_uploader.post_persist" method="onUpload" />
    </service>
</services>
```

You can now implement you custom logic in the `onUpload` method of your EventListener.

## Use custom input data
Many of the supported frontends support passing custom data through the request. Here's an example for [FineUploader](frontend_fineuploader.md) sending an id of an Entity along the normal request.

```html
<script type="text/javascript">
var uploader = new qq.FineUploader(
{
    element: document.getElementById('fine-uploader'),
    request: {
        endpoint: "{{ oneup_uploader_endpoint('gallery') }}",
        params: {
            gallery: "{{ gallery.id }}"
        }
    }
});
</script>
```

As you can see, we extended the `request` part of the FineUploader by adding a `params` section. These variables are accessible through the request object in the EventHander.

```php
public function onUpload(PostPersistEvent $event)
{
    $request = $event->getRequest();
    $gallery = $request->get('gallery');
    
    // ...
}
```

## Other available variables
The Event object provides the following methods.

* `getFile`: Get the uploaded file. Is either an instance of `Gaufrette\File` or `Symfony\Component\HttpFoundation\File\File`.
* `getRequest`: Get the current request including custom variables.
* `getResponse`: Get the response object to add custom return data.
* `getType`: Get the name of the mapping of the current upload. Useful if you have multiple mappings and EventListeners.
* `getConfig`: Get the config of the mapping.

## Using chunked uploads
If you are using chunked uploads and hook into the `oneup_uploader.post_chunk_upload` event, you will get `PostChunkUploadEvent` in your listeners. This Event type differs from the previously introduced ones. You'll have the following methods.

* `getChunk`: Get the chunk file. Is an instance of `Symfony\Component\HttpFoundation\File\File`.
* `getRequest`: Get the current request including custom variables.
* `getResponse`: Get the response object to add custom return data.
* `getType`: Get the name of the mapping of the current upload. Useful if you have multiple mappings and EventListeners.
* `getConfig`: Get the config of the mapping.
* `isLast`: Returns `true` if this is the last chunk to be uploaded, `false` otherwise.
