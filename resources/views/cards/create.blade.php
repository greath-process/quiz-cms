{{--@extends('layouts/layout')
@section('title', $title)--}}

{{--@section('content')
    <main class="w-full p-4 sm:p-20 mx-auto flex-auto">
        @livewire('cards-create')
    </main>
@endsection--}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Page</title>
    @livewireStyles
</head>
<body>

    <main class="w-full p-4 sm:p-20 mx-auto flex-auto">
        @livewire('cards-create')
    </main>
    @livewireScripts
</body>
</html>
