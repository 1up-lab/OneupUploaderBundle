Custom Namer
============

The purpose of a namer service is to name an uploaded file before it is stored to the storage layer.

## UniqidNamer (default)

Returns a system wide unique filename using the `uniqid()` function.

## GaufretteNamer

Decorates a namer and calls it until the filename is unique on the provided Gaufrette file system.

Example: Decorating the `UniqidNamer`:

```xml
<services>
    <service id="acme_demo.gaufrette_namer" class="Oneup\UploaderBundle\Uploader\Naming\GaufretteNamer">
        <argument type="service" id="gaufrette.gallery_filesystem"/>
        <argument type="service" id="oneup_uploader.namer.uniqid"/>
    </service>
</services>
```

## Use a custom namer

First, create a custom namer which implements ```Oneup\UploaderBundle\Uploader\Naming\NamerInterface```.

```php
<?php

namespace Acme\DemoBundle;

use Oneup\UploaderBundle\Uploader\File\FileInterface;
use Oneup\UploaderBundle\Uploader\Naming\NamerInterface;

class CatNamer implements NamerInterface
{
    public function name(FileInterface $file)
    {
        return 'grumpycat.jpg';
    }
}
```

To match the `NamerInterface` you have to implement the function `name()` which expects an `FileInterface` and should return a string representing the name of the given file. The example above would name every file _grumpycat.jpg_ and is therefore not very useful.

Next, register your created namer as a service in your `services.xml`

```xml
<services>
    <service id="acme_demo.custom_namer" class="Acme\DemoBundle\CatNamer" />
</services>
```

Now you can use your custom service by adding it to your configuration:

```yml
oneup_uploader:
    mappings:
        gallery:
            namer: acme_demo.custom_namer
```

Every file uploaded through the `Controller` of this mapping will be named with your custom namer.
