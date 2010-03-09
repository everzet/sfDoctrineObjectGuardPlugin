<div class="form">
  <h1><?php echo __('Setting the password') ?>.</h1>

  <?php include_partial('sfObjectGuardAuth/flash') ?>
  <?php include_partial('passwordForm', array('form' => $form)) ?>
</div>
