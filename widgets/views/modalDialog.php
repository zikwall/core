<!-- Dialog -->
<div class="<?= $dialogClass ?>">
    <!-- Content -->
    <div class="modal-content">
        <!-- Header -->
        <?php if ($header !== null || $showClose): ?>
            <div class="modal-header">
                <?php if ($showClose): ?>
                    <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                <?php endif; ?>
                <?php if ($header !== null): ?>
                    <h4 class="modal-title"><?= $header ?></h4>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($dialogContent) : ?>
            <?= $dialogContent ?>
        <?php else : ?>
            <!-- Body -->
            <div class="<?= $bodyClass ?>">
                <?php if ($body !== null): ?>
                    <?= $body ?>
                <?php endif; ?>
                <?php if ($initialLoader): ?>
                    <?php echo \zikwall\encore\modules\core\widgets\LoaderWidget::widget(); ?>
                <?php endif; ?>
            </div>

            <!-- Footer -->
            <?php if ($footer !== null): ?>
                <div class="modal-footer">
                    <?= $footer ?> 
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
