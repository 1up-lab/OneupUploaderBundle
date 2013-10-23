Use Gaufrette as Storage layer
==============================

Gaufrette is an abstract storage layer you can use to store your uploaded files. From the _Why use Gaufrette_ section on [their Repository](https://github.com/KnpLabs/Gaufrette):

> The filesystem abstraction layer permits you to develop your application without the need to know were all those medias will be stored and how.
>
> Another advantage of this is the possibility to update the files location without any impact on the code apart from the definition of your filesystem. In example, if your project grows up very fast and if your server reaches its limits, you can easily move your medias in an Amazon S3 server or any other solution.

In order to use Gaufrette with OneupUploaderBundle, be sure to follow these steps:

## Install KnpGaufretteBundle

Add the KnpGaufretteBundle to your composer.json file.

```js
{
    "require": {
        "knplabs/knp-gaufrette-bundle": "0.1.*"
    }
}
```

And update your dependencies through composer.

    $> php composer.phar update knplabs/knp-gaufrette-bundle

After installing, enable the bundle in your AppKernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Knp\Bundle\GaufretteBundle\KnpGaufretteBundle(),
    );
}
```

## Configure your Filesystems

The following is a sample configuration for the KnpGaufretteBundle. It will create a filystem service called `gaufrette.gallery_filesystem` which can be used in the OneupUploaderBundle. For a complete list of features refer to the [official documentation](https://github.com/KnpLabs/KnpGaufretteBundle).

```yml
# app/config/config.yml

knp_gaufrette:
    adapters:
        gallery:
            local:
                directory: %kernel.root_dir%/../web/uploads
                create: true

    filesystems:
        gallery:
            adapter: gallery
```

## Configure your mappings

Activate Gaufrette by switching the `type` property to `gaufrette` and pass the Gaufrette filesystem configured in the previous step.

```yml
# app/config/config.yml

oneup_uploader:
    mappings:
        gallery:
            storage:
                type: gaufrette
                filesystem: gaufrette.gallery_filesystem
```

You can specify the buffer size used for syncing files from your filesystem to the gaufrette storage by changing the property `sync_buffer_size`.

```yml
# app/config/config.yml

oneup_uploader:
    mappings:
        gallery:
            storage:
                type: gaufrette
                filesystem: gaufrette.gallery_filesystem
                sync_buffer_size: 1M
```

You may also specify the stream wrapper protocol for your filesystem:
```yml
# app/config/config.yml

oneup_uploader:
    mappings:
        gallery:
            storage:
                type: gaufrette
                filesystem: gaufrette.gallery_filesystem
                stream_wrapper: gaufrette://gallery/
```

> This is only useful if you are using a stream-capable adapter. At the time of this writing, only
the local adapter is capable of streaming directly.

The first part (`gaufrette`) in the example above `MUST` be the same as `knp_gaufrette.stream_wrapper.protocol`,
the second part (`gallery`) in the example, `MUST` be the key of the filesytem (`knp_gaufette.filesystems.key`).
It also must end with a slash (`/`).

This is particularly useful if you want to get exact informations about your files. Gaufrette offers you every functionality
to do this without relying on the stream wrapper, however it will have to download the file and load it into memory
to operate on it. If `stream_wrapper` is specified, the bundle will try to open the file as streams when such operation
is requested. (e.g. getting the size of the file, the mime-type based on content)
