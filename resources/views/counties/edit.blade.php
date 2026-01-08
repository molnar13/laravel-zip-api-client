@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">Megye szerkesztése</div>
    <div class="card-body">
        <form action="{{ route('counties.update', $county['id']) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label>Megye neve</label>
                <input type="text" name="name" class="form-control" value="{{ $county['name'] }}" required>
            </div>
            <button type="submit" class="btn btn-warning">Frissítés</button>
            <a href="{{ route('counties.index') }}" class="btn btn-secondary">Mégse</a>
        </form>
    </div>
</div>
@endsection