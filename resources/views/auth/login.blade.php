@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card mt-5">
            <div class="card-header bg-dark text-white">Bejelentkezés</div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('login.post') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email cím</label>
                        <input type="email" name="email" class="form-control" required autofocus placeholder="pl. teszt@pelda.hu">
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Jelszó</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Belépés</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection