<?php if(!empty($cellDataArr) && is_array($cellDataArr)): ?>
	<?php if(!empty($feed_type) && $feed_type == 'social'): ?>
		<!-- Begin, Twitter Feeds -->
		<div title="Twitter Feeds" class="feed_text_size">
			<div class="slider">
			    <ul class="slides white">
					<?php if(!empty($social_type) && $social_type == 'twitter'): ?>
						<?php foreach ($cellDataArr as $cellData): ?>
							<li>
								<span>
									<?= $cellData['feed'] ?> -- <?= $cellData['feeder_name'] ?>
								</span>
								<br />
								<span>Total Like(s): <span class="like_cnt" id="like_cnt_<?= getEncyptionValue($cellData['id']) ?>"><?= $cellData['likes_count'] ?></span></span>
							
							</li>
						<?php endforeach; ?>
					<?php endif; ?>
				</ul>
			</div>
		</div>
	<!-- End, Twitter Feeds -->
	<?php endif; ?>

	<!-- Begin, Admin Feed -->
	<?php if(!empty($feed_type) && $feed_type == 'admin_text'): ?>
		<div  title="Admin Feed" class="white admin_text custom_text_size admin_text_<?= $cellNumber ?>" id="admin_text_<?= $cellNumber ?>">
			<?php if(!empty($cellDataArr[0])): ?>
				<q><?= $cellDataArr[0]['feed'] ?></q>
			<?php endif; ?>
		</div>
	<?php endif; ?>
	<!-- End, Admin Feed -->

	<!-- Begin, Admin Image -->
	<?php if(!empty($feed_type) && $feed_type == 'admin_image'): ?>
		<div title="Admin Image" class="admin_text admin_text_<?= $cellNumber ?> img-rational-view-container" id="admin_text_<?= $cellNumber ?>">
			<?php if(!empty($cellDataArr[0]) && !empty($cellDataArr[0]['feed'])): ?>
			<img src="<?= SITE_URL.$cellDataArr[0]['feed'] ?>" id="img_admin_image_<?= getEncyptionValue($cellDataArr[0]['id']) ?>" class="img_admin_image responsive-img img-rational-view" />
			<?php endif; ?>
		</div>
	<?php endif; ?>
	<!-- End, Admin Image -->

	<!-- Begin, User Feed -->
	<?php if(!empty($feed_type) && $feed_type == 'user_feed'): ?>

		<div title="User Feeds" class="feed_text_size user_feed user_feed_<?= $cellNumber ?>" id="user_feed_<?= $cellNumber ?>">
			<div class="slider">
			    <ul class="slides white">
				<?php foreach ($cellDataArr as $cellData): ?>
					<li>
							<span>
								<?= $cellData['feed'] ?> -- <?= $cellData['feeder_name'] ?>
							</span>
							<br />
							<span>Total Like(s): <span class="like_cnt" id="like_cnt_<?= getEncyptionValue($cellData['id']) ?>"><?= $cellData['likes_count'] ?></span></span>
						
					</li>
				<?php endforeach; ?>
				</ul>
			</div>
		</div>

	<?php endif; ?>
	<!-- End, User Feed -->

<?php endif; ?>

<?php if(!empty($cellNumber) && !empty($cellDataArr[0]['refresh_timeout'])): ?>
	<script type="text/javascript">
		setTimeoutForCell(<?= $cellNumber ?>, <?= $cellDataArr[0]['refresh_timeout'] ?>);
	</script>
<?php endif; ?>