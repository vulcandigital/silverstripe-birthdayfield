## silverstripe-birthdayfield

## Requirements
* silverstripe/silverstripe-framework: ^4.0

## Install
```sh
composer require vulcandigital/silverstripe-birthdayfield
```

## Usage
```php
class BirthdayForm extends Form
{
    public function __construct(RequestHandler $controller = null, $name = self::DEFAULT_NAME)
    {
        $fields = FieldList::create([
            $birthday = BirthdayField::create('Birthday', 'Birthday')
        ]);

        $actions =  FieldList::create([
            FormAction::create('process', 'Submit Birthday')
        ]);

        $validator = RequiredFields::create([
            'Birthday'
        ]);

        parent::__construct($controller, $name, $fields, $actions, $validator);
    }
}
```

If you wish for it to render inline with columns (Bootstrap v3 required) you can then

```php
$birthday->setBootstrapRender(true);
```

If you want to disable the individual labels for each of the fields and leave only the main one:

```php
$birthday->disableTitles();
```

## Configuration
```yml
Vulcan\BirthdayField\Forms\BirthdayField:
    # Change the output/read-only format display of the field
    format: 'Y-m-d'
```

## License
[BSD 3-Clause](LICENSE.md) © Vulcan Digital Ltd
