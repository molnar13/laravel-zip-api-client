@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Megyék kezelése</h1>
    @if(session('token'))
        <a href="{{ route('counties.create') }}" class="btn btn-success">Új Megye</a>
    @endif
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Megye neve</th>
                    <th>Műveletek</th>
                </tr>
            </thead>
            <tbody>
                @foreach($counties as $county)
                <tr>
                    <td>{{ $county['id'] }}</td>
                    <td>{{ $county['name'] }}</td>
                    <td>
                        @if(session('token'))
                            <a href="{{ route('counties.edit', $county['id']) }}" class="btn btn-sm btn-warning">Szerk.</a>
                            
                            <form action="{{ route('counties.destroy', $county['id']) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Biztosan törlöd? A hozzá tartozó városok is törlődhetnek!');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Törlés</button>
                            </form>
                        @else
                            <span class="text-muted small">Nincs jog</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection