<?php
$this->headScript()->appendFile(PUBLIC_PATH . "/js/jquery.dataTables.min.js");
$this->headScript()->appendFile(PUBLIC_PATH . "/js/dataTableComman.js");
$this->headScript()->appendFile(PUBLIC_PATH . "/js/back_end/bootbox.min.js");
$this->headScript()->appendFile(PUBLIC_PATH . "/js/jquery-ui-1.8.16.custom.min.js");
$this->headLink()->appendStylesheet(PUBLIC_PATH . "css/jquery-ui-1.8.16.custom.css");
$this->headScript()->appendFile(PUBLIC_PATH . "/js/back_end/shopExcelQueue.js");
?>
<?php echo $this->headTitle()->append('backend_shop Excel Information');?>
<div id="imbullbody-content" style="overflow: hidden;">
    <div id="codeAlertListdiv">
        <div class="wrap columns-2">
            <div class='wrap columns-2'>
                <?php if ($this->successMessage != '' || $this->errorMessage != '') { ?>
                <br>
                <div class ="mainpage-content-colorbox success">
                <?php if ($this->successMessage != '') { ?>
                    <span class="successserver"><?php echo $this->successMessage; ?></span>
                <?php } ?>
                <?php if ($this->errorMessage != '') { ?>
                    <span class="errorserver"><?php echo $this->errorMessage; ?></span>
                <?php } ?>
                </div>
                <?php } ?>
            </div>
            <h1 class="fl"><?php echo $this->translate('backend_Queued Shop Excel Import Data'); ?></h1> 
            <div class="clear"></div>
            <table  class="table table-bordered table-striped" id="shopExcelListTable" style="position:relative;">
                <thead>
                    <tr>
                        <th style="display:none;"><?php echo $this->translate('Id'); ?></th>
                        <th><?php echo $this->translate('backend_Created At'); ?></th>
                        <th><?php echo $this->translate('backend_Total Shops Count'); ?></th>
                        <th><?php echo $this->translate('backend_Uploaded By'); ?></th>
                        <th><?php echo $this->translate('backend_Delete'); ?></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>