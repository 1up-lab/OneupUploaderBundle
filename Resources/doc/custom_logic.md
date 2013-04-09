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