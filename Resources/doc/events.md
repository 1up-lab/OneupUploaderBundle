OneupUploaderBundle Events
==========================

For a list of general Events, you can always have a look at the `UploadEvents.php` file in the root of this bundle.

* `oneup_uploader.post_upload` Will be dispatched after a file has been uploaded and moved.
* `oneup_uploader.post_persist` The same as `oneup_uploader.post_upload` but will only be dispatched if no `Orphanage` is used.

Moreover this bundles also dispatches some special kind of generic events you can listen to.

* `oneup_uploader.post_upload.{mapping}`
* `oneup_uploader.post_persist.{mapping}`

The `{mapping}` part is the key of your configured mapping. The examples in this documentation always uses the mapping key `gallery`. So the dispatched event would be called `oneup_uploader.post_upload.gallery`.
Using these generic events can save you some time and coding lines, as you don't have to check for the correct type in the `EventListener`.

See the [custom logic section](custom_logic.md) of this documentation for specific examples on how to use these Events.