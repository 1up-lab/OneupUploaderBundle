Getting started
===============

The OneupUploaderBundle is a Symfony2 bundle developed and tested for versions 2.1+. This bundle does only provide a solid backend for the supported types of Javascript libraries. It does however not provide the assets itself. So in order to use any uploader, you first have to download and integrate it by yourself.

## Prerequisites

This bundle is tested using Symfony2 versions 2.1+.

### Translations
If you wish to use the default texts provided with this bundle, you have to make sure that you have translator
enabled in your configuration file.

```yaml
# app/config/config.yml

framework:
    translator: ~
```

## Installation

Perform the following steps to install and use the basic functionality of the OneupUploaderBundle:

* Download OnueupUploaderBundle using Composer
* Enable the bundle
* Configure the bundle
* Prepare your frontend

### Step 1: Download the OneupUploaderBundle

Add OneupUploaderBundle to your composer.json using the following construct:

```js
{
    "require": {
        "oneup/uploader-bundle": "~1.3"
    }
}
```

Now tell composer to download the bundle by running the following command:

    $> php composer.phar update oneup/uploader-bundle

Composer will now fetch and install this bundle in the vendor directory ```vendor/oneup```

### Step 2: Enable the bundle

Enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Oneup\UploaderBundle\OneupUploaderBundle(),
    );
}
```

### Step 3: Configure the bundle

This bundle was designed to just work out of the box. The only thing you have to configure in order to get this bundle up and running is a mapping.

```yaml
# app/config/config.yml

oneup_uploader:
    mappings:
        gallery:
            frontend: blueimp # or any uploader you use in the frontend
```

To enable the dynamic routes, add the following to your routing configuration file.

```yaml
#  app/config/routing.yml

oneup_uploader:
    resource: .
    type: uploader
```

The default directory that is used to upload files to is `web/uploads/{mapping_name}`.

> It was reported that in some cases this directory was not created automatically. Please double check its existance if the upload does not work for you.

### Step 4: Prepare your frontend

No matter what library you choose, be sure to connect the corresponding endpoint property to the dynamic route created from your mapping. To get a url for a specific mapping you can use the `oneup_uploader.templating.uploader_helper` service as follows:

```php
$helper = $this->container->get('oneup_uploader.templating.uploader_helper');
$endpoint = $helper->endpoint('gallery');
```

or in a Twig template you can use the `oneup_uploader_endpoint` function:

    {{ oneup_uploader_endpoint('gallery') }}

So if you take the mapping described before, the generated route name would be `_uploader_gallery`. Follow one of the listed guides to include your frontend:

* [Use FineUploader](frontend_fineuploader.md)
* [Use jQuery File Upload](frontend_blueimp.md)
* [Use YUI3 Uploader](frontend_yui3.md)
* [Use Uploadify](frontend_uploadify.md)
* [Use FancyUpload](frontend_fancyupload.md)
* [Use MooUpload](frontend_mooupload.md)
* [Use Plupload](frontend_plupload.md)
* [Use Dropzone](frontend_dropzone.md)

## Next steps

After installing and setting up the basic functionality of this bundle you can move on and integrate
some more advanced features.

* [Process uploaded files using custom logic](custom_logic.md)
* [Return custom data to frontend](response.md)
* [Enable chunked uploads](chunked_uploads.md)
* [Using the Orphanage](orphanage.md)
* [Use Gaufrette as storage layer](gaufrette_storage.md)
* [Include your own Namer](custom_namer.md)
* [Use custom error handlers](custom_error_handler.md)
* [Support a custom uploader](custom_uploader.md)
* [Validate your uploads](custom_validator.md)
* [General/Generic Events](events.md)
* [Enable Session upload progress / upload cancelation](progress.md)
* [Use Chunked Uploads behind Load Balancers](load_balancers.md)
* [Template helpers Reference](templating.md)
* [Configuration Reference](configuration_reference.md)
* [Testing this bundle](testing.md)

## FAQ

> I want to use a frontend library you don't yet support

This is absolutely no problem, just follow the instructions given in the corresponding [documentation file](custom_uploader.md). If you think that others could profit of your code, please consider making a pull request. I'm always happy for any kind of contribution.

> Why didn't you implement the _delete_ feature provided by Fine Uploader?

FineUploaders _delete Feature_ is using generated unique names we would have to store in order to track down which file to delete. But both the storage and the deletetion of files are tight-coupled with the logic of your very own implementation. This means we leave the _delete Feature_ open for you to implement. Information on how the route must be crafted can be found on the [official documentation](http://docs.fineuploader.com/features/delete.html) and on [the blog](http://blog.fineuploader.com/2013/01/delete-uploaded-file-in-33.html) of Fine Uploader.

> Why didn't you implement the _delete_ feature provided by another library?

See the answer to the previous question and replace _FineUploader_ by the library you have chosen.
