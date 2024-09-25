<!DOCTYPE html>
<html>
<head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Scan QR Code</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        video {
            transform: scaleX(-1);
        }
    </style>
</head>
<body>    

@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <h1>Scan QR Code</h1>
        <center>
            <div class="form-group">
                <label for="room">Pilih Ruangan:</label>
                <select class="form-control" id="room">
                    <option value="">-- Pilih Ruangan --</option>
                    <option value="1">Ruangan 1</option>
                    <option value="2">Ruangan 2</option>
                    <option value="3">Ruangan 3</option>
                </select>
            </div>
            <div id="qr-reader" style="width: 500px;"></div>
            <div id="qr-reader-results"></div>
        </center>
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>ID Conference</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Affiliation</th>
                    <th>Check-in Time</th> 
                    <th>Room</th>
                </tr>
            </thead>
            <tbody id="visitor-info">
                @foreach($checkInTimes as $checkIn)
                    <tr>
                        <td>{{ $checkIn->visitor->id_conference }}</td>
                        <td>{{ $checkIn->visitor->name }}</td>
                        <td>{{ $checkIn->visitor->email }}</td>
                        <td>{{ $checkIn->visitor->affiliation }}</td>
                        <td>{{ $checkIn->check_in_time }}</td>
                        <td>{{ $checkIn->room }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
    <script>
        var scanning = false; // Flag untuk mencegah scan berulang

        function onScanSuccess(decodedText, decodedResult) {
            if (scanning) return; // Jika sedang scanning, hentikan proses

            const selectedRoom = document.getElementById('room').value;
            
            // Validasi jika ruangan belum dipilih
            if (!selectedRoom) {
                alert('Silakan pilih ruangan sebelum melakukan check-in.');
                return;
            }

            console.log('QR Code scanned:', decodedText);
            scanning = true; // Aktifkan flag scanning

            fetch('/check-in', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ 
                    qr_code: decodedText,
                    room: selectedRoom // Kirim data room
                })
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
    console.log('Response data:', data);
    if (data.success) {
        document.getElementById('visitor-info').insertAdjacentHTML('beforeend', `
            <tr>
                <td>${data.visitor.id_conference}</td>
                <td>${data.visitor.name}</td>
                <td>${data.visitor.email}</td>
                <td>${data.visitor.affiliation}</td>
                <td>${data.checkInTime.check_in_time}</td> <!-- Gunakan data check-in -->
                <td>${data.checkInTime.room}</td> <!-- Gunakan data room -->
            </tr>
        `);

        Swal.fire({
            title: `Welcome Mr./Mrs. ${data.visitor.name}`,
            text: `You have successfully checked in to Room ${data.checkInTime.room}.`,
            icon: 'success',
            timer: 2500,
            showConfirmButton: false
        });
    } else {
        alert('Check-in failed: ' + data.message);
    }

    setTimeout(() => {
        scanning = false;
    }, 3000);
})
            .catch(err => {
                console.error('Error:', err);
                alert('An error occurred. Please try again.');

                // Tetap izinkan scan lagi setelah error, setelah 3 detik
                setTimeout(() => {
                    scanning = false;
                }, 3000);
            });
        }

        // Mulai proses QR code scanning
        var html5QrCode = new Html5Qrcode("qr-reader");
        html5QrCode.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: 250 },
            onScanSuccess
        ).catch(err => {
            console.error('Unable to start QR code scanner', err);
        });
    </script>
@endsection
