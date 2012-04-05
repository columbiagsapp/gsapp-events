<?php
/**
 * @file
 * Theme implementation to display a node.
 *
 * Available variables:
 * - $title: the (sanitized) title of the node.
 * - $content: Node body or Neaser depending on $neaser flag.
 * - $user_picture: The node author's picture from user-picture.tpl.php.
 * - $date: Formatted creation date. Preprocess functions can reformat it by
 *   calling format_date() with the desired parameters on the $created variable.
 * - $name: Themed username of node author output from theme_username().
 * - $node_url: Direct url of the current node.
 * - $terms: the themed list of taxonomy term links output from theme_links().
 * - $display_submitted: whether submission information should be displayed.
 * - $submitted: Themed submission information output from
 *   theme_node_submitted().
 * - $links: Themed links like "Read more", "Add new comment", etc. output
 *   from theme_links().
 * - $classes: String of classes that can be used to style contextually through
 *   CSS. It can be manipulated through the variable $classes_array from
 *   preprocess functions. The default values can be one or more of the
 *   following:
 *   - node: The current template type, i.e., "theming hook".
 *   - node-[type]: The current node type. For example, if the node is a
 *     "Blog entry" it would result in "node-blog". Note that the machine
 *     name will often be in a short form of the human readable label.
 *   - node-teaser: Nodes in teaser form.
 *   - node-preview: Nodes in preview mode.
 *   The following are controlled through the node publishing options.
 *   - node-promoted: Nodes promoted to the front page.
 *   - node-sticky: Nodes ordered above other non-sticky nodes in teaser
 *     listings.
 *   - node-unpublished: Unpublished nodes visible only to administrators.
 *   The following applies only to viewers who are registered users:
 *   - node-by-viewer: Node is authored by the user currently viewing the page.
 *
 * Other variables:
 * - $node: Full node object. Contains data that may not be safe.
 * - $type: Node type, i.e. story, page, blog, etc.
 * - $comment_count: Number of comments attached to the node.
 * - $uid: User ID of the node author.
 * - $created: Time the node was published formatted in Unix timestamp.
 * - $classes_array: Array of html class attribute values. It is flattened
 *   into a string within the variable $classes.
 * - $zebra: Outputs either "even" or "odd". Useful for zebra striping in
 *   teaser listings.
 * - $id: Position of the node. Increments each time it's output.
 *
 * Node status variables:
 * - $build_mode: Build mode, e.g. 'full', 'teaser'...
 * - $teaser: Flag for the teaser state (shortcut for $build_mode == 'teaser').
 * - $page: Flag for the full page state.
 * - $promote: Flag for front page promotion state.
 * - $sticky: Flags for sticky post setting.
 * - $status: Flag for published status.
 * - $comment: State of comment settings for the node.
 * - $readmore: Flags true if the teaser content of the node cannot hold the
 *   main body content.
 * - $is_front: Flags true when presented in the front page.
 * - $logged_in: Flags true when the current user is a logged-in member.
 * - $is_admin: Flags true when the current user is an administrator.
 *
 * The following variable is deprecated and will be removed in Drupal 7:
 * - $picture: This variable has been renamed $user_picture in Drupal 7.
 *
 * @see template_preprocess()
 * @see template_preprocess_node()
 * @see zen_preprocess()
 * @see zen_preprocess_node()
 * @see zen_process()
 */
//dsm($node->field_event_poster);

$nodeloc = ($node->field_event_location[0]['view'] ? $node->field_event_location[0]['view'] . ", " : "");
$nodeloc_top = end($node->field_event_location);
if($nodeloc_top) $nodeloc_top_class = $nodeloc_top['view'];
$nodeloc_top_class = transliteration_clean_filename(strtolower(preg_replace("/ /", "-", trim($nodeloc_top_class))));

global $base_url;



?>


<div id="node-<?php print $node->nid; ?>" class="<?php print $classes; ?> <?php print ($teaser ? 'node-teaser' : 'node-full'); ?> clearfix <?php print $nodeloc_top_class; ?>">

  <?php if ($unpublished): ?>
    <div class="unpublished"><?php print t('Unpublished'); ?></div>
  <?php endif; ?>

<?php  /* DATE STUFF */
$dateobj = date_make_date($node->field_event_date[0]['value'], 'UTC'); 
$dateutcobj = clone $dateobj;
date_timezone_set($dateobj, timezone_open(date_default_timezone_name(TRUE)));

if (strtotime(date("Y-m-d")) == strtotime(date_format_date($dateobj, "custom", "Y-m-d"))) { 
	$istoday = "istoday";
}
if (strtotime(date("Y-m-d")) <= strtotime(date_format_date($dateobj, "custom", "Y-m-d"))) { 
	$isfuture = "isfuture";
}


if($node->field_event_visibility[0]['value'] == "private") $isprivate = "isprivate";

?>

<?php if($teaser) { ?>
	<?php if($isfuture) { print '<a class="futureanchor" name="future"></a>'; } ?>
  <div class="content teaser-content <?php print $istoday . " " . $isprivate; ?>" id="teaser-node-<?php print $nid; ?>">
	<a href="<?php print $node_url; ?>"<?php if($_GET['q'] == "widget") { print ' target="_blank"'; } ?>>
	<div class="teaser-date">
		<div class="teaser-date-box"></div>
		<div class="teaser-date-day"><?php print date_format_date($dateobj, "custom", "j"); ?> </div>
		<div class="teaser-date-month"><?php print date_format_date($dateobj, "custom", "M"); ?> </div>
		<?php
			$ts = date_format_date($dateobj, "custom", "U");
			print '<div class="teaser-date-nid"'. 			
						'style="display:none;" title="' . $ts . '">' . 
						date_format_date($dateobj, "custom", "Y-n-j") . '</div>';

		?>
	</div>
	<div class="teaser-info">
		<div class="event-title"><?php if($isprivate) { print "PRIVATE: "; } print $title; ?></div>
		<div class="content-left">
			<div class="event-type hide-for-semester"><?php print $node->field_event_taxonomy_type[0]['view']; ?></div>
			<div class="event-location"><?php print ($node->field_event_location[0]['view'] ? $node->field_event_location[0]['view'] . " " : "");
 ?></div>

			<div class="event-time hide-for-semester"><?php print date_format_date($dateobj, "custom", "g:ia"); ?> </div>

			<div class="event-people hide-for-semester hide-for-month">
			<?php  $counter = 0; $max = count($node->field_event_people); 
				//hacky thing to display commas
				foreach($node->field_event_people as $person) { $counter++; ?>
				<div class="event-person"><?php print $person['view']; if($counter < $max) print ", "; ?></div>
			<?php } ?>
			</div>

		</div>
	
	</div> <!-- /info-content -->
	</a>
	<!-- this was in if($_GET['q'] != "widget"), Troy moved b/c now we want images again -->
	<div class="teaser-image" id="teaser-image-<?php print $nid; ?>">
		<?php 
		if($node->field_event_hover_image[0]['view']) {
			print $node->field_event_hover_image[0]['view']; 
		} else {
			print $node->field_event_poster[0]['view'];
		} ?>
	</div>

<?php 
// don't load images if we're in the widget -- why waste bandwidth? stopping image loading is harder at a js/css level..
if($_GET['q'] != "widget") { ?>
	
<?php } ?>

  </div> <!-- /content -->


<?php /* ************************************************ */ ?>
<?php /* ************************************************ */ ?>
<?php /* ************************************************ */ ?>
<?php /* ************************************************ */ ?>
<?php /* ************************************************ */ ?>

<?php } else { 
?>


  <div class="content">
	<?php 
	
	$first_poster_image_path = null;
	
	if($node->field_event_imagegallery[0]['view'] || $node->field_event_poster[0]['view'] ||
	$node->field_event_presentation[0]['view'] || 
	$node->field_event_scribd[0]['safe'] ||
	$node->field_event_emvideo[0]['view']) { 
	
	?>
	<div class="section image-section">
					<div id="imagegallery-lightbox">
						<?php foreach($node->field_event_imagegallery as $image) { ?>
							<a class="colorbox-imagegallery" href="<?php print $base_url . "/" . imagecache_create_path('event_lightbox_poster_view', $image['filepath']); ?>"></a>
						<?php } ?>
					</div>
					<div id="poster-lightbox">
						<?php foreach($node->field_event_poster as $image) { 
							if ($first_poster_image_path == null) {
								$first_poster_image_path = $image['filepath'];
							}
						?>
							<a class="colorbox-poster" href="<?php print $base_url . "/" . imagecache_create_path('event_lightbox_poster_view', $image['filepath']); ?>"></a>
						<?php } ?>
					</div>
		<div class="content-left">
			<div id="slideshow-area">

				<!-- Image Gallery -->
				<?php if($node->field_event_imagegallery[0]['view']) { ?>
				<div id="imagegallery" class="item">
					<div class="slider-wrapper theme-default">
					<div id="ribbon"></div>
					<div id="imagegallery-slider" class="nivoSlider">
						<?php foreach($node->field_event_imagegallery as $image) { ?>
							<?php print $image['view']; ?>
						<?php } ?>
					</div>
					</div>
				</div>
				<?php } ?>
				<!-- /Image Gallery -->


				<!-- Poster -->
				<?php if($node->field_event_poster[0]['view']) { ?>
				<div id="poster" class="item">
					<div class="slider-wrapper theme-default">
					<div id="ribbon"></div>
					<div id="poster-slider" class="nivoSlider">
						<?php foreach($node->field_event_poster as $imagep) { ?>
							<?php print $imagep['view']; ?>
						<?php } ?>
					</div>
					</div>
				</div>
				<?php } ?>
				<!-- /Poster -->

				<!-- Presentation -->
				<?php 
				if (strlen($node->field_event_scribd[0]['safe']) > 10) {
					// show scribd
				?>
					<div id="presentation" class="item">
						<div id="scribd-embed">
							<?php print $node->field_event_scribd[0]['safe']; ?>
						</div>
					</div>
				<?php } else {
					// show images
					if($node->field_event_presentation[0]['view']) { 
				?>
					<div id="presentation" class="item">
						<div class="slider-wrapper theme-default">
						<div id="ribbon"></div>
						<div id="presentation-slider" class="nivoSlider">
							<?php foreach($node->field_event_presentation as $image) { ?>
								<?php print $image['view']; ?>
								<?php //print $image['data']['description']; ?>
							<?php } ?>
						</div> 
						</div>
					</div>
				<?php 
					}
				}
				?>
				<!-- /Presentation -->


				<!-- 120322 FLICKR GALLERY -->
				<?php
				/* PHOTOSET EXISTS???? */
				if($node->field_event_flickr[0]['value']) {
					
					$flickr_params = array(
						'api_key'	=> 'd14d721b7f03bdd96889e1a7567e51d6',
						'method'	=> 'flickr.photosets.getPhotos',
						'format'	=> 'php_serial',
						'extras'	=> 'url_o, url_m',
						'photoset_id'	=> trim($node->field_event_flickr[0]['value']),
					);
					
					$flickr_encoded_params = array();
					foreach ($flickr_params as $k => $v){
						$flickr_encoded_params[] = urlencode($k).'='.urlencode($v);
					}
					$flickr_url = "http://api.flickr.com/services/rest/?".implode('&', $flickr_encoded_params);
					$flickr_rsp = file_get_contents($flickr_url);
					$flickr_rsp_obj = unserialize($flickr_rsp);

					if ($flickr_rsp_obj['stat'] == 'ok'){

						/* CALL WORKED AND IMAGES EXIST */
					?>


					<div id="flickr" class="item">
						<div class="slider-wrapper theme-default">
						<div id="ribbon"></div>
						<div id="flickr-slider" class="nivoSlider">



						<?php foreach($flickr_rsp_obj['photoset']['photo'] as $key => $flickr_photo) {

// pix is 430x323
// resize to height of 323
// so get original dimensions, calculate required width, use sencha to resize.
$reqwidth = ceil($flickr_photo['width_o'] / ($flickr_photo['height_o'] / 323));

							print "<img class='event_flickr_image' src='http://src.sencha.io/" . $reqwidth . "/" . $flickr_photo['url_o'] . "'>\n";
						}?>

						</div> 
						</div>
					</div>

					<?php

					}
				}
				?>
				<!-- FLICKR GALLERY END -->




			</div>
		<?php if($node->field_event_imagegallery[0]['view'] || $node->field_event_poster[0]['view'] || $node->field_event_presentation[0]['view']) { ?>
			<div id="slideshow-nav">
				<?php if($node->field_event_poster[0]['view']) { ?>
					<div class="elem selected" name="poster">Poster</div>
				<?php } ?>
				<?php if($node->field_event_imagegallery[0]['view']) { ?>
					<div class="elem" name="imagegallery">Image Gallery</div>
				<?php } ?>
				<?php if($node->field_event_presentation[0]['view']) { ?>
					<div class="elem" name="presentation">Presentation</div>
				<?php } ?>
				<?php if($node->field_event_flickr[0]['value']) { ?>
					<div class="elem" name="flickr">Flickr</div>
				<?php } ?>
				<div id="expand-poster">
				<?php
					$first = null;
					foreach($node->field_event_poster as $image) {
						if ($first == null) {
							print '<a class="thickbox t-poster1" id="tbox-expand" rel="g1" href="/' .
							$image['filepath'] . '" title="Larger image"><img src="/sites/all/themes/events/images/makebig.png" width="14" height="14" /></a>';
							$first = 1;
						} else {
							print '<a class="thickbox t-poster" id="tbox-expand" rel="g1" href="/' .
							$image['filepath'] . '" title="Larger image" style="display: none;">&nbsp;</a>';
						}
					}
					print '</div><!-- close expand-poster-->';
					print '<div id="expand-imagegallery">';
					$first = null;
					foreach($node->field_event_imagegallery as $image) {
						if ($first == null) {
							print '<a class="thickbox t-image1" id="tbox-expand" rel="g2" href="/' .
							$image['filepath'] . '" title="Larger image"><img src="/sites/all/themes/events/images/makebig.png" width="14" height="14" /></a>';
							$first = 1;
						} else {
							print '<a class="thickbox t-image" id="tbox-expand" rel="g2" href="/' .
							$image['filepath'] . '" title="Larger image" style="display: none;">&nbsp;</a>';
						}
					}
				
				?>
				</div>
			</div>
		<?php } ?>
		</div>
		<div class="content-right">
			<div class="video">
				<?php if($node->field_event_video_preview[0]['view']) { ?>
				<div class="overlay">
					<div class="button"><img src="/<?php print path_to_theme(); ?>/images/video-play-button.png"></div>
					<div class="image">
						<?php print $node->field_event_video_preview[0]['view']; ?></div>
				</div>
				<?php } else if($node->field_event_emvideo[0]['provider'] == "youtube") { ?>
				<div class="overlay">
					<div class="button"><img src="/<?php print path_to_theme(); ?>/images/video-play-button.png"></div>
					<div class="image">
						<img src="http://img.youtube.com/vi/<?php print $node->field_event_emvideo[0]['value']; ?>/0.jpg" width="430" height="323"></div>
				</div>
				<?php } else if($node->field_event_emvideo[0]['provider'] == "vimeo") { 
				print '<iframe src="http://player.vimeo.com/video/' . 			
					$node->field_event_emvideo[0]['value'] . '?title=0&amp;byline=0&amp;portrait=0" width="430" height="323" frameborder="0"></iframe>';
				} ?>
				<div class="content <?php if($node->field_event_video_preview[0]['view']) print "hidedefault"; ?>">
					<?php 
						if($node->field_event_emvideo[0]['provider'] == "youtube") {
						// using YT JS Player API: http://code.google.com/apis/youtube/js_api_reference.html
						print '<div id="ytapiplayer-div" class="' . $node->field_event_emvideo[0]['value'] . '"></div>';
						}
					?>
				</div>
			</div>
		</div>
	</div> <!-- /image-content -->
<?php }  // ending monster check for various image-based conditions ?>

	<div class="section info-section">
		<div class="event-title"><?php print $title; ?></div>
		<div class="content-left">
			<div class="event-info">
				<div class="event-type"><?php print $node->field_event_taxonomy_type[0]['view']; ?></div>
				<div class="event-time"><?php print date_format_date($dateobj, "custom", "l, F j, Y g:ia"); ?> </div>
				<div class="event-location"><?php print $node->field_event_location[0]['view']; ?></div>
			</div>
			<div class="event-social">
				 <div class="share dropdown">
					<div><a class="header" href="#"><span class="arrow"><img src="/<?php print path_to_theme(); ?>/images/arrow_white_down.png"></span>Share</a>
						<div class="sub_menu">
							<span><a href="http://www.addthis.com/bookmark.php" style="text-decoration:none;" class="addthis addthis_button_email">Email</a></span>
							<span><a href="http://www.addthis.com/bookmark.php" style="text-decoration:none;" class="addthis addthis_button_facebook">Facebook</a></span>
							<span><a href="http://www.addthis.com/bookmark.php" style="text-decoration:none;" class="addthis addthis_button_twitter">Twitter</a></span>
						</div>
					</div>
				</div>
				<div class="follow dropdown">
					<div><a class="header" href="#"><span class="arrow"><img src="/<?php print path_to_theme(); ?>/images/arrow_white_down.png"></span>Follow</a>
						<div class="sub_menu">
							 <span><a target="_new" href="http://ccgsapp.org/follow-cc">Email</a></span>
							 <span><a target="_new" href="<?php print $base_url . "/rss.xml"; ?>">RSS</a></span>
							 <span><a target="_new" href="http://www.facebook.com/gsapp1881">Facebook</a></span>
							 <span><a target="_new" href="http://twitter.com/#!/gsapponline">Twitter</a></span>
							 <span><a target="_new" href="http://www.youtube.com/user/ColumbiaGSAPP">Youtube</a></span>
							 <span><a target="_new" href="http://itunes.apple.com/WebObjects/MZStore.woa/wa/viewPodcast?id=499345704">iTunes U</a></span>
							 <span><a target="_new" href="http://www.livestream.com/gsapp">Livestream</a></span>
							 <span><a target="_new" href="http://www.ccgsapp.org">CC:GSAPP</a></span>
						</div>
					</div>
				</div> 
				<div class="gcal dropdown">
					<?php 
					$nodebody = substr($node->content['body']['#value'], 0, 2000);
					$gcalcode = "http://www.google.com/calendar/event?action=TEMPLATE&text= " . htmlentities($title) . "&dates=" . 
						date_format_date($dateutcobj, "custom", "Ymd\THis\Z") . "/" . date_format_date($dateutcobj, "custom", "Ymd\THis\Z") . "&details=" . 
						htmlentities(strip_tags(preg_replace('/<\/p>|<br\\\\s*?\\/??>/i', "%0A%0A", $nodebody))) . " ..." . "&location=" . 
						htmlentities($node->field_event_location[0]['view']) . "&trp=false&sprop=&sprop=name:";
					?>
					<div><a class="header-img" href="<?php print $gcalcode; ?>" target="_new"><img src="/<?php print path_to_theme(); ?>/images/addtocal.png"></a>
						<div class="sub_menu">
							 <span><a target="_new" href="<?php print $gcalcode; ?>">Add to Cal</a></span>
						</div>
					</div>
	
				</div>
			</div>
			<?php /* if(count($node->field_event_people) > 1) { ?>
				<div class="event-people">
				<?php  $counter = 0; $max = count($node->field_event_people); 
					//hacky thing to display commas
					foreach($node->field_event_people as $person) { $counter++; ?>
					<div class="event-person"><?php print $person['view']; if($counter < $max) print ", "; ?></div>
				<?php } ?>
				</div>
			<?php } */ ?>
			<div class="event-subtitle"><?php print $node->field_event_subtitle[0]['view']; ?></div>
			<div class="event-description"><?php print $node->content['body']['#value']; ?></div>
			<div class="event-imagecredits"><?php print $node->field_event_imagecredits[0]['view']; ?></div>


		</div>
		<div class="content-right">
			<?php $resultstr = array();
			foreach($node->field_event_hashtag as $hashtag) { 
				$resultstr[] = $hashtag['view']; 
			} 
			$hashtagqueries = implode(" OR ", $resultstr);
			$hashtagtitle = implode(", ", $resultstr);
			?>
			<?php if($node->field_event_hashtag[0]['view']) { 
			$hashtag = $node->field_event_hashtag[0]['view']; ?>
			<div id="event-tweets">
				<div class="event-twitter-header">
					<div class="label">Twitter:</div>
					<div class="event-hashtags">
					<?php foreach($node->field_event_hashtag as $hashtag) { ?>
						<div class="event-hashtag"><?php print $hashtag['view']; ?></div>
					<?php } ?>
					</div>
				</div>
				<div id="event-twitter-container">
					<div id="event-twitter-embed">
						<script src="http://widgets.twimg.com/j/2/widget.js"></script>
						<script>
						new TWTR.Widget({
						  version: 2,
						  type: 'search',
						  search: '<?php print $hashtagqueries; ?>',
						  interval: 30000,
						  title: 'Tweets with <?php print $hashtagtitle; ?>',
						  subject: '',
						  width: 400,
						  height: 150,
						  theme: {
						    shell: {
						      background: '#ffffff',
						      color: '#ffffff'
						    },
						    tweets: {
						      background: '#ffffff',
						      color: '#444444',
						      links: '#1985b5'
						    }
						  },
						  features: {
						    scrollbar: true,
						    loop: false,
						    live: true,
						    behavior: 'all'
						  }
						}).render().start();
						</script>
					</div>
					<div id="event-twitter-control">Expand</div>
				</div>
			</div>
			<?php } ?>
			<div id="event-comments">
				<div class="label">Comment:</div>
				<div class="comment-box">
					<?php if (!empty($node -> disqus_comments)) : 
						print $node -> disqus_comments; 
					  endif; ?>
					<?php /* <div class="fb-comments" data-href="<?php print $node_url; ?>" data-width="430"></div> */ ?>
				</div>
			</div>	
		</div>
	
	</div> <!-- /info-content -->

<!-- AddThis Button BEGIN -->
<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=ra-4f2088e20e765fc7"></script>
<!-- AddThis Button END -->


<?php //print_r($node); ?>
  </div> <!-- /content -->

<?php } ?>

</div><!-- /.node -->

<!-- added a comment -->

<div id="log"></div>
