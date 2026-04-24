@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1>Profile</h1>

    {{-- Profile Update Form --}}
    <form method="post" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input id="name" name="name" type="text" class="form-control"
                   value="{{ old('name', $user->first_name . ' ' . $user->last_name) }}" required>
            @error('name') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" name="email" type="email" class="form-control"
                   value="{{ old('email', $user->email) }}" required>
            @error('email') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn btn-primary">Save</button>
    </form>

    {{-- Delete Account Form --}}
    <hr class="my-4">
    <form method="post" action="{{ route('profile.destroy') }}">
        @csrf
        @method('delete')

        <div class="mb-3">
            <label for="password" class="form-label">Confirm Password to Delete Account</label>
            <input id="password" name="password" type="password" class="form-control">
            @if ($errors->userDeletion->has('password'))
                <div class="text-danger">{{ $errors->userDeletion->first('password') }}</div>
            @endif
        </div>

        <button type="submit" class="btn btn-danger">Delete Account</button>
    </form>
</div>
@endsection
