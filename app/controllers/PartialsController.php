<?php

class PartialsController extends \BaseController {

    public function missingMethod($parameters = [])
    {
        return View::make(implode('/', $parameters), Input::all());
    }
}