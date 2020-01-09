# Kirby3 captcha

**Requirement:** Kirby 3.0

A plugin for the [Kirby CMS](http://getkirby.com) v3.x to secure forms with CAPTCHAs using [Securimage](http://www.phpcaptcha.org)

## Installation

Clone this repository into `/site/plugins`:

`git clone --recursive https://github.com/marsch-/kirby3-captcha captcha`

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

Validate the field in a controller as shown in the [Email form recipe](https://getkirby.com/docs/cookbook/forms/basic-contact-form#the-contact-form-controller) from the Kirby cookbook:

```php
<?php
return function($kirby, $pages, $page) {

    $alert = null;

    if($kirby->request()->is('POST') && get('submit')) {

        // check the honeypot
        if(empty(get('website')) === false) {
            go($page->url());
            exit;
        }

        $data = [
            'name'  => get('name'),
            'email' => get('email'),
            'text'  => get('text'),
            'captcha'  => get('captcha'),
        ];

        $rules = [
            'name'  => ['required', 'min' => 3],
            'email' => ['required', 'email'],
            'text'  => ['required', 'min' => 3, 'max' => 3000],
            'captcha' => ['required', 'captcha'],
        ];

        $messages = [
            'name'  => 'Please enter a valid name',
            'email' => 'Please enter a valid email address',
            'text'  => 'Please enter a text between 3 and 3000 characters',
            'captcha' => 'The security code entered was incorrect',
        ];

        // some of the data is invalid
        if($invalid = invalid($data, $rules, $messages)) {
            $alert = $invalid;

            // the data is fine, let's send the email
        } else {
            try {
                $kirby->email([
                    'template' => 'email',
                    'from'     => 'yourcontactform@yourcompany.com',
                    'replyTo'  => $data['email'],
                    'to'       => 'you@yourcompany.com',
                    'subject'  => esc($data['name']) . ' sent you a message from your contact form',
                    'data'     => [
                        'text'   => esc($data['text']),
                        'sender' => esc($data['name'])
                    ]
                ]);

            } catch (Exception $error) {
                $alert['error'] = "The form could not be sent";
            }

            // no exception occured, let's send a success message
            if (empty($alert) === true) {
                $success = 'Your message has been sent, thank you. We will get back to you soon!';
                $data = [];
            }
        }
    }

    return [
        'alert'   => $alert,
        'data'    => $data ?? false,
        'success' => $success ?? false
    ];
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

Björn Fromme <mail@bjo3rn.com>, Markus Schatzl <marsch@mailbox.org>
