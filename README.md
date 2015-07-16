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
<a href="#" onclick="document.getElementById('captchaimg').src = '<?php echo url('captcha') ?>?' + Math.random(); return false">[ Different Image ]</a>
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

## Author

Björn Fromme <mail@bjo3rn.com>
