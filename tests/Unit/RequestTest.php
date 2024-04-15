
<?php

use NotilifyAPI\Request;

it('can be initiated without client', function () {
    $request = new Request();

    expect($request)->toBeInstanceOf(Request::class);
});

it('can be initiated with client', function () {
    $client = new GuzzleHttp\Client();

    $request = new Request($client);

    expect($request)->toBeInstanceOf(Request::class);
});
