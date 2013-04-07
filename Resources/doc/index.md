Getting started
===============

## Prerequisites

This bundle tested using Symfony2 versions 2.1+.

### Translations
If you wish to use the default texts provided with this bundle, you have to make sure that you have translator
enabled in your configuration file.

```yaml
# app/config/config.yml

framework:
    translator: ~
```

## Installation

Perform the following step to install and use the basic functionality of the OneupUploaderBundle:

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
    gallery: ~
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

* [Enable chunked uploads](chunked_uploads.md)
* Using the Orphanage
* Use Gaufrette as storage layer
* [Include your own Namer](custom_namer.md)
* Testing this bundle
* [Configuration Reference](configuration_reference.md)