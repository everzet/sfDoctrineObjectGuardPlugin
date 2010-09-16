<?php use_helper('I18N') ?>

<?php echo __('Go to the following link to proceed with password change:') ?>
<?php echo link_to('activate', '@sf_object_guard_activate?activation_key=' . $key, 'absolute=true') ?>

<?php echo __('Your email:') ?> "<?php echo $user->getEmail() ?>"
<?php echo __('Your new password:') ?> "<?php echo $password ?>"
