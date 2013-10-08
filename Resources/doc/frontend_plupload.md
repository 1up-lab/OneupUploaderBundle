Use Plupload in your Symfony2 application
=========================================

Download [Plupload](http://http://www.plupload.com/) and include it in your template. Connect the `url` property to the dynamic route `_uploader_{mapping_name}`.

```html
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript" src="http://bp.yahooapis.com/2.4.21/browserplus-min.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.9/jquery-ui.min.js"></script>
<script type="text/javascript" src="http://www.plupload.com/plupload/js/plupload.full.js"></script>
<script type="text/javascript" src="http://www.plupload.com/plupload/js/jquery.ui.plupload/jquery.ui.plupload.js"></script>

<script type="text/javascript">
$(document).ready(function()
{
    $("#fileupload").plupload(
    {
        runtimes: "html5",
        url: "{{ oneup_uploader_endpoint('gallery') }}"
    });
});
</script>

<div id="fileupload"></div>
```

Configure the OneupUploaderBundle to use the correct controller:

```yaml
# app/config/config.yml

oneup_uploader:
    mappings:
        gallery:
            frontend: plupload
```

Be sure to check out the [official manual](http://www.plupload.com/documentation.php) for details on the configuration.

The Plupload library does not send a unique id along the file upload request. Because of that, we only have the filename as an information to distinguish uploads. It is possible though that two users upload a file with the same name at the same time. To further tell these files apart, the SessionId is used. If you provide anonymous uploads on your application, be sure to configure the firewall accordingly.

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
