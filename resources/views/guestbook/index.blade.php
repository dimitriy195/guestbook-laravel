@extends('layouts.app')

@section('content')
        @include('guestbook._form')

    @include('guestbook._list', ['isRootLevel' => true])

    <div class="mt-3">
        {{ $messages->links() }}
    </div>
@endsection
