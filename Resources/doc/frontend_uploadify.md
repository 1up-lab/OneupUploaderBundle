Use Uploadify
=============

Download [Uploadify](http://www.uploadify.com/download/) and include it in your template. Connect the `uploader` property to the dynamic route `_uploader_{mapping_name}` and include the FlashUploader file.

> If you are using UploadiFive, please drop me a note. I'd like to know if this bundle also works for the HTML5-Version of this frontend library.

```html

<script type="text/javascript" src="{{ asset('bundles/acmedemo/js/jquery.uploadify.js') }}"></script>
<script type="text/javascript">
$(document).ready(function()
{
    $('#fileupload').uploadify(
    {
        swf: "{{ asset('bundles/acmedemo/js/uploadify.swf') }}",
        uploader: "{{ oneup_uploader_endpoint('gallery') }}"
    });

});
</script>

<div id="fileupload" />
```

Configure the OneupUploaderBundle to use the correct controller:

```yaml
# app/config/config.yml

oneup_uploader:
    mappings:
        gallery:
            frontend: uploadify
```

Be sure to check out the [official manual](http://www.uploadify.com/documentation/) for details on the configuration.

Next steps
----------

After this setup, you can move on and implement some of the more advanced features. A full list is available [here](https://github.com/1up-lab/OneupUploaderBundle/blob/master/Resources/doc/index.md#next-steps).

* [Process uploaded files using custom logic](custom_logic.md)
* [Return custom data to frontend](response.md)
* [Include your own Namer](custom_namer.md)
* [Configuration Reference](configuration_reference.md)
