@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <h1>Atendee List</h1>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <a href="{{ route('visitor.create') }}" class="btn btn-primary mb-3">Add</a>
        <a href="{{ route('visitor.scan') }}" class="btn btn-success mb-3">Scan QR Code</a>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID Conference</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Affiliation</th>
                    <th>QR Code</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($visitors as $visitor)
                    <tr>
                        <td>{{ $visitor->id_conference }}</td>
                        <td>{{ $visitor->name }}</td>
                        <td>{{ $visitor->email }}</td>
                        <td>{{ $visitor->affiliation }}</td>
                        <td class="text-center">
                            <div>
                                {!! QrCode::size(100)->generate($visitor->qr_code) !!}
                                <div class="mt-2">
                                    <a href="{{ route('visitor.downloadQrCode', $visitor->id) }}" class="btn btn-primary mt-2">Download QR Code</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">No visitors found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
