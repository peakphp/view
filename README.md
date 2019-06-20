# <img src="https://raw.githubusercontent.com/peakphp/art/master/logo-clean-50x50.png" alt="Peak"> Peak/View

Fast and minimalist view template engine with macro and helpers.

### Installation

```
$ composer require peak/view
```

### Documentation

See https://peakphp.github.io/docs/view

### Quick start

```php
<?php

$vars = ['foo' => 'bar'];
$presentation = new SingleLayoutPresentation('/path/to/layout.php', '/path/to/script.php');
$view = new View($vars, $presentation);
$content = $view->render();
```

layout.php

```
<html>
<head>
    <title>Hello <?= $this->foo; ?></title>
</head>
<body>
    <?= $this->layoutContent; ?>
</body>
</html>
```

script.php

```
<h1>Hello <?= $this->foo; ?> !</h1>
<p>Welcome aboard ... </p>
```

