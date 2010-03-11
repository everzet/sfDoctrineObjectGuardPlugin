<?php

/*
 * This file is part of the sfDoctrineObjectGuardPlugin.
 * (c) 2010 Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfObjectGuardTranslit does things.
 *
 * @package    sfDoctrineObjectGuardPlugin
 * @subpackage I18N
 * @author     Konstantin Kudryashov <ever.zet@gmail.com>
 * @version    1.0.0
 */
class sfObjectGuardTranslit
{
  /**
   * Convert any passed string to a url friendly string. Converts 'My first blog post' to 'my-first-blog-post'
   *
   * @param  string $text  Text to urlize
   * @return string $text  Urlized text
   */
  public static function doctrine_urlize($text)
  {
    // Transliteration
    $text = self::transliterate($text);

    // Urlize
    return Doctrine_Inflector::urlize($text);
  }

  protected static function getReplaceTable()
  {
    return array(
      'а'=>'a', 'б'=>'b', 'в'=>'v', 'г'=>'g', 'д'=>'d', 'е'=>'e', 'ж'=>'g', 'з'=>'z',
      'и'=>'i', 'й'=>'y', 'к'=>'k', 'л'=>'l', 'м'=>'m', 'н'=>'n', 'о'=>'o', 'п'=>'p',
      'р'=>'r', 'с'=>'s', 'т'=>'t', 'у'=>'u', 'ф'=>'f', 'ы'=>'i', 'э'=>'e', 'А'=>'A',
      'Б'=>'B', 'В'=>'V', 'Г'=>'G', 'Д'=>'D', 'Е'=>'E', 'Ж'=>'G', 'З'=>'Z', 'И'=>'I',
      'Й'=>'Y', 'К'=>'K', 'Л'=>'L', 'М'=>'M', 'Н'=>'N', 'О'=>'O', 'П'=>'P', 'Р'=>'R',
      'С'=>'S', 'Т'=>'T', 'У'=>'U', 'Ф'=>'F', 'Ы'=>'I', 'Э'=>'E', 'ё'=>"yo", 'х'=>"h",
      'ц'=>"ts", 'ч'=>"ch", 'ш'=>"sh", 'щ'=>"shch", 'ъ'=>"", 'ь'=>"", 'ю'=>"yu", 'я'=>"ya",
      'Ё'=>"YO", 'Х'=>"H", 'Ц'=>"TS", 'Ч'=>"CH", 'Ш'=>"SH", 'Щ'=>"SHCH", 'Ъ'=>"", 'Ь'=>"",
      'Ю'=>"YU", 'Я'=>"YA"
    );
  }

  /**
   * Transliterating russian string
   *
   * @param string $text Text to transliterate
   * @return string Transliterated test
   */
  public static function transliterate($text)
  {
    return strtr($text, self::getReplaceTable());
  }
}
