<?php use_helper('I18N') ?>

<div class="form">
  <h1><?php echo __('Enter to site') ?></h1>

  <?php include_partial('sfObjectGuardAuth/flash') ?>
  <?php include_partial('loginForm', array('form' => $form)) ?>
</div>
