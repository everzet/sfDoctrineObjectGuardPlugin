<?php

/*
 * This file is part of the sfDoctrineObjectGuardPlugin.
 * (c) 2010 Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfTesterMailerExt does things.
 *
 * @package    sfDoctrineObjectGuardPlugin
 * @subpackage tests
 * @author     Konstantin Kudryashov <ever.zet@gmail.com>
 * @version    1.0.0
 */
class sfTesterMailerExt extends sfTesterMailer
{
  public function regexpBody($regexp, &$content)
  {
    $body = $this->message->getBody();
    $content = preg_match($regexp, $body, $match) ? $match : null;

    return $this;
  }
}