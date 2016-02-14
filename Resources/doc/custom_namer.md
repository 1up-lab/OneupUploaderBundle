Custom Namer
============

The purpose of a namer service is to name an uploaded file before it is stored to the storage layer. Currently the OneupUploaderBundle only provides a single namer service called `UniqidNamer`, which will return a system wide unique filename using the `uniqid()` function.

## Use a custom namer

First, create a custom namer which implements ```Oneup\UploaderBundle\Uploader\Naming\NamerInterface```.

```php
<?php

namespace AppBundle\Uploader\Naming;

use Symfony\Component\HttpFoundation\Request;

use Oneup\UploaderBundle\Uploader\File\FileInterface;
use Oneup\UploaderBundle\Uploader\Naming\NamerInterface;

class CatNamer implements NamerInterface
{
    public function name(FileInterface $file, Request $request)
    {
        return 'grumpycat.jpg';
    }
}
```

To match the `NamerInterface` you have to implement the function `name()` which expects an `FileInterface` and should return a string representing the name of the given file. The example above would name every file _grumpycat.jpg_ and is therefore not very useful. The namer should return an unique name to avoid issues if the file already exists.

Next, register your created namer as a service in your `services.xml`

```xml
<services>
    <service id="acme_demo.custom_namer" class="Acme\DemoBundle\CatNamer" />
</services>
```

```yml
services:
    app.cat_namer:
        class: AppBundle\Uploader\Naming\CatNamer
```

Now you can use your custom service by adding it to your configuration:

```yml
oneup_uploader:
    mappings:
        gallery:
            namer: app.cat_namer
```

Every file uploaded through the `Controller` of this mapping will be named with your custom namer.

## Add custom data in your file name

As you can see, the namer can access to the Request Object, so you can easily send any custom data and use it to name your files.

To send custom data, follow instructions as described in the [Custom Logic Documentation](custom_logic.md).

You can access to your custom data in the same way :

```php
class CustomNamer implements NamerInterface
{
    public function name(FileInterface $file, Request $request)
    {
        return $request->get('my_custom_data') . $file->getExtension();
    }
}
```
