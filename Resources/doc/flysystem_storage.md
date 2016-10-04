Use Flysystem as Storage layer
==============================

Flysystem is an abstract storage layer you can use to store your uploaded files. An explanation why you should use an abstraction storage layer comes from the _Why use Gaufrette_ section on [the Gaufrette Repo](https://github.com/KnpLabs/Gaufrette):

> The filesystem abstraction layer permits you to develop your application without the need to know were all those medias will be stored and how.
>
> Another advantage of this is the possibility to update the files location without any impact on the code apart from the definition of your filesystem. In example, if your project grows up very fast and if your server reaches its limits, you can easily move your medias in an Amazon S3 server or any other solution.

In order to use Flysystem with OneupUploaderBundle, be sure to follow these steps:

## Install OneupFlysystemBundle

Add the OneupFlysystemBundle to your composer.json file.

```js
{
    "require": {
        "oneup/flysystem-bundle": "1.4.*"
    }
}
```

And update your dependencies through composer.

    $> php composer.phar update oneup/flysystem-bundle

After installing, enable the bundle in your AppKernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Oneup\FlysystemBundle\OneupFlysystemBundle(),
    );
}
```

## Configure your Filesystems

The following is a sample configuration for the OneupFlysystemBundle. It will create a flysystem service called `oneup_flysystem.gallery_filesystem` which can be used in the OneupUploaderBundle. For a complete list of features refer to the [official documentation](https://github.com/1up-lab/OneupFlysystemBundle).

```yml
# app/config/config.yml

oneup_flysystem:
    adapters:
        acme.flysystem_adapter:
            awss3v3:
                client: acme.s3_client
                bucket: ~
                prefix: ~
    filesystems:
        gallery:
            adapter: acme.flysystem_adapter
```

## Configure your mappings

Activate Flysystem by switching the `type` property to `flysystem` and pass the Flysystem filesystem configured in the previous step.

```yml
# app/config/config.yml

oneup_uploader:
    mappings:
        gallery:
            storage:
                type: flysystem
                filesystem: oneup_flysystem.gallery_filesystem
```
