<?php //debugVar($dataSet,true); ?>
<?php include_once('header.php'); ?>
	<div class="had-container">
		<div class="rows">
			<div class="col s12">
				<div class="col s10">
					<!-- Dropdown Structure -->
					<?php if(!$blnDevice){echo $strMobileMenu;} ?>
					<nav class="hide-on-med-and-down-1">
			    		<div class="nav-wrapper blue-grey darken-4">
							<?php echo $strChildMenu ?>
			      			<a href="javascript:void(0);" class="brand-logo"><img src="<?php echo SITE_URL.DEFAULT_MENU_LOG?>" class="responsive-img logo-container"/></a>
							<a href="javascript:void(0);" data-target="mobile" class="button-collapse  hide-on-med-and-up"><i class="material-icons">menu</i></a>
							<?php echo $strMainMenu?>
							<?php if($blnDevice){echo $strMobileMenu;}?>
							<!--ul id="userSettings" class="dropdown-content">
								<li><a href="<?php echo SITE_URL?>login/lougout">Logout</a></li>
							</ul-->
			  				<ul id="nav-mobile" class="right">
								<?php if($companyList != ''){?>
									<li><?php echo $companyList; ?></li>
								<?php }?>
			  					<li><a href="javascript:void(0);"><i class="material-icons">&nbsp;</i></a></li>
			  					<!--li><img src="<?php echo SITE_URL.DEFAULT_USER_IMG?>" class="responsive-img circle pt10 tooltipped user-log" width="50px" height="50px" data-position="bottom" data-delay="50" data-tooltip=""/</li-->
								<li><span><?php echo $userName.' ('.$roleName .') | '?></span></li>
								<li style="margin-left:-10px !important;"><a href="<?php echo SITE_URL?>login/lougout">Logout</a></li>
								<!--li><a href="javascript:void(0);" data-target='userSettings' class="dropdown-trigger top-context-menu"><i class="material-icons">more_vert</i></a></li-->
			  				</ul>
						</div>
			  		</nav>
			  	</div>
			  	<div class="col s1"></div>
			</div>
		</div>

		<div class="main-container">
			<div class="row">
				<div class="col s6"><h5><?php echo $moduleTitle;?></h5></div>
				<div class="col s6 right module-action">
					<?php if(!isset($noAction)) {?>
						<!-- Dropdown Trigger -->
						<a class='dropdown-trigger btn right w200 aActionContainer hide-on-med-and-down' href='javascript:void(0);' data-target='dropdown1'><i class="material-icons"></i>Action</a>
						<!--a href="javascript:void(0);" data-target='dropdown1' class="dropdown-trigger right module-context-menu hide-on-med-and-up"><i class="material-icons">more_vert</i></a-->
						<?php 
							$strAddNewItemLabel = ((isset($moduleCustomTitle))&&($moduleCustomTitle != '')?$moduleCustomTitle:$moduleTitle);
							$strAddNewItemLabel	= explode('>',$strAddNewItemLabel);
							$strAddNewItemLabel	= end($strAddNewItemLabel);
						?>
						<!-- Dropdown Structure -->
						<ul id='dropdown1' class='dropdown-content dlActionList'>
							<?php if(!isset($noSearchAdd)){?>
								<?php if(($moduleForm != 'frmLeadReportSearch') && ($moduleForm != 'frmTaskReportSearch')  && ($moduleForm != 'frmCompany')){?>
									<li><a class="addItemInModule" href="javascript:void(0);" onclick="openEditModel('<?php echo $strDataAddEditPanel?>','',2);"><i class="material-icons">add_circle</i>Add New <?php echo $strAddNewItemLabel?></a></li>
									<!--li class="divider"></li>
									<li><a class="downloadRecords" href="javascript:void(0);" onclick='openEditModel("import-data-container","",0);'><i class="material-icons">file_upload</i>Import Data</a></li>
									<li class="divider"></li>
									<li><a class="downloadRecords" href="javascript:void(0);" onclick='openEditModel("export-data-container","",0);'><i class="material-icons">file_download</i>Export Data</a></li-->
									<li class="divider"></li>
								<?php }?>
								<?php if($moduleForm != ''){?>
									<li><a class="searchItemInModule" href="javascript:void(0);" onclick="openEditModel('<?php echo $strDataAddEditPanel?>','',3);"><i class="material-icons">search</i>Search</a></li>
									<li class="divider"></li>
								<?php }?>
							<?php }?>
						</ul>
					<?php }?>
				</div>
			</div>
			<?php echo $body; ?>
			<?php echo getDeleteConfirmation($deleteUri);?>
			<?php echo getEditContentForm($getRecordByCodeUri);?>
			<?php echo getFormStrecture($moduleUri,'frmModuleSearch');?>
			<?php if(isset($strCustomUri)) { echo getFormStrecture($strCustomUri,'frmCustom');};?>
			<?php echo getFormStrecture('','frmDynamicEventDataSet');?>
			<?php echo getFormStrecture('','frmHookDataProcess');?>
			<span class="hide" name="txtSearchFilters" id="txtSearchFilters"><?php echo $strSearchArr?></span>
		</div>
	</div>
<?php include_once('footer.php');?>