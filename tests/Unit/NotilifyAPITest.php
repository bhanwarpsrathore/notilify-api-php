<?php

use NotilifyAPI\NotilifyAPI;
use NotilifyAPI\Request;

it('can be initiated without Request object', function () {
    $api = new NotilifyAPI();

    expect($api)->toBeInstanceOf(NotilifyAPI::class);
});

it('can be initiated with Request object', function () {
    $request = new Request();

    $api = new NotilifyAPI($request);

    expect($api)->toBeInstanceOf(NotilifyAPI::class);
});
