@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Replace</div>

                <div class="panel-body">
                    <form class="form-horizontal" method="POST" action="{{ route('replacer') }}" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="form-group{{ $errors->has('zip_filer') ? ' has-error' : '' }}">
                            <label for="zip_filer" class="col-md-4 control-label">Sustitución masiva (zip)</label>

                            <div class="col-md-6">
                                <input id="zip_filer" type="file" class="form-control" name="zip_filer" value="{{ old('zip_filer') }}" autofocus>

                                @if ($errors->has('zip_filer'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('zip_filer') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('filer') ? ' has-error' : '' }}">
                            <label for="filer" class="col-md-4 control-label">o archivo a sustituir</label>

                            <div class="col-md-6">
                                <input id="filer" type="file" class="form-control" name="filer" value="{{ old('filer') }}">

                                @if ($errors->has('filer'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('filer') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('replace_txt') ? ' has-error' : '' }}">
                            <label for="replace_txt" class="col-md-4 control-label">Texto a sustituir</label>

                            <div class="col-md-6">
                                <input id="replace_txt" type="replace_txt" class="form-control" name="replace_txt" required>

                                @if ($errors->has('replace_txt'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('replace_txt') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Enviar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
