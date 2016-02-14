Set a custom subfolder
======================

If you want to add a custom subfolder to the path of uploaded file (e.g. dynamic subfolder), the only thing you need to do is to send a custom data named `subfolder`, containing a string with the desired subfolder.

## Example
The following example is an implementation with BluImp JQuery File Upload.

```js
$('#fileupload').fileupload({
    formData: {subfolder: 'my_custom_folder'}
});
```

With this example, files will be uploaded in the folder `my_custom_folder` within the main uploads folder (e.g. `web/uploads`).
