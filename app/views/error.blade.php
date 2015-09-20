@extends('layout')

@section('pageTitle')
    Paging Sir Horace!
@stop

@section('content')
<div class="row">
    <div class="columns small-12">
        <h2>Paging Sir Horace! We've encountered a bug in the Medusa-class database. Say again, we've encountered a bug in the Medusa-class database.</h2>

        <p>Please report this error via email (<a href="mailto:bolthole@trmn.org">bolthole@trm.org</a>) or the <a href="https://bolthole.trmn.org">Web Portal</a>.
            Be sure to report exactly what you were doing when the error occurred or the exact steps required to duplicate the error and the error text below.</p>

        <p>Thank You,<br /><br />
        The Medusa Dev Team</p>
    </div>
</div>

<div class="row">
    <div class="alert-box alert" data-alert>
        <p>{{ $e->getMessage() }}</p>
        <p>Stack Trace:</p>
        <p class="small">{{ nl2br($e->getTraceAsString()) }}</p>

    </div>
</div>

@stop