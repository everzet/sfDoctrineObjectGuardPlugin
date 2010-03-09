<?php echo __('Go to the following link to proceed with registration:') ?>

<?php echo url_for('@sf_object_guard_activate?activation_key=' . $key, 'absolute=true') ?>