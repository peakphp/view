# <img src="https://raw.githubusercontent.com/peakphp/art/master/logo-clean-50x50.png" alt="Peak"> Peak/View

Fast and minimalist view template engine with macro, helpers and directives.

## Installation

```
$ composer require peak/view
```

### Create a view

A view need at least 2 things:

 - A Presentation
 - Data (or variables if you prefer)
 
```php
$presentation = new SingleLayoutPresentation(
    '/path/to/layout1.php', 
    '/path/to/view1.php'
);
$data = [
    'title' => 'FooTheBar'
    'name' => 'JohnDoe'
];
$view = new View($data, $presentation);
```

### Example of view templates

layout example:
```html
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= $this->title ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    <main>
        <?= $this->layoutContent ?>
    </main>
</body>
</html>
```

script example (represented by ```$this->layoutContent``` in your layout):
```html
<div class="container">
    hello <?= $this->name ?>
</div>
```



### Render a view

```php
$output = $view->render();
```

### Add a macro
Macro are closure that will be bind to your view class instance. They have access to all class properties/methods so they must be used carefully. Macro are ideal for small task. 

```php
$view->addMacro('formatedName', function() {
    return ucfirst($this->name);
});
```

and in your template view:
```php
...
<h1>Hello <?= $this->formatedName() ?></h1>
```

### Add an helper
An helper is a standalone object instance. You map a method from your helper to the view. In contrary of macro, helper do not have access to view properties/methods directly and tend to be more maintainable and secure than macro. Helper are ideal for advanced task and can benefit from dependencies injection.

Example of an helper class:
```php
class TextUtil
{
    public function escape($text)
    {
        return filter_var($text, FILTER_SANITIZE_SPECIAL_CHARS);
    }
}
```

Before you can use it, you'll need to expose ``escape`` method your view helpers:
```php
$view->setHelpers([
    'escape' => new TextUtil(),
]);
```

and finally, you'll be able to use your helper the same way you use macros
```php
...
<h1>Hello <?= $this->escape($this->name) ?></h1>
```


Tips: You can also add multiple methods from a single instance:
```php
$textUtil = new TextUtil();
$view->setHelpers([
    'espace' => $textUtil,
    'myMethod2' => $textUtil,
]);
```

### Create a complex Presentation 
```php
$presentation = new Presentation([
    '/layout1.php' => [
        '/view1.php',
        '/layout2,php' => [
            '/view2.php',
            '/view3.php',
        ]
    ]
]);
```