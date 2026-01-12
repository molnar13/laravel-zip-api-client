@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-warning">Város szerkesztése</div>
            <div class="card-body">
                
                <form action="{{ route('settlements.update', $settlement['id']) }}" method="POST">
                    @csrf
                    @method('PUT') <div class="mb-3">
                        <label class="form-label">Település neve</label>
                        <input type="text" name="name" class="form-control" 
                               value="{{ old('name', $settlement['name']) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Irányítószám</label>
                        <input type="text" name="zip_code" class="form-control" 
       value="{{ old('zip_code', $settlement['postal_code'] ?? $settlement['zip_code'] ?? '') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Megye</label>
                        <select name="county_id" class="form-select" required>
                            @foreach($counties as $county)
                                <option value="{{ $county['id'] }}" 
                                    {{ $settlement['county_id'] == $county['id'] ? 'selected' : '' }}>
                                    {{ $county['name'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('settlements.index') }}" class="btn btn-secondary">Mégse</a>
                        <button type="submit" class="btn btn-warning">Frissítés</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection