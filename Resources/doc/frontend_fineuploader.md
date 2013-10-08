Use FineUploader
================

Download [FineUploader](http://fineuploader.com/) and include it in your template. Connect the `endpoint` property to the dynamic route `_uploader_{mapping_name}`.

```html
<script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="js/jquery.fineuploader-3.4.1.js"></script>
<script type="text/javascript">
$(document).ready(function()
{
    var uploader = new qq.FineUploader({
        element: $('#uploader')[0],
        request: {
            endpoint: "{{ oneup_uploader_endpoint('gallery') }}"
        }
    });
});
</script>

<div id="uploader"></div>
```

Configure the OneupUploaderBundle to use the correct controller:

```yaml
# app/config/config.yml

oneup_uploader:
    mappings:
        gallery:
            frontend: fineuploader
```

Be sure to check out the [official manual](https://github.com/Widen/fine-uploader/blob/master/readme.md) for details on the configuration.
