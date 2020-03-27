Use jQuery File Upload
======================

Download [jQuery File Upload](http://blueimp.github.io/jQuery-File-Upload/) and include it in your template. Connect the `data-url` property on the HTML element to the dynamic route `_uploader_{mapping_name}`.

```html
<script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="js/jquery.ui.widget.js"></script>
<script type="text/javascript" src="js/jquery.iframe-transport.js"></script>
<script type="text/javascript" src="js/jquery.fileupload.js"></script>
<script type="text/javascript">
$(document).ready(function()
{
    $('#fileupload').fileupload({});
});
</script>

<input id="fileupload" type="file" name="files[]" data-url="{{ oneup_uploader_endpoint('gallery') }}" multiple />
```

Configure the OneupUploaderBundle to use the correct controller:

```yaml
# app/config/config.yml

oneup_uploader:
    mappings:
        gallery:
            frontend: blueimp
```

Be sure to check out the [official manual](https://github.com/blueimp/jQuery-File-Upload#jquery-file-upload-plugin) for details on the configuration.

The jQuery File Upload library does not send a unique id along the file upload request. Because of that, we only have the filename as an information to distinguish uploads. It is possible though that two users upload a file with the same name at the same time. To further tell these files apart, the SessionId is used. If you provide anonymous uploads on your application, be sure to configure the firewall accordingly.

```yml
# app/config/security.yml

security:
    firewalls:
        main:
            pattern: ^/
            anonymous: true
```

Next steps
----------

After this setup, you can move on and implement some of the more advanced features. A full list is available [here](https://github.com/1up-lab/OneupUploaderBundle/blob/master/Resources/doc/index.md#next-steps).

* [Process uploaded files using custom logic](custom_logic.md)
* [Return custom data to frontend](response.md)
* [Include your own Namer](custom_namer.md)
* [Configuration Reference](configuration_reference.md)
