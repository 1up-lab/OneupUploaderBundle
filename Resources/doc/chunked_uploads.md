Using Chunked Uploads
=====================

Fine Uploader comes bundled with the possibility to use so called chunked uploads. If enabed, an uploaded file will be split into equal sized blobs which are sequentially uploaded afterwards. In order to use this feature, be sure to enable it in the frontend.

```js
$(document).ready(function()
{
    var uploader = new qq.FineUploader({
        element: document.getElementById('uploader'),
        request: {
            endpoint: "{{ path('_uploader_gallery') }}"
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