<fieldset>
    <legend class="ninety Incised901Light">&nbsp;Award Information ({{$branch}})&nbsp;</legend>
    <div class="row">
        <div class="columns small-1 Incised901Light">
            {{Form::label('code[' . $branch . ']', 'Award Code', ['class' => 'my'])}}
            {{Form::text('code[' . $branch . ']','', ['title' => 'Typically 3 to 4 letters.  These will also be used for post-nominals if the award confers them'])}}
        </div>
        <div class="columns small-4 Incised901Light">
            {{Form::label('name[' . $branch . ']', "Award Name", ['class' => "my"])}}
            {{Form::text('name[' . $branch . ']')}}
        </div>
        <div class="columns small-4 Incised901Light">
            {{Form::label('location[' . $branch . ']', "Uniform Location", ['class' => "my"])}}
            {{Form::select('location[' . $branch . ']', [null => 'Select Location', 'LR' => 'Left Ribbons', 'RR' => 'Right Ribbons', 'RS' => 'Right Sleeve', 'QB' => 'Qualification Badges'], '', ['title' => 'Where on the uniform is this award placed'])}}
        </div>
        <div class="columns small-3 Incised901Light">
            {{Form::label('postnominals[' . $branch . ']', '&nbsp;')}}<br/>
            {{Form::checkbox('postnominals[' . $branch . ']', 1)}} Confers Post Nominals
        </div>
    </div>
    <div class="row">
        <div class="columns small-12 Incised901Light">
            {{Form::label('issued_for[' . $branch . ']', "Award Description", ['class' => "my"])}}
            {{Form::textarea('issued_for[' . $branch . ']', '', ['placeholder' => 'Enter the description of the award, including examples of what it can be issued for.'])}}
        </div>
    </div>
    <div class="row">
        <div class="columns small-3 Incised901Light">
            {{Form::label('image_path[' . $branch . ']', "Award/Ribbon Image", ['class' => "my"])}}
            {{Form::file('image_path[' . $branch . ']')}}
        </div>
        <div class="columns small-4 Incised901Light end">
            {{Form::label('replaces[' . $branch . ']', "Other awards replaced by this award (optional)", ['class' => "my"])}}
            {{Form::text('replaces[' . $branch . ']', '', ['placeholder' => 'Enter a comma separated list of award CODES or leave blank'])}}
        </div>
    </div>
</fieldset>