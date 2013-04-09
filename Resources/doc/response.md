Return custom data to the Frontend
==================================

There are some use cases where you need custom data to be returned to the frontend. For example the id of a generated Doctrine Entity or the like. To cover this, you can use `UploaderResponse` passed through all `Events`.

```php
