<?php

use_helper('I18N');

echo __('Account activation for %1%.', array(
  '%1%' => sfConfig::get('app_site_name', 'site')
));
