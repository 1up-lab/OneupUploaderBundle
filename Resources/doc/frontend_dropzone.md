Use Dropzone in your Symfony2 application
=========================================

Download [Dropzone](http://www.dropzonejs.com/) and include it in your template. Connect the `action` property of the form to the dynamic route `_uploader_{mapping_name}`.

```html
<script type="text/javascript" src="https://rawgithub.com/enyo/dropzone/master/downloads/dropzone.js"></script>

<form action="{{ oneup_uploader_endpoint('gallery') }}" class="dropzone">
</form>
```

Configure the OneupUploaderBundle to use the correct controller:

```yaml
# app/config/config.yml

oneup_uploader:
    mappings:
        gallery:
            frontend: dropzone
```

Be sure to check out the [official manual](http://www.dropzonejs.com/) for details on the configuration.

Next steps
----------

After this setup, you can move on and implement some of the more advanced features. A full list is available [here](https://github.com/1up-lab/OneupUploaderBundle/blob/master/Resources/doc/index.md#next-steps).

* [Process uploaded files using custom logic](custom_logic.md)
* [Return custom data to frontend](response.md)
* [Include your own Namer](custom_namer.md)
* [Configuration Reference](configuration_reference.md)
