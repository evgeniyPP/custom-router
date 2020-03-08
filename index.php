<?php
include_once 'Router.php';

$router = new Router();

$router->get('/', function () {
    global $text;
    $text = 'Введи своё имя!';
});

$router->get('/hello/:name', function ($name) {
    global $text;
    $text = "Привет, {$name}!";
});

$router->post('/', function () {
    global $text;
    $name = trim(htmlspecialchars($_POST['name']));
    $text = "Привет, $name!";
});

$router->get('/post/edit/:name/:id', function ($name, $id) {
    global $text;
    $text = "{$name}, вы редактируете пост №{$id}!";
});

$router->run();

?>

<h2><?=$text?></h2>
<form method='POST'>
    <input type="text" name="name">
    <input type="submit" value="Отправить">
</form>