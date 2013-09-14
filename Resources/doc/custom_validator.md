Writing custom Validators
=========================

File validations in the OneupUploaderBundle were made using the [EventDispatcher](http://symfony.com/doc/current/components/event_dispatcher/introduction.html) component.
If you want to enhance file validations in your application, you can register your own `EventListener` like in the example below.
To fail a validation, throw a `ValidationException`. This will be catched up by the `Controller`.

```php
namespace Acme\DemoBundle\EventListener;

use Oneup\UploaderBundle\Event\ValidationEvent;
use Oneup\UploaderBundle\Uploader\Exception\ValidationException;

class AlwaysFalseValidationListener
{
    public function onValidate(ValidationEvent $event)
    {
        $config  = $event->getConfig();
        $file    = $event->getFile();
        $type    = $event->getType();
        $request = $event->getRequest();

        // do some validations
        throw new ValidationException('Sorry! Always false.');
    }
}
```

After that register your new `EventListener` in the `services.xml` of your application.

```xml
<?xml version="1.0" encoding="UTF-8" ?>

<container xmlns="http://symfony.com/schema/dic/services"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">

    <services>
        <service
            id="acme_demo.always_false_listener"
            class="Acme\DemoBundle\EventListener\AlwaysFalseValidationListener"
        >
            <tag name="kernel.event_listener" event="oneup_uploader.validation" method="onValidate" />
        </service>
    </services>
</container>
```
