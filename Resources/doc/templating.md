Template Helpers
================

The following template helpers are available.

* `oneup_uploader_endpoint` Returns the endpoint route to which the uploader should send its files.
* `oneup_uploader_progress` Returns the route where you can ping the progress of a given file if configured.
* `oneup_uploader_cancel` Returns the route where you can cancel an upload if configured.
* `oneup_uploader_upload_key` Returns the php.ini variable `session.upload_progress.name`. You may need this for getting progress configured.
* `oneup_uploader_maxsize` Returns the configured max size value in bytes for a given mapping.

Use these helpers in your templates like this:

```twig
{{ oneup_uploader_endpoint('gallery') }}
```

> **Note**: `oneup_uploader_upload_key` does not need a mapping key.