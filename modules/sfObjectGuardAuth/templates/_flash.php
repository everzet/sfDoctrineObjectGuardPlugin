<p>
  <?php if ($sf_user->hasFlash('notice')): ?>
    <?php echo $sf_user->getFlash('notice') ?>
  <?php endif; ?>
</p>