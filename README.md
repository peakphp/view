# <img src="https://raw.githubusercontent.com/peakphp/art/master/logo-clean-50x50.png" alt="Peak"> Peak/View

Fast and minimalist view template engine with macro, helpers and directives.

## Installation

This is a standalone packages and it is not provided automatically with ``peak/framework``

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

### Macros
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

### Helpers
An helper is a standalone object instance that is ``callable``. In contrary of macro, helper do not have access to view properties/methods directly but tend to be more maintainable and secure than macro. Helper are ideal for advanced task and can benefit from dependencies injection.

Example of an helper class:
```php
class EscapeTextHelper
{
    public function __invoke($text)
    {
        return filter_var($text, FILTER_SANITIZE_SPECIAL_CHARS);
    }
}
```

Before you can use it, you'll need to give a function name to your view helper:
```php
$view->setHelpers([
    'escape' => new EscapeTextHelper(),
    // ...
]);
```

and finally, you'll be able to use your helper the same way you use macros
```php
...
<h1>Hello <?= $this->escape($this->name) ?></h1>
```

### Directives

Directives provide you simpler and more elegant syntax for  writing templates. By default, there is not directive activated in your View. You need to add them to your View instance with ``setDirectives()`` method. The downside of directives is that View must run them after rendering a template, adding an extra compilation step. The more directives you have, the more it take times to render the view . Of course, this side effect can be mitigated with a proper caching solution, but to keep things simple, Peak View doesn't provide one by default.

```php
$view->setDirectives([
    // give you ability to print variable with syntax {{ $myVar }}
    new EchoDirective(), 
    // give you ability to call native php functions, 
    // macros or helpers with syntax @function(...args)
    new FnDirective(),  
]);

$view->addVars([
    'name' => 'bob'
    'messages' => [
        // ...
    ],
    'items' => [
        'property' => 'value'
    ]
]);
```

template.php
```html
<h1>Hello {{ $name }}</h1>

<p>You can call native php function with @</p>
<h4>@date(l \t\h\e jS) - You have @count($name) message(s)</h4>

<p>You can also call helpers and macros too</p>
<p>@escape($name)</p>

<p>And you can still use directly PHP like this: <?= $this->name; ?></p>

<p>And finally, array variable can be accessed with DotNotation syntax: {{ $items.property }}</p>
```

It is important to keep in mind that PHP is executed first in your template and directives are compiled/rendered after that. 

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