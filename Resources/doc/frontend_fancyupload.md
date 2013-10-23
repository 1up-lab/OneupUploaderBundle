Use FancyUpload
=============

Download [FancyUpload](http://digitarald.de/project/fancyupload/) and include it in your template. Connect the `url` property to the dynamic route `_uploader_{mapping_name}` and include the FlashUploader file (`path`).

> If you have any idea on how to make a minimal example about the usage of FancyUpload, consider creating a pull-request.

```html

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/mootools/1.2.2/mootools.js"></script>
<script type="text/javascript" src="{{ asset('bundles/acmedemo/js/Swiff.Uploader.js') }}"></script>
<script type="text/javascript" src="{{ asset('bundles/acmedemo/js/Fx.ProgressBar.js') }}"></script>
<script type="text/javascript" src="{{ asset('bundles/acmedemo/js/FancyUpload2.js') }}"></script>

<script type="text/javascript">
window.addEvent('domready', function()
{
    var uploader = new FancyUpload2($('demo-status'), $('demo-list'),
    {
		url: $('form-demo').action,
		fieldName: 'photoupload',
		path: "{{ asset('bundles/acmedemo/js/Swiff.Uploader.swf') }}",
		limitSize: 2 * 1024 * 1024,
		onLoad: function() {
			$('demo-status').removeClass('hide');
			$('demo-fallback').destroy();
		},
		debug: true,
		target: 'demo-browse'
	});

	$('demo-browse').addEvent('click', function()
    {
		swiffy.browse();
		return false;
	});

	$('demo-select-images').addEvent('change', function()
    {
		var filter = null;
		if (this.checked) {
			filter = {'Images (*.jpg, *.jpeg, *.gif, *.png)': '*.jpg; *.jpeg; *.gif; *.png'};
		}
		uploader.options.typeFilter = filter;
	});

	$('demo-clear').addEvent('click', function()
    {
		uploader.removeFile();
		return false;
	});

	$('demo-upload').addEvent('click', function()
    {
		uploader.upload();
		return false;
	});
});
</script>


<form action="{{ oneup_uploader_endpoint('gallery') }}" method="post" enctype="multipart/form-data" id="form-demo">
	<fieldset id="demo-fallback">
		<legend>File Upload</legend>
		<p>
			Selected your photo to upload.<br />
			<strong>This form is just an example fallback for the unobtrusive behaviour of FancyUpload.</strong>
		</p>
		<label for="demo-photoupload">
			Upload Photos:
			<input type="file" name="photoupload" id="demo-photoupload" />
		</label>
	</fieldset>

	<div id="demo-status" class="hide">
		<p>
			<a href="#" id="demo-browse">Browse Files</a> |
			<input type="checkbox" id="demo-select-images" /> Images Only |
			<a href="#" id="demo-clear">Clear List</a> |
			<a href="#" id="demo-upload">Upload</a>
		</p>
		<div>
			<strong class="overall-title">Overall progress</strong><br />
			<img src="{{ asset('bundles/acmedemo/images/progress-bar/bar.gif') }}" class="progress overall-progress" />
		</div>
		<div>
			<strong class="current-title">File Progress</strong><br />
			<img src="{{ asset('bundles/acmedemo/images/progress-bar/bar.gif') }}" class="progress current-progress" />
		</div>
		<div class="current-text"></div>
	</div>
	<ul id="demo-list"></ul>
</form>

```

Configure the OneupUploaderBundle to use the correct controller:

```yaml
# app/config/config.yml

oneup_uploader:
    mappings:
        gallery:
            frontend: fancyupload
```

Be sure to check out the [official manual](http://digitarald.de/project/fancyupload/) for details on the configuration.

Next steps
----------

After this setup, you can move on and implement some of the more advanced features. A full list is available [here](https://github.com/1up-lab/OneupUploaderBundle/blob/master/Resources/doc/index.md#next-steps).

* [Process uploaded files using custom logic](custom_logic.md)
* [Return custom data to frontend](response.md)
* [Include your own Namer](custom_namer.md)
* [Configuration Reference](configuration_reference.md)
