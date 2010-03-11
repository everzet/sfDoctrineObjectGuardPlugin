<?php

use_helper('I18N');

echo __('Password recovery for %1%.', array('%1%' => sfConfig::get('app_site_name', 'site')));
