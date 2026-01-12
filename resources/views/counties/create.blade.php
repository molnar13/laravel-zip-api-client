@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">Új Megye</div>
    <div class="card-body">
        <form action="{{ route('counties.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label>Megye neve</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">Mentés</button>
            <a href="{{ route('counties.index') }}" class="btn btn-secondary">Mégse</a>
        </form>
    </div>
</div>
@endsection