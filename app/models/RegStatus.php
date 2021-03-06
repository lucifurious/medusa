<?php

use Jenssegers\Mongodb\Model as Eloquent;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RegStatus extends Eloquent {
	protected $fillable = ['status'];

    protected $table = 'status';

    static function getRegStatuses()
    {
        $retVal = [];

        foreach(self::orderBy('status')->get() as $status) {
            $retVal[$status->status] = $status->status;
        }

        asort($retVal);

        return ['' => 'Select a status'] + $retVal;
    }

}