<?php

Route::get('show', function() {
    return View::make('show', [
        'faces'   => App::make('funnyfaces')->latest(),
        'success' => Session::get('success'),
    ]);
});

Route::get('upload', function() {
    return View::make('upload', [
        'success' => Session::get('success'),
    ]);
});

Route::post('upload', ['before' => 'csrf', function() {
    try {
        $caption = Input::get('caption');
        $file    = Input::file('photo');
        if ($file == null || $caption == null) {
            throw new \InvalidArgumentException('File was not uploaded.');
        }

        App::make('funnyfaces')->add($file, $caption);
        return Redirect::to('show')->with('success', true);
    } catch (\Exception $e) {
        Log::error($e->getMessage());
        return Redirect::to('upload')->with('success', false);
    }
}]);
