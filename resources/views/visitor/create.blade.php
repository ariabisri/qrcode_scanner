@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <h1 class="mb-4">Add Participant</h1>

        <form action="{{ route('visitor.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="id_conference">ID Conference:</label>
                <input type="text" id="id_conference" name="id_conference" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="affiliation">Affiliation:</label>
                <input type="text" id="affiliation" name="affiliation" class="form-control" placeholder="Kampus/Kantor/PT">
            </div>


            <button type="submit" class="btn btn-primary">Submit</button>
        </form>

        <a href="{{ route('visitor.index') }}" class="btn btn-secondary mt-3">Kembali ke Daftar Pengunjung</a>
    </div>
@endsection
