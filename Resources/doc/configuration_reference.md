Configuration Reference
=======================

All available configuration options are listed below with their default values.

``` yaml
oneup_uploader:
    chunks:
        maxage:               604800
        directory:            ~
    orphanage:
        maxage:               604800
        directory:            ~
    mappings:             # Required

        # Prototype
        id:
            storage:
                service:              ~
                type:                 filesystem
                filesystem:           ~
                directory:            ~
            allowed_extensions:   []
            disallowed_extensions:  []
            max_size:             9223372036854775807
            use_orphanage:        false
            namer:                oneup_uploader.namer.uniqid
```