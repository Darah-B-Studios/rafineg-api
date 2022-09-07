@extends('layouts.dashboard')

@section('content')
<h2 class="mt-4">List of registered users</h2>
<div class="table-responsive">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Email address</th>
                <th scope="col">Telephone</th>
                <th scope="col">Options</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr class="text-sm">
                <td>{{$user->id}}</td>
                <td>{{$user->firstname}} {{$user->lastname}}</td>
                <td>{{$user->email}}</td>
                <td>{{$user->phone_number}}</td>
                <td>
                    <a href="{{route('manage-users.show', $user->id)}}" class="btn btn-sm btn-link text-primary px-2">view</a>
                    <button class="btn btn-sm btn-text text-danger px-2">delete</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
