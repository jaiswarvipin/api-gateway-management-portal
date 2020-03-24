<?php include_once('header.php'); ?>
<?php if(isset($blnFrontRequest)){?>
	<nav class="show-on-large hide-on-med-and-down">
		<div class="nav-wrapper blue lighten-2">
			<a href="javascript:void(0);" class="brand-logo"><img src="<?php echo SITE_URL.DEFAULT_LOGO?>" class="responsive-img logo-container"/></a>
			<a href="javascript:void(0);" class="brand-logo center"><span class="fEventUserFeedEventName"><?php echo $strEventName?></span></a>
			<a href="javascript:void(0);" class="brand-logo right" style="margin-top: -5px !important;">|&nbsp;&nbsp;<span class="fEventUserFeedEventCode" id="time"><?php echo strtoupper($strEventCode)?></span></a>
		</div>
	</nav>
	<div class="row show-on-medium-and-down hide-on-med-and-up blue lighten-2">
		<div class="col s12 l12 m12 aling-wrapper center-align"><img src="<?php echo SITE_URL.DEFAULT_LOGO?>" class="responsive-img logo-container"/></div>
		<div class="col aling-wrapper center-align s12 l12 m12"><span class="fEventUserFeedEventName"><?php echo $strEventName?></span></div>
	</div>
<?php }?>
<div class="<?php if((isset($blnDevice)) && ($blnDevice)){ echo "container";}else{ echo "main-container";};?>">
	<?php echo $body; ?>
</div>
<?php include_once('footer.php');?>