		<div id="headerUserNav" class="textRight">
<?php
	// Only show Add Project and Admin is user has level 1 or 2 permissions
	if ($user_perm != 3) {
?>
			<a href="<?php echo DOC_ROOT; ?>project" class="font10 greyLink greyBg tahoma bold">ADD PROJECT</a>
      <a href="<?php echo DOC_ROOT; ?>admin" class="font10 greyLink greyBg tahoma bold">ADMIN</a>
<?php
	}
?>
      <a href="<?php echo DOC_ROOT; ?>profile" class="font10 greyLink greyBg tahoma bold">EDIT PROFILE</a>
      <a href="<?php echo DOC_ROOT; ?>logout" class="font10 greyLink greyBg tahoma bold">LOGOUT</a>
    </div>