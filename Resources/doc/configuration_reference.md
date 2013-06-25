Configuration Reference
=======================

All available configuration options along with their default values are listed below.

``` yaml
oneup_uploader:
    chunks:
        maxage:               604800
        directory:            ~
    orphanage:
        maxage:               604800
        directory:            ~
    twig:                 true
    mappings:             # Required

        # Prototype
        id:
            frontend:             fineuploader
            custom_frontend:
                name:                 ~
                class:                ~
            storage:
                service:              ~
                type:                 filesystem
                filesystem:           ~
                directory:            ~
            allowed_extensions:    []
            disallowed_extensions: []
            allowed_mimetypes:     []
            disallowed_mimetypes:  []
            max_size:             9223372036854775807
            use_orphanage:        false
            enable_progress:      false
            enable_cancelation:   false
            namer:                oneup_uploader.namer.uniqid
```
