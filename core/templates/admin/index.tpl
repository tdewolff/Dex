<h2><?php echo __('Admin panel'); ?></h2>

<h3><?php echo __('Visitors'); ?></h3>
<div id="load_stats" class="dex-api load-status">
	<div class="working"><i class="fa fa-cog fa-spin"></i></div>
	<div class="error"><i class="fa fa-times"></i></div>
</div>
<iframe class="stats-frame" src="/<?php echo $_['base_url']; ?>admin/auxiliary/stats/"></iframe>
<div id="referrals">
	<ul id="referral-urls" class="table">
		<li>
			<div><?php echo __('Referral'); ?></div>
			<div><?php echo __('Count'); ?></div>
		</li>
		<li id="load_referral_urls" class="dex-api load-status">
			<div class="working"><i class="fa fa-cog fa-spin"></i></div>
			<div class="error"><i class="fa fa-times"></i></div>
			<div class="empty"><?php echo __('empty'); ?></div>
		</li>
	</ul>
	<ul id="referral-keywords" class="table">
		<li>
			<div><?php echo __('Keyword'); ?></div>
			<div><?php echo __('Count'); ?></div>
		</li>
		<li id="load_referral_keywords" class="dex-api load-status">
			<div class="working"><i class="fa fa-cog fa-spin"></i></div>
			<div class="error"><i class="fa fa-times"></i></div>
			<div class="empty"><?php echo __('empty'); ?></div>
		</li>
	</ul>
</div>

<h3><?php echo __('Latest errors'); ?></h3>
<ul id="latest-logs" class="table">
	<li>
		<div><?php echo __('Date time'); ?></div>
		<div><?php echo __('Message'); ?></div>
	</li>
	<li id="load_logs" class="dex-api load-status">
		<div class="working"><i class="fa fa-cog fa-spin"></i></div>
		<div class="error"><i class="fa fa-times"></i></div>
		<div class="empty"><?php echo __('empty'); ?></div>
	</li>
</ul>

<a href="/<?php echo $_['base_url']; ?>admin/logs/" class="button indent view-logs"><i class="fa fa-list-alt"></i>&ensp;<?php echo __('View') . ' ' . $_['log_name']; ?></a>

<script id="log_item" type="text/x-dot-template">
	<li data-html="{{=it.html}}" class="{{=it.type.toLowerCase()}}">
		<div>{{=it.datetime}}</div>
		<div>{{=it.message}}</div>
	</li>
</script>

<script id="referral_url_item" type="text/x-dot-template">
	<li>
		<div>{{=it.url}}</div>
		<div>{{=it.n}}</div>
	</li>
</script>

<script id="referral_keyword_item" type="text/x-dot-template">
	<li>
		<div>{{=it.keyword}}</div>
		<div>{{=it.n}}</div>
	</li>
</script>

<script type="text/javascript">
	$(function () {
		apiLoadStatusWorking($('#load_stats'));
		$('.stats-frame').load(function () {
			$('.stats-frame').ready(function () {
				api('/' + base_url + 'api/core/stats/', {
					action: 'get_visits'
				}, function (data) {
					apiLoadStatusSuccess($('#load_stats'));
					$('.stats-frame').slideDown(100);
					$('.stats-frame')[0].contentWindow.drawStats(data['visits']);
				}, function () {
					apiLoadStatusError($('#load_stats'));
				});
			});
		});

		var referral_urls = $('#referral-urls');
		var referral_keywords = $('#referral-keywords');
		var referral_url_item = doT.template($('#referral_url_item').text());
		var referral_keyword_item = doT.template($('#referral_keyword_item').text());
		apiLoadStatusWorking($('#load_referral_urls'));
		apiLoadStatusWorking($('#load_referral_keywords'));
		api('/' + base_url + 'api/core/stats/', {
			action: 'get_referrals',
			limit: 10
		}, function (data) {
			console.log(data);
			if (!data['referrals']['urls'].length) {
				apiLoadStatusEmpty($('#load_referral_urls'));
				return;
			} else {
				apiLoadStatusSuccess($('#load_referral_urls'));

				var items = '';
				$.each(data['referrals']['urls'], function () {
					items += referral_url_item(this);
				});
				referral_urls.append(items);
			}

			if (!data['referrals']['keywords'].length) {
				apiLoadStatusEmpty($('#load_referral_keywords'));
				return;
			} else {
				apiLoadStatusSuccess($('#load_referral_keywords'));

				var items = '';
				$.each(data['referrals']['keywords'], function () {
					items += referral_keyword_item(this);
				});
				referral_keywords.append(items);
			}
		}, function () {
			apiLoadStatusError($('#load_referral_urls'));
			apiLoadStatusError($('#load_referral_keywords'));
		});

		var logs = $('#latest-logs');
		var log_item = doT.template($('#log_item').text());
		apiLoadStatusWorking($('#load_logs'));
		api('/' + base_url + 'api/core/logs/', {
			action: 'get',
			lines: 10,
			errors: true
		}, function (data) {
			if (!data['logs'].length) {
				apiLoadStatusEmpty($('#load_logs'));
				return;
			}

			apiLoadStatusSuccess($('#load_logs'));
			var items = '';
			$.each(data['logs'], function () {
				items += log_item(this);
			});
			logs.append(items);
		}, function () {
			apiLoadStatusError($('#load_logs'));
		});

		logs.on('click', 'li', function () {
			$.fancybox.open({
				content: $(this).attr('data-html'),
				beforeShow: function () {
					this.skin.addClass('dex-api-error');
				},
				overlay: {
					closeClick: true,
					locked: false
				}
			});
		});
	});
</script>