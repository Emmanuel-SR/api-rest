<?php
declare (strict_types = 1);

include 'help/help.php';
include 'config.php';
include 'request.php';
include 'router.php';
include 'response.php';
include 'db/conexion.php';
include 'db/ORM.php';
include 'db/model.php';

includeModel();

$router = new Router(new Request());

$router->post('/employees', function ($request) {
    $persona  = Persona::find(1);
    $response = new Response(200, $persona->getColumnas());
    $response->output();
});
