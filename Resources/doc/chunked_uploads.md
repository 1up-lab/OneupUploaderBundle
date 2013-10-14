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

## Use Gaufrette to store chunk files

You can also use a Gaufrette filesystem as the chunk storage. A possible use case is to use chunked uploads behind non-session sticky load balancers.
To do this you must first set up [Gaufrette](gaufrette_storage.md). There are however some additional things to keep in mind.
The configuration for the Gaufrette chunk storage should look as the following:

```yaml
oneup_uploader:
    chunks:
        maxage: 86400
        storage:
            type: gaufrette
            filesystem: gaufrette.gallery_filesystem
            prefix: 'chunks'
            stream_wrapper: 'gaufrette://gallery/'
```

> :exclamation: Setting the `stream_wrapper` is heavily recommended for better performance, see the reasons in the [gaufrette configuration](gaufrette_storage.md#configure-your-mappings)

As you can see there are is an option, `prefix`. It represents the directory
 *relative* to the filesystem's directory which the chunks are stored in.
Gaufrette won't allow it to be outside of the filesystem.
This will give you a better structured directory,
as the chunk's folders and the uploaded files won't mix with each other.
You can set it to an empty string (`''`), if you don't need it. Otherwise it defaults to `chunks`.

> :exclamation: You can only use stream capable filesystems for the chunk storage, at the time of this writing
only the Local filesystem is capable of streaming directly.

The chunks will be read directly from the temporary directory and appended to the already existing part on the given filesystem,
resulting in only one single read and one single write operation.

> :exclamation: Do not use a Gaufrette filesystem for the chunk storage and a local filesystem for the mapping. This is not possible to check during container setup and will throw unexpected errors at runtime!

You can achieve the biggest improvement if you use the same filesystem as your storage. If you do so, the assembled
file only has to be moved out of the chunk directory, which takes no time on a local filesystem.

> The load distribution is forcefully turned on, if you use Gaufrette as the chunk storage.

See the [Use Chunked Uploads behind Load Balancers](load_balancers.md) section in the documentation for a full configuration example.

## Clean up

The ChunkManager can be forced to clean up old and orphanaged chunks by using the command provided by the OneupUploaderBundle.

    $> php app/console oneup:uploader:clean-chunks

This parameter will clean all chunk files older than the `maxage` value in your configuration.
