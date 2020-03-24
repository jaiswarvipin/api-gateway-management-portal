<html>

<head>
  <?php 
    /* Variable initialization */
    $strFileName  = '';
    /* if exiting environment is not development then do needful */
//    if(ENVIRONMENT != 'development'){
//      $strFileName  = '.mini';
//    }

  ?>
  <title><?php echo (isset($moduleTitle)? strip_tags($moduleTitle):'');?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
  <link rel="stylesheet" type="text/css" href="<?php echo SITE_URL.RESOURCE_CSS_PATH?>materialize<?php echo $strFileName?>.css" />
  <link rel="stylesheet" type="text/css" href="<?php echo SITE_URL.RESOURCE_CSS_PATH?>jquery.fancybox<?php echo $strFileName?>.css" />
  <link rel="stylesheet" type="text/css" href="<?php echo SITE_URL.RESOURCE_CSS_PATH?>style<?php echo $strFileName?>.css?v=1.0.0.0.1" />
  <style>
    body {
		display: flex;
		min-height: 100vh !important;
		flex-direction: column;
	    background: #fff;
    }

    main {
      flex: 1 0 auto;
    }
	
    .input-field input[type=date]:focus + label,
    .input-field input[type=text]:focus + label,
    .input-field input[type=email]:focus + label,
    .input-field input[type=password]:focus + label {
      color: #e91e63;
    }

    .input-field input[type=date]:focus,
    .input-field input[type=text]:focus,
    .input-field input[type=email]:focus,
    .input-field input[type=password]:focus {
      border-bottom: 2px solid #e91e63;
      box-shadow: none;
    }

    .input-field input[type=checkbox] + label, .input-field input[type=radio] + label {
      pointer-events: auto;
    }

  </style>
  <script language="javaScript">
  <!--
    var SITE_URL  = "<?php echo SITE_URL;?>";
	var DELIMITER  = "<?php echo DELIMITER;?>";
  -->
  </script>
</head>

<body <?php if(strstr($_ci_view,'default')){?>style="background-color: #f6f6f6;"<?php }?>>
