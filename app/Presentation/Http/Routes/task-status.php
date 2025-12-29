<?php

Flight::route('GET /taskStatus', function () {
    $controller = Flight::taskStatusController();
    $controller->getAll();
});
