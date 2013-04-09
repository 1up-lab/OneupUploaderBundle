Process uploaded files using custom logic
=========================================

In almost every use case you need to further process uploaded files. For example if you want to add them to a Doctrine Entity or the like. To cover this, the OneupUploaderBundle provides some useful Events you can listen to.

* `PostUploadEvent`: Will be dispatched after a file has been uploaded and moved.
* `PostPersistEvent`: The same as `PostUploadEvent` but will only be dispatched if no `Orphanage` is used.

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
    <service id="acme_hello.upload_listener" class="Acme\HelloBundle\EventListener">
        <argument type="service" id="doctrine" />
        <tag name="kernel.event_listener" event="oneup.uploader.post.persist" method="onUpload" />
    </service>
</services>
```

You can now implement you custom logic in the `onUpload` method of your EventListener.

## Use custom input data
FineUploader supports passing custom data through the request as the following examples states. For example you can pass the id of an entity you wish to paste the images to.

```html
<script type="text/javascript">
var uploader = new qq.FineUploader({
    element: document.getElementById('fine-uploader'),
    text: {
        uploadButton: "{{ 'edit.selectfile' | trans({}, 'myphotos') }}"
    },
    request: {
        endpoint: "{{ path('_uploader_gallery') }}",
        params: {
            gallery: "{{ gallery.id }}"
        }
    }
});
</script>
```

As you can see, we extended the `request` part of the Fine Uploader by adding a `params` section. These variables are accessible through the request object in the EventHander.

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
* `getType`: Get the name of the mapping of the current upload. Useful if you have multiple mappings and EventListeners.
* `getConfig`: Get the config of the mapping.