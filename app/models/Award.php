<?php
use Jenssegers\Mongodb\Model as Eloquent;
use Medusa\Enums\MedusaDefaults;
use Medusa\Permissions\MedusaPermissions;
use Medusa\Audit\MedusaAudit;

class Award extends Eloquent {

    use MedusaPermissions, MedusaAudit;

	protected $fillable = [];
}