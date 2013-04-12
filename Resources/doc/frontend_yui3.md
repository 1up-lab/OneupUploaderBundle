Use YUI3 Upload
===============

Download [YUI3 Upload](http://yuilibrary.com/yui/docs/uploader/) and include it in your template. Connect the `uploadURL` property to the dynamic route `_uploader_{mapping_name}`.

```html
<script src="http://yui.yahooapis.com/3.9.1/build/yui/yui-min.js"></script>
<script>
YUI().use('uploader', function (Y) {
    
    var uploader = new Y.Uploader(
    {
        multipleFiles: true,
        uploadURL: "{{ oneup_uploader_endpoint('gallery') }}"
    }).render("#fileupload");
    
    uploader.on('fileselect', function()
    {
        uploader.uploadAll();
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
            frontend: yui3
```

Be sure to check out the [official manual](http://yuilibrary.com/yui/docs/uploader/) for details on the configuration.