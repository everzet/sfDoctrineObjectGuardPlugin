<?php use_helper('I18N') ?>

<?php echo __('Go to the following link to finish registration:') ?>
<?php echo link_to('activate', '@sf_object_guard_activate?activation_key=' . $key, 'absolute=true') ?>

<?php echo __('Your email:') ?> "<?php echo $email ?>"
<?php echo __('Your temporary password:') ?> "<?php echo $password ?>"