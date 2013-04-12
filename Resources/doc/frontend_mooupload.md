Use MooUpload in your Symfony2 application
==========================================

Download [MooUpload](https://github.com/juanparati/MooUpload) and include it in your template. Connect the `action` property to the dynamic route `_uploader_{mapping_name}`.

```html
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/mootools/1.4.0/mootools-yui-compressed.js"></script>
<script type="text/javascript" src="http://www.livespanske.com/labs/MooUpload/MooUpload.js"></script>
<script type="text/javascript">

window.addEvent("domready", function()
{
	var myUpload = new MooUpload("fileupload",
    {
		action: "{{ oneup_uploader_endpoint('gallery') }}",
		method: "auto"
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
            frontend: mooupload
```

Be sure to check out the [official manual](https://github.com/juanparati/MooUpload) for details on the configuration.