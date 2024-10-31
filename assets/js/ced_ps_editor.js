/**
* Adds a custom button to the tinymce editor
*/
(function() {

	tinymce.PluginManager.add('ced_ps_mce_button', function( editor, url ) {
		editor.addButton( 'ced_ps_mce_button', {
			text: 'Keywords',
			type: 'listbox',
			icon: false,
			onselect: function (e) {
		        editor.insertContent(this.value());
		      },
		    values: [
		        { text: 'Product Name', value: '&nbsp;<strong>%product_name%</strong>' },
		        { text: 'Username', value: '&nbsp;<em>%user%</em>' },
		        { text: 'Login url', value: '&nbsp;%permalink%' },
		        { text: 'Site Link', value: '&nbsp;%site_url%' }
		      ],			
		});
	});

})();