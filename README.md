# CAPTCHA plugin

A plugin for [Kirby CMS](http://getkirby.com) to secure forms with CAPTCHAs using [Securimage](http://www.phpcaptcha.org)

## Installation

Clone this repository into `/site/plugins`:

`git clone --recursive https://github.com/bjo3rnf/kirby-plugin-captcha captcha`

### Required config variables and their defaults

- captcha.case_sensitive: true
- captcha.perturbation: .75
- captcha.num_lines: 8
- captcha.charset: 'ABCDEFGHJKMNPQRSTVWXYZ'

### Optional config variables

- captcha.ttf_file
- captcha.height
- captcha.width
- captcha.bg_color
- captcha.text_color
- captcha.line_color

See [Securimage documentation](https://www.phpcaptcha.org/documentation/customizing-securimage/) for further information.

## How to use it

Add a CAPTCHA field and image to your template:

```html
<label for="captcha">Captcha</label>
<input type="text" id="captcha" name="captcha" size="10" maxlength="6">
<img id="captchaimage" src="<?php echo url('captcha') ?>" alt="CAPTCHA Image">
<a href="#" onclick="document.getElementById('captchaimage').src = '<?php echo url('captcha') ?>?' + Math.random(); return false">[ Different Image ]</a>
```

Validate the field in a controller as shown in [Bastian's Gist](https://gist.github.com/bastianallgeier/c396df7923848912393d):

```php
<?php

return function($site, $pages, $page) {

  $alert = null;

  $data = array(
    'name'     => get('name'),
    'email'    => get('email'),
    'captcha'  => get('captcha'),
  );

  if (get('submit')) {

    $rules = array(
      'name'    => array('required'),
      'email'   => array('required', 'email'),
      'captcha' => array('required', 'captcha'),
    );

    $messages = array(
      'name'    => 'Please enter your name',
      'email'   => 'Please enter a valid email address',
      'captcha' => 'The security code entered was incorrect',
    );

    if ($invalid = invalid($data, $rules, $messages)) {
      $alert = $invalid;
    } else {
      $body  = snippet('contactmail', $data, true);
      $email = email(array(
        'to'      => c::get('contactform.to'),
        'from'    => c::get('contactform.from'),
        'subject' => c::get('contactform.subject'),
        'body'    => $body
      ));

      if ($email->send()) {
        go('thankyou');
      } else {
        $alert = array($email->error());
      }
    }
  }

  return compact('alert', 'data');
};
```

For multiple CAPTCHAS on the same page make sure to set a namespace for each field by adding a query parameter to the 
image url:

```html
...
<img id="captchaimage1" src="<?php echo url('captcha?namespace=captcha1') ?>" alt="CAPTCHA Image 1">
<input type="text" id="captcha1" name="captcha1" size="10" maxlength="6">
...
<img id="captchaimage2" src="<?php echo url('captcha?namespace=captcha2') ?>" alt="CAPTCHA Image 2">
<input type="text" id="captcha2" name="captcha2" size="10" maxlength="6">
...
```

and providing that namespace as the third parameter when registering the validator:

```php
...
    $rules = array(
      'name'    => array('required'),
      'email'   => array('required', 'email'),
      'captcha1' => array('required', 'captcha' => 'captcha1'),
      'captcha2' => array('required', 'captcha' => 'captcha2'),
    );
...
```

## Author

Björn Fromme <mail@bjo3rn.com>
