@extends ('layouts.app')

@section('head')

<style>
    
    video {
        transform: scaleX(-1);
    }
</style>
@endsection
@section ('content')
@include('scan_session')
@endsection