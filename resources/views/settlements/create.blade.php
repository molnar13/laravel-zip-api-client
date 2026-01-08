@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-success text-white">Új város hozzáadása</div>
            <div class="card-body">
                
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('settlements.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Település neve</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Irányítószám</label>
                        <input type="text" name="zip_code" class="form-control" value="{{ old('zip_code') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Megye</label>
                        <select name="county_id" class="form-select" required>
                            <option value="">-- Válassz megyét --</option>
                            @foreach($counties as $county)
                                <option value="{{ $county['id'] }}" {{ old('county_id') == $county['id'] ? 'selected' : '' }}>
                                    {{ $county['name'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('settlements.index') }}" class="btn btn-secondary">Mégse</a>
                        <button type="submit" class="btn btn-success">Mentés</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection