@extends('layout')

@section('pageTitle')
    Add An Award
@stop

@section('content')
    <h2>Add An Award</h2>

    {{ Form::model( $award, [ 'route' => [ 'award.store' ], 'id' => 'award' ] ) }}

    <div class="row">
        <div class="columns small-1 Incised901Light" style="padding-left: 1.25rem; margin-left: 1.125rem;">
            {{Form::label('display_order', "Display Order", ['class' => "my"])}}
            {{Form::text('display_order')}}
        </div>
        <div class="columns small-3 Incised901Light end">
            {{Form::label('select_branch', 'Select a branch to add award info for', ['class' => 'my'])}}
            {{Form::select('select_branch', $branches, null, ['id' => 'select_branch'])}}
        </div>
    </div>
    <div id="award_info">
        <!-- Award info partials are inserted here -->
    </div>

@stop
@section('scriptFooter')
    <script type="text/javascript">
        $('#select_branch').on('change', function () {

            $.ajax({
                url: "/p/partials/awardinfo?branch=" + $('#select_branch').val(),
                cache: false
            })
                .done(function (html) {
                    $('#award_info').append(html);
                });
            $('#select_branch :selected').remove();
        });
    </script>
@stop