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
        storage:
            directory: %kernel.cache_dir%/uploader/chunks
```

You can choose a custom directory to save the chunks temporarily while uploading by changing the parameter `directory`.

Since version 1.0 you can also use a Gaufrette filesystem as the chunk storage. To do this you must first
set up [Gaufrette](gaufrette_storage.md).There are however some additional things to keep in mind.
The configuration for the Gaufrette chunk storage should look as the following:
```
oneup_uploader:
    chunks:
        maxage: 86400
        storage:
            type: gaufrette
            filesystem: gaufrette.gallery_filesystem 
            prefix: 'chunks'
            stream_wrapper: 'gaufrette://gallery/'
```

> Setting the stream_wrapper is heavily recommended for better performance, see the reasons in the [gaufrette configuration](gaufrette_storage.md#configure-your-mappings)

As you can see there are is a new option, ```prefix```. It represents the directory 
 *relative* to the filesystem's directory which the chunks are stored in.
Gaufrette won't allow it to be outside of the filesystem.

> You can only use stream capable filesystems for the chunk storage, at the time of this writing
only the Local filesystem is capable of streaming directly.

This will give you a better structured directory,
as the chunk's folders and the uploaded files won't mix with each other. 
> You can set it to an empty string (```''```), if you don't need it. Otherwise it defaults to ```chunks```.

The chunks will be read directly from the tmp and appended to the already existing part on the given filesystem,
resulting in only 1 read and 1 write operation.

You can achieve the biggest improvement if you use the same filesystem as your storage, as if you do so, the assembled
file only has to be moved out of the chunk directory, which on the same filesystem takes almost not time.

> The ```load distribution``` is forcefully turned on, if you use gaufrette as the chunk storage.


## Clean up

The ChunkManager can be forced to clean up old and orphanaged chunks by using the command provided by the OneupUploaderBundle.

    $> php app/console oneup:uploader:clean-chunks

This parameter will clean all chunk files older than the `maxage` value in your configuration.
