<?php

class AwardController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /award
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /award/create
	 *
	 * @return Response
	 */
	public function create()
	{

		return View::make('awards.create', ['award' => new Award,
        'branches' => [null => 'Select Branch to add award info for', 'RMN' => 'Royal Manticoran Navy', 'RMA' => 'Royal Manticoran Army', 'GSN' => 'Grayson Space Navy']
        ]);
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /award
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /award/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /award/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /award/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /award/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}