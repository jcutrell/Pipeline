<?phprequire_once TEMPLATE_PATH.'/site/helper/format.php';$uploads = $SOUP->get('uploads');$fork = $SOUP->fork();$fork->set('title', 'Attached Files');$fork->startBlockSet('body');?><script type="text/javascript" src="<?= Url::scripts() ?>/flowplayer-3.2.6.min.js"></script><script type="text/javascript">$(document).ready(function(){	// set up flowplayer for audio, video, flash files	$('#flowplayer').dialog({		modal: true,		autoOpen: false,		resizable: false,		height: 320,		width: 420	});	$('div.video a.preview, div.audio a.preview, div.flash a.preview').click(function(){		var url = $(this).attr('href');		var title = $(this).attr('title');		$('#flowplayer').dialog({ title: title });		$('#flowplayer').dialog('open');		flowplayer("flowplayer", "<?= Url::base() ?>/lib/flowplayer/flowplayer-3.2.7.swf", {			clip: {				url: url,				scaling: 'fit',				onFinish: function() { this.getPlugin("play").hide(); }			}		});		return false;	});		// images don't use flowplayer, just a standard dialog		$('div.image a.thumb.preview').click(function(){		$('#imageviewer img').remove();		var img = $(this).find('img');		$(img).clone().css('display','block').appendTo('#imageviewer');		//$(img).css('display','block');		//var height = $(img).attr('height');		//var width = $(img).attr('width');		var title = $(img).attr('alt');		$('#imageviewer').dialog({			title: title,			modal: true,			resizable: false,			position: 'center',			height: 'auto',			width: 'auto'		});		return false;	});		// non-thumbnail 'Preview' link	$('div.image p.secondary a.preview').click(function(){		$(this).parent().parent().find('a.thumb.preview').click();		return false;	});});</script><div id="flowplayer" style="overflow: hidden;"></div><div id="imageviewer" style="overflow: hidden;"></div><?phpif($uploads != null) {	echo '<ul class="segmented-list">';	foreach($uploads as $u) {		$thumbURL = $u->getThumbURL();		$previewURL = $u->getPreviewURL();		$downloadURL = $u->getDownloadURL();		$className = '';				// get CSS class for icon		switch($u->getMime()) {			case 'image/jpg':			case 'image/jpeg':			case 'image/png':			case 'image/gif':				$className = ' image';				break;			case 'audio/mpeg': 			case 'application/octet-stream':				if($u->getExtension() == 'mp3') {					$className = ' audio';				} elseif( ($u->getExtension() == 'fla') ||					($u->getExtension() == 'swf') ) {					$className = ' flash';					}				break;			case 'application/x-shockwave-flash': 			case 'video/x-flv': 				$className = ' flash';				break;			case 'video/mpeg': 			case 'video/quicktime': 			case 'video/x-msvideo': 				$className = ' video';				break;			default:				$className = '';				break;		}?><li>	<div class="upload<?= $className ?>">	<?php if($previewURL != null): ?>		<a class="thumb preview" href="<?= $previewURL ?>" style="background-image: url('<?= $thumbURL ?>');" title="<?= $u->getOriginalName() ?>">			<?php if($className == ' image'): ?>			<img src="<?= $previewURL ?>" style="display: none;" height="<?= $u->getHeight() ?>" width="<?= $u->getWidth() ?>" alt="<?= $u->getOriginalName() ?>" />			<?php else: ?>			<img src="<?= Url::images() ?>/play_large.png" alt="Play Preview" />			<?php endif; ?>		</a>	<?php endif; ?>	<h6 class="primary"><a href="<?= $downloadURL ?>"><?= $u->getOriginalName() ?></a></h6>	<p class="secondary">		<?= formatFileSize($u->getSize()) ?> <span class="slash">/</span> 		<?php if($previewURL != null): ?>		<a class="preview" href="<?= $previewURL ?>" title="<?= $u->getOriginalName() ?>">Preview</a> <span class="slash">/</span>		<?php endif; ?>		<a href="<?= $downloadURL ?>">Download</a>	</p>	</div><!-- .upload --></li><?php	}	echo '</ul>';} else {	echo '<p>(none)</p>';}?><?php$fork->endBlockSet();$fork->render('site/partial/panel');