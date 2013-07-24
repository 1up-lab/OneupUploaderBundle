Using Chunked Uploads
=====================

Fine Uploader comes bundled with the possibility to use so called chunked uploads. If enabed, an uploaded file will be split into equal sized blobs which are sequentially uploaded afterwards. In order to use this feature, be sure to enable it in the frontend.

```js
$(document).ready(function()
{
    var uploader = new qq.FineUploader({
        element: document.getElementById('uploader'),
        request: {
            endpoint: "{{ oneup_uploader_endpoint('gallery') }}"
        },
        chunking: {
            enabled: true,
            partSize: 10000000
        }
    });
});
```

The `partSize` property defines the maximum size of a blob, in this case 10MB.

> Be sure to select a partSize that fits your requirement. If it is too small, the speed of the overall upload drops significantly.

## Configure

You can configure the `ChunkManager` by using the following configuration parameters.

```
oneup_uploader:
    chunks:
        maxage: 86400
        directory: %kernel.cache_dir%/uploader/chunks
```

You can choose a custom directory to save the chunks temporarily while uploading by changing the parameter `directory`.

## Clean up

The ChunkManager can be forced to clean up old and orphanaged chunks by using the command provided by the OneupUploaderBundle.

    $> php app/console oneup:uploader:clean-chunks

This parameter will clean all chunk files older than the `maxage` value in your configuration.
