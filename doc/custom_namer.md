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

namespace AppBundle\Uploader\Naming;

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

## Change the directory structure

With the `NameInterface` you can change the directory structure to provide a better files organization or to use your own existing structure. For example, you need to separate the uploaded files by users with a `user_id` folder.

You need to inject the `security.token_storage` service to your namer.

```xml
<services>
    <service id="acme_demo.custom_namer" class="Acme\DemoBundle\CatNamer">
        <argument type="service" id="security.token_storage"/>
    </service>
</services>
```

```yml
services:
    acme_demo.custom_namer:
        class: Acme\DemoBundle\CatNamer
        arguments: ["@security.token_storage"]
```

Now you can use the service to get the logged user id and return the custom directory like below:

```php
<?php

namespace Acme\DemoBundle;

use Oneup\UploaderBundle\Uploader\File\FileInterface;
use Oneup\UploaderBundle\Uploader\Naming\NamerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class CatNamer implements NamerInterface
{
    private $tokenStorage;
    
    public function __construct(TokenStorage $tokenStorage){
        $this->tokenStorage = $tokenStorage;
    }
    
    /**
     * Creates a user directory name for the file being uploaded.
     *
     * @param FileInterface $file
     * @return string The directory name.
     */
    public function name(FileInterface $file)
    {
        $userId = $this->tokenStorage->getToken()->getUser()->getId();
        
        return sprintf('%s/%s.%s',
            $userId,
            uniqid(),
            $file->getExtension()
        );
    }
}
```

Every file uploaded through the `Controller` of this mapping will be named with your new directory structure.
