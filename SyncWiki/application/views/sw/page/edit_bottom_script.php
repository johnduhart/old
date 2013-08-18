		<script type="text/javascript">
			$("#editorTools ul").each(function () {
				var $links = $(this).find('a');
				
				for(i=0;i<$links.length;i++) {
					if($links[i].hash.substr(0,1) != '#' || $links[i].hash.length < 2)
					{
						$links.splice(i, 1);
						i--;
					}
				}
				
				var panelIds = $links.map(function() { return this.hash; }).get().join(","),
					$panels = $(panelIds),
					$panelwrapper = $panels.filter(':first').parent(),
					delay = 500,
					heightOffset = 34;
				
				$panels.hide();
				
				$links.click(function () {
					var link = this,
						$link = $(this);
					
					if ($link.is('.selected')) {
						$links.removeClass('selected');
						if ($.support.opacity) {
							$panels.stop().animate({opacity: 0}, delay);
						}
						$panelwrapper.stop().animate({height: 0}, delay);
						return;
					}
					
					$links.removeClass('selected');
					$link.addClass('selected');
					
					if ($.support.opacity) {
						$panels.stop().animate({opacity: 0}, delay);
					}
					
					$panelwrapper.stop().animate({
						height: 0
					}, delay, function () {
						var height = $panels.hide().filter(link.hash).css('opacity', 1).show().height() + heightOffset;
						$panelwrapper.animate({
							height: height
						}, delay);
					});
				});
			});
			
			$(".panels .cancel").click(function () {
				$("#editorTools .selected").click();
			});
		</script>
