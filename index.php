<?php

/**
 * Captcha for KIRBY3 Plugin
 *
 * @author Björn Fromme <mail@bjo3rn.com>
 * @author Markus Schatzl <marsch@mailbox.org>
 * @version 1.1.0
 */

load([
      'securimage' => 'securimage/securimage.php'
], __DIR__);

v::$validators['captcha'] = function($value, $namespace = 'captcha') {         
  $securimage = new Securimage();                                                
  if ($namespace !== 'captcha') {                                                
    $securimage->setNamespace($namespace);                                       
  }                                                                            
  return $securimage->check($value);                                             
}; 

Kirby::plugin('marsch/kirby3-captcha', [
  'routes' => function($kirby) { 
    return [
      [ 
        'pattern' => 'captcha(?:(:any))?',
        'action' => function () {

          $img = new Securimage();
	  
	  $img->case_sensitive = c::get('captcha.case_sensitive', true);
          $img->perturbation   = c::get('captcha.perturbation', .75);
	  $img->num_lines      = c::get('captcha.num_lines', 8);
          $img->charset        = c::get('captcha.charset', 'ABCDEFGHJKMNPQRSTVWXYZ');


          if (c::get('captcha.ttf_file')) {
            $img->ttf_file = c::get('captcha.ttf_file');
	  }
          if (c::get('captcha.height')) {
            $img->image_height = c::get('captcha.height');
	  }
	  if (c::get('captcha.width')) {
            $img->image_width = c::get('captcha.width');
	  }
	  if (c::get('captcha.bg_color')) {
            $img->image_bg_color = new Securimage_Color(c::get('captcha.bg_color'));
	  }
	  if (c::get('captcha.text_color')) {
            $img->text_color = new Securimage_Color(c::get('captcha.text_color'));
	  }
          if (c::get('captcha.line_color')) {
            $img->line_color = new Securimage_Color(c::get('captcha.line_color'));
          }
	  if (get('namespace')) {
            $img->setNamespace(get('namespace'));
          }
	 
	  $img->show();
	  return false;
        }
      ]
    ];
  }
]);

