Getting started
===============

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
        "oneup/uploader-bundle": "*"
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
        gallery: ~
```

To enable the dynamic routes, add the following to your routing configuration file.

```yaml
#  app/config/routing.yml

oneup_uploader:
    resource: .
    type: uploader
```

### Step 4: Prepare your frontend

As this is a server implementation for Fine Uploader, you have to include this library in order to upload files through this bundle. You can find them on the [official page](http://fineuploader.com) of Fine Uploader. Be sure to connect the endpoint property to the dynamic route created from your mapping. It has the following form:

    _uploader_{mapping_key}

```html
<script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="js/jquery.fineuploader-3.4.1.js"></script>
<script type="text/javascript">
$(document).ready(function()
{
    var uploader = new qq.FineUploader({
        element: $('#uploader'),
        request: {
            endpoint: "{{ path('_uploader_gallery') }}"
        }
    });
});
</script>

<div id="uploader"></div>
```

This is of course a very minimal setup. Be sure to include stylesheets for Fine Uploader if you want to use them.

## Next steps

After installing and setting up the basic functionality of this bundle you can move on and integrate
some more advanced features.

* [Process uploaded files using custom logic](custom_logic.md)
* [Return custom data to frontend](response.md)
* [Enable chunked uploads](chunked_uploads.md)
* [Using the Orphanage](orphanage.md)
* [Use Gaufrette as storage layer](gaufrette_storage.md)
* [Include your own Namer](custom_namer.md)
* [Testing this bundle](testing.md)
* [Configuration Reference](configuration_reference.md)

## FAQ

> Why didn't you implement the _delete_ feature provided by Fine Uploader?

Fine Uploaders _delete Feature_ is using generated unique names we would have to store in order to track down which file to delete. But both the storage and the deletetion of files are tight-coupled with the logic of your very own implementation. This means we leave the _delete Feature_ open for you to implement. Information on how the route must be crafted can be found on the [official documentation](https://github.com/Widen/fine-uploader/blob/master/docs/options-fineuploaderbasic.md#deletefile-option-properties) and on [the blog](http://blog.fineuploader.com/2013/01/delete-uploaded-file-in-33.html) of Fine Uploader.