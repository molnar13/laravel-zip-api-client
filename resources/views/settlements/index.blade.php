@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Települések Listája</h1>
    <div>
        <a href="{{ route('settlements.export.pdf') }}" class="btn btn-outline-danger">PDF Export</a>
        <a href="{{ route('settlements.filter') }}" class="btn btn-outline-primary">ABC Szűrő</a>
        
        @if(session('token'))
            <a href="{{ route('settlements.create') }}" class="btn btn-success">Új Város</a>
        @endif
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Irányítószám</th>
                    <th>Település</th>
                    <th>Megye</th>
                    <th>Műveletek</th>
                </tr>
            </thead>
            <tbody>
                @forelse($settlements as $s)
                <tr>
                    <td>{{ $s['zip_code'] ?? '-' }}</td>
                    <td>{{ $s['name'] }}</td>
                    <td>{{ $s['county']['name'] ?? 'Nincs adat' }}</td>
                    <td>
                        @if(session('token'))
                            <a href="#" class="btn btn-sm btn-warning">Szerk.</a>
                            @else
                            <span class="text-muted text-small">Nincs jog</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center">Nincs megjeleníthető adat.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection