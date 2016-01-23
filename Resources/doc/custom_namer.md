Custom Namer
============

The purpose of a namer service is to name an uploaded file before it is stored to the storage layer.

Currently the OneupUploaderBundle provides two namer methods.
- Default used is a namer called `UniqidNamer`, which will return a system wide unique filename using the `uniqid()` function.
- The other method called `UrlSafeNamer` using `random_bytes` function, see [Using UrlSafeNamer](#urlsafenamer) how to use it

## UrlSafeNamer

To enable UrlSafeNamer you will need to change your namer in your mappings to `oneup_uploader.namer.urlsafe`

Example

```yml
oneup_uploader:
    mappings:
        gallery:
            namer: oneup_uploader.namer.urlsafe
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

```yml
services:
    acme_demo.custom_namer:
        class: Acme\DemoBundle\CatNamer
```

Now you can use your custom service by adding it to your configuration:

```yml
oneup_uploader:
    mappings:
        gallery:
            namer: acme_demo.custom_namer
```

Every file uploaded through the `Controller` of this mapping will be named with your custom namer.
