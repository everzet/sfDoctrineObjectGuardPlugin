<div class="form">
  <h1><?php echo __('Password recovery') ?></h1>

  <?php include_partial('sfObjectGuardAuth/flash') ?>
  <?php include_partial('remindForm', array('form' => $form)) ?>
</div>
