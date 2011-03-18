		<div id="viewTabs" class="headerTabs">
			<a href="<?php echo DOC_ROOT . $regionName."?view=project";?>" id="viewByProject" class="spriteLink font12 greyLink tahoma <?php if ($view == "project") { ?>active<?php }?>">View by Project</a>
      <a href="<?php echo DOC_ROOT . $regionName."?view=resource";?>" id="viewByResource" class="spriteLink font12 greyLink tahoma <?php if ($view == "resource") { ?>active<?php }?>">View by Resource</a>
      <a href="" id="print" class="spriteLink font10 greyLink tahoma greyBg">PRINT</a>
      <a href="" id="export" class="spriteLink font10 greyLink tahoma greyBg">EXPORT</a>
    </div>