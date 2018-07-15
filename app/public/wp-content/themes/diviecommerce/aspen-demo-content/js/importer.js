var ags_demo_importer_pb;
jQuery(document).ready(function($) {
	$('#ags-demo-importer-form').submit(function() {
		$('#ags-demo-importer-status-complete, #ags-demo-importer-complete-message').hide();
		$('#ags-demo-importer-status-inprogress').show();
		
		$(window.frames['ags-demo-importer-frame'].document.body).remove();
		
		if ($('#ags-demo-importer-progress').data('circle-progress')) {
			$('#ags-demo-importer-progress').replaceWith('<div id="ags-demo-importer-progress"><strong></strong></div>');
		}
		$('#ags-demo-importer-progress')
			.circleProgress()
			.on('circle-animation-progress', function(ev, animationProgress, value) {
				$(this).children('strong').text(Math.round(value * 100) + '%');
			});
		$('#ags-demo-importer-status').show();
		
		$('#ags-demo-importer-frame').css('display', 'block');
	});
});

function ags_demo_importer_progress(value) {
	jQuery('#ags-demo-importer-progress').circleProgress('value', value);
	if (value == 1) {
		jQuery('#ags-demo-importer-status-complete').show();
		jQuery('#ags-demo-importer-status-inprogress').hide();
		jQuery('#ags-demo-importer-complete-message').slideDown();
	}
}