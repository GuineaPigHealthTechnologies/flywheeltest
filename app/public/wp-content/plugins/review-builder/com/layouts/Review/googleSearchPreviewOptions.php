<?php if (!SGRB_PRO_VERSION):?>
	<div class="container-fluid">
<div class="sgrb-pro-options-wrapper">
	<div class="sgrb-coming-soon">
		<a class="sgrb-pull-right" target="_blank" href="<?php echo SGRB_PRO_URL ;?>"><img src="<?php echo $sgrb->app_url.'assets/page/img/long-ribbon.png'; ?>" width="400px"></a>
	</div>
<?php endif;?>
	<div class="row<?php echo (!SGRB_PRO_VERSION) ? ' sgrb-disable': '';?>">
		<div class="col-md-1 col-md-offset-1">
			<div class="sgrb-checkbox-wrapper">
				<input id="sgrb-google-on" class="sgrb-google-search-checkbox" type="checkbox" value="true" name="sgrb-google-search-on"<?php echo (@$sgrbDataArray['sgrb-google-search-on']) ? ' checked' : '';?>>
				<label class="sgrb-checkbox-label" for="sgrb-google-on"></label>
			</div>
		</div>
		<div class="col-md-5">
			<label for="sgrb-google-on">
				<?php _e('Show Your review in Google search', 'sgrb');?>
			</label>
		</div>
	</div>

	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="sgrb-google-search-preview" <?php echo (!SGRB_PRO_VERSION) ? ' style="opacity:0.5;"': '';?>>
				<div class="sgrb-google-box-wrapper">
					<div class="sgrb-google-box-title"><?php _e('Your review title', 'sgrb');?></div>
					<div class="sgrb-google-box-url">www.your-web-page.com/your-review-site/...</div>
					<div class="sgrb-google-box-image-votes"><img width="70px" height="20px" src="<?php echo $sgrb->app_url.'assets/page/img/google_search_preview.png';?>"><span>Rating - 5 - 305 votes</span></div>
					<div class="sgrb-google-box-description"><span><?php _e('Your description text, if description field in Your selected template not exist, then there will be another field\'s text, e.g. title,subtitle', 'sgrb');?> ...</span></div>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<p><?php _e('
				Search Engine Optimization (SEO) is the process of making a website more visible in search engine results pages.
				The process itself has a variety of strategies and techniques that aim to improve search engine rankings,
				and extremely increase the traffic to your website.
				There are a number of methods to improve a certain web page for search engines', 'sgrb');?>.
			</p>
			<p><?php _e('
				Google makes up around 75% of search engine queries.
				It is one of the most important search engines to pursue SEO.
				Understanding the bases of Google SEO can help you increase organic traffic to your website.
				And while Google’s underlying system for ranking websites may seem like the rest,
				there are some key differences with Google that set it apart from other search engines', 'sgrb');?>.
			</p>
			<p><?php _e('
				Search engine optimization often refers to making small modifications in some parts of your website.
				Though when viewed individually, these changes may seem to be incremental improvements,
				but when you combine them with other optimizations,
				they can have a remarkable impact on your site\'s user experience and performance in organic search results', 'sgrb');?>.
			</p>
		</div>
	</div>
<?php if (!SGRB_PRO_VERSION):?>
</div>
</div>
<?php endif;?>
