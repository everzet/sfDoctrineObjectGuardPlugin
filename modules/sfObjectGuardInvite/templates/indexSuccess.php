<?php use_helper('I18N') ?>

<div class="form">
  <h1><?php echo __('Invite user') ?></h1>

  <?php include_partial('sfObjectGuardAuth/flash') ?>
  <?php include_partial('inviteForm', array('form' => $form)) ?>
</div>
