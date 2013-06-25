Enable Session Upload Progress
==============================

As of PHP 5.4, there is the possibility to track the upload progress of individual files being uploaded.
To enable this feature be sure you enable it in your configuration.

```yaml
# app/config/config.yml

oneup_uploader:
    mappings:
        gallery:
            enable_progress: true
```

The OneupUploaderBundle generates a new route for you to probe the status of a file currently being uploaded.
Once again, there are frontend helpers to simplify this process:

* `{{ oneup_uploader_progress('gallery') }}` This helper will return the path where you can send your progress request to.
* `{{ oneup_uploader_upload_key() }}` This helper will return the ini option `session.upload_progress.name`.

An example of this feature using the jQuery File Upload plugin can be found in the [corresponding wiki article](https://github.com/blueimp/jQuery-File-Upload/wiki/PHP-Session-Upload-Progress):

```js
$('#fileupload').bind('fileuploadsend', function (e, data) {
    if (data.dataType.substr(0, 6) === 'iframe') {
        var progressObj = {
            name: '{{ oneup_uploader_upload_key() }}',
            value: (new Date()).getTime() // pseudo unique ID
        };
        
        data.formData.push(progressObj);
        data.context.data('interval', setInterval(function () {
            $.get('{{ oneup_uploader_progress("gallery") }}', $.param([progressObj]), function (result) {
                e = $.Event( 'progress', {bubbles: false, cancelable: true});
                $.extend(e, result);
                ($('#fileupload').data('blueimp-fileupload') ||
                    $('#fileupload').data('fileupload'))._onProgress(e, data);
            }, 'json');
        }, 1000));
    }
}).bind('fileuploadalways', function (e, data) {
    clearInterval(data.context.data('interval'));
});
```

Be sure to initially send the key/value in your upload request for this to work. Or to quote the [PHP-Manual](http://php.net/manual/en/session.upload-progress.php):

> The upload progress will be available in the $_SESSION superglobal when an upload is in progress, and when POSTing a variable of the same name as the session.upload_progress.name INI setting is set to.


Enable Cancelation Route
------------------------

The new API also comes with a handy new feature to cancel uploads currently in process.
To activate the corresponding route, simply enable it in your configuration file.

```yaml
# app/config/config.yml

oneup_uploader:
    mappings:
        gallery:
            enable_cancelation: true
```

If enabled, you can use the frontend helper function to get the correct route:

```twig
{{ oneup_uploader_cancel('gallery') }}
```

You still need to send the correct value for the `oneup_uploader_upload_key`.

Caveats
-------

You'll need an activated session for this feature to work correctly. Be sure to enable the session for anonymous users, if you provide anonymous uploads:

```yml
# app/config/security.yml

security:
    firewalls:
        main:
            pattern: ^/
            anonymous: true
```
