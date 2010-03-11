<?php use_helper('I18N') ?>

<div class="form">
  <h1><?php echo __('Registration') ?></h1>

  <?php include_partial('sfObjectGuardAuth/flash') ?>
  <?php include_partial('registerForm', array('form' => $form)) ?>
</div>
