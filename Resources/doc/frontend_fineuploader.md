Use FineUploader
================

Download [FineUploader 5](http://fineuploader.com/) and include it in your template. Connect the `endpoint` property to the dynamic route `_uploader_{mapping_name}`. This example is based on the [UI Mode](http://docs.fineuploader.com/branch/master/quickstart/01-getting-started.html) of FineUploader.

```html
<link href="fine-uploader/fine-uploader-new.min.css" rel="stylesheet">
<script type="text/javascript" src="fine-uploader/fine-uploader.min.js"></script>
<script type="text/javascript">
    var uploader = new qq.FineUploader({
        element: document.getElementById('fine-uploader'),
        request: {
            endpoint: "{{ oneup_uploader_endpoint('gallery') }}"
        }
    });
</script>

<div id="fine-uploader"></div>
```

You can find a fully example of all available settings on the [official documentation](http://docs.fineuploader.com/branch/master/quickstart/02-setting_options.html).

If you are using Fine Uploader UI, as described in this example, you **MUST** include a template in your document/markup. You can use the ``default.html`` file in the templates directory bundled with the FineUploader library and customize it as desired. You even can use an inline template. See the official [styling documentation page](http://docs.fineuploader.com/branch/master/features/styling.html) for more details.

Configure the OneupUploaderBundle to use the correct controller:

```yaml
# app/config/config.yml

oneup_uploader:
    mappings:
        gallery:
            frontend: fineuploader
```

Be sure to check out the [official manual](https://github.com/Widen/fine-uploader/blob/master/readme.md) for details on the configuration.

Next steps
----------

After this setup, you can move on and implement some of the more advanced features. A full list is available [here](https://github.com/1up-lab/OneupUploaderBundle/blob/master/Resources/doc/index.md#next-steps).

* [Process uploaded files using custom logic](custom_logic.md)
* [Return custom data to frontend](response.md)
* [Include your own Namer](custom_namer.md)
* [Configuration Reference](configuration_reference.md)
