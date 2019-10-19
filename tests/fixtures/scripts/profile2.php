<h1>Profile of <?php echo $this->name; ?></h1><?=
    $this->renderChildren([
        __DIR__.'/child1.php',
        __DIR__.'/child2.php'
    ], [
        'secondName' => 'bob'
    ])
?><?= $this->renderOrphans([
    __DIR__.'/orphan1.php',
], [
    'name' => 'foobar'
])
?>