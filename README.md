# SunEditor Field for Laravel Nova

This package provides a custom field for Laravel Nova that integrates the SunEditor, a lightweight and flexible WYSIWYG editor.

![screenshot](screenshot.png)

## Features

- Rich text editing capabilities with SunEditor.
- Seamless integration with Laravel Nova.
- Easy configuration and customization.
- Emoji support within editor.

## Installation

To install the custom field, follow these steps:

1. Install the package via Composer:

    ```sh
    composer require webard/nova-suneditor-field
    ```

2. Publish the assets:

    ```sh
    php artisan vendor:publish --provider="Webard\NovaSunEditor\FieldServiceProvider"
    ```

## Usage

To use the SunEditor field in your Laravel Nova resource, follow these steps:

1. Import the field in your Nova resource file:

    ```php
    use Webard\NovaSunEditor\SunEditor;
    ```

2. Add the field to the `fields` method of your Nova resource:

    ```php
    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),

            SunEditor::make('Content', 'content')
                ->rules('required', 'string')
                ->hideFromIndex(),
        ];
    }
    ```

## Customization

The SunEditor field can be customized by some methods:

1. In `nova-suneditor.php` you can define more buttonLists and name them. Then, you can use buttonList using `buttonListName` method.

```php
SunEditor::make('Content', 'content')
    ->buttonListName('my-buttons')
```

2. You can define buttonList directly in field definition:

```php
SunEditor::make('Content', 'content')
    ->buttonList([
        [
            'undo',
            'redo',
            'bold'
        ]
    ]);
```

3. You can change settings directly in field definition:

```php
SunEditor::make('Content', 'content')
    ->settings([
        'minHeight' => '500px'
    ]);
```

## TODO

I'm are actively seeking contributions to enhance this package. Here are some features I would love to see implemented:

- [ ] multi-language
- [ ] image browser

## Contributing

We welcome contributions to improve this plugin! Please follow these steps to contribute:

1. Fork the repository.
2. Create a new branch for your feature or bug fix.
3. Make your changes and commit them with descriptive messages.
4. Push your changes to your forked repository.
5. Open a pull request to the main repository.

## License

This project is licensed under the MIT License. See the [LICENSE.md](LICENSE.md) file for more details.

## Acknowledgements

- [SunEditor](https://github.com/JiHong88/suneditor) - Excellent WYSIWYG editor.
- [Picmo](https://github.com/joeattardi/picmo) - The emoji picker library used in this plugin.

## Contact

For questions or support, please open an issue on GitHub.
