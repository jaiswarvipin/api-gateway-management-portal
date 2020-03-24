	<!--footer class="page-footer fix blue-grey darken-4" style="font-size:12px !important;padding:0px !important">
		<div class="footer-copyright">
			<div class="container">&copy;<?php echo date('Y')?>  Copyright Text
				<a class="grey-text text-lighten-4 right" href="#!">More Links</a>
            </div>
		</div>
	</footer-->
	<script type="text/javascript" src="<?php echo SITE_URL.RESOURCE_SCRIPT_PATH?>jquery-3.2.1.js"></script>
    <script type="text/javascript" src="<?php echo SITE_URL.RESOURCE_SCRIPT_PATH?>jquery-ui.js"></script>
	<script type="text/javascript" src="<?php echo SITE_URL.RESOURCE_SCRIPT_PATH?>jquery.fancybox<?php echo $strFileName?>.js"></script>
  	<script type="text/javascript" src="<?php echo SITE_URL.RESOURCE_SCRIPT_PATH?>materialize.min.js?v=1"></script>
  	<script type="text/javascript" src="<?php echo SITE_URL.RESOURCE_SCRIPT_PATH?>jquery.validate<?php echo $strFileName?>.js"></script>
  	<script type="text/javascript" src="<?php echo SITE_URL.RESOURCE_SCRIPT_PATH?>default<?php echo $strFileName?>.js?v=1.0.0.0.5"></script>
	<?php if((strstr($_ci_view,'fullwidth')) || (strstr($_ci_view,'dashboard')) || ((isset($strSource) && ($strSource == 'cms')))){?>
		<script type="text/javascript" src="<?php echo SITE_URL.RESOURCE_SCRIPT_PATH?>function<?php echo $strFileName?>.js?v=1.0.0.0.2"></script>
	<?php }else{?>
		<script type="text/javascript" src="<?php echo SITE_URL.RESOURCE_SCRIPT_PATH?>script<?php echo $strFileName?>.js?v=1.0.0.0.1"></script>
	<?php }?>
  	<?php unset($strFileName);?>
</body>
</html>