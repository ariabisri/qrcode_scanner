<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visitor;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\CheckInTime;
use FontLib\Table\Type\name;

class VisitorController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Fungsi untuk menampilkan daftar pengunjung
    public function index()
    {
        $visitors = Visitor::all();
        $checkInTimes = CheckInTime::with('visitor')->whereDate('check_in_time', Carbon::today())->get();

        return view('visitor.index', compact('visitors', 'checkInTimes'));
    }

    public function create()
    {
        return view('visitor.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'affiliation' => 'nullable|string|max:255',
            'id_conference' => 'nullable|string|max:255',
        ]);

        Visitor::create([
            'name' => $request->name,
            'email' => $request->email,
            'affiliation' => $request->affiliation,
            'id_conference' => $request->id_conference,
            'qr_code'=>$request->id_conference.strval(rand(10000,99999))
        ]);

        return redirect()->route('visitor.index')->with('success', 'Visitor added successfully.');
    }

    // Fungsi untuk menampilkan QR code dan detail pengunjung
    public function show($id)
    {
        $visitor = Visitor::findOrFail($id);

        $qrCode = QrCode::size(200)->generate(route('visitor.show', $visitor->id));

        return view('visitor.show', compact('visitor', 'qrCode'));
    }

    public function scan(Request $request)
    {
        $visitor = Visitor::where('id', $request->input('id'))
            ->where('attended', false)
            ->first();

        if ($visitor) {
            $visitor->attended = true;
            $visitor->save();
            return response()->json(['status' => 'success', 'message' => 'Attendance marked.']);
        }

        return response()->json(['status' => 'error', 'message' => 'Invalid or already marked as attended.']);
    }

    public function invitation($id)
    {
        $visitor = Visitor::findOrFail($id);
        $qrCode = QrCode::size(250)->generate(route('visitor.undangan', $id));
        return view('visitor.undangan', compact('visitor', 'qrCode'));
    }

    public function showScanPage()
    {
        $checkInTimes = CheckInTime::with('visitor')->whereDate('check_in_time', Carbon::today())->get();
        return view('visitor.scan', compact('checkInTimes'));
    }

    public function checkIn(Request $request)
    {
        Log::info('Check-in process started.');

        $request->validate([
            'qr_code' => 'required|string',
            'room' => 'required|string',
        ]);

        Log::info('QR Code received: ' . $request->qr_code);
        Log::info('Room received: ' . $request->room);

        $visitor = Visitor::where('qr_code', $request->qr_code)->first();

        if ($visitor) {
            $today = Carbon::now()->setTimezone('Asia/Jakarta');

            $checkInToday = CheckInTime::where([
                ['visitor_id', '=', $visitor->id],
                ['room', '=', $request->room]
            ])
            ->whereDate('check_in_time', $today)
            ->exists();
            Log::info(Carbon::now()->setTimezone('Asia/Jakarta'));

            if ($checkInToday) {
                Log::warning('Visitor has already checked in today: ' . $visitor->id);
                return response()->json(['success' => false, 'icon'=>'warning', 'message' => $visitor->name.', you have already checked in']);
            } else{

                Log::info('Saving CheckInTime for Visitor ID: ' . $visitor->id);
    
                $checkInTime = new CheckInTime();
                $checkInTime->visitor_id = $visitor->id;
                $checkInTime->check_in_time = Carbon::now()->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s');
                $checkInTime->room = $request->room;
    
                Log::info('Data to be saved:', [
                    'visitor_id' => $visitor->id,
                    'check_in_time' => $checkInTime->check_in_time,
                    'room' => $checkInTime->room,
                ]);
    
                $checkInTime->save();

                $checked =  $checkInTimes = CheckInTime::with('visitor')->where([['visitor_id', $visitor->id],['room', $request->room]])->get();
    
                Log::info('Visitor checked in successfully: ' . $visitor->id);
    
                return response()->json(['success' => true, 'visitor' => $visitor, 'checkInTime' => $checked]);
            }

        } else{

            Log::warning('Visitor not found for QR Code: ' . $request->qr_code);
            return response()->json(['success' => false, 'icon'=>'error', 'message' => 'Attendee not found.']);
        }

    }

    public function downloadQrCode($id)
    {
        $visitor = Visitor::findOrFail($id);
        $qrCode = QrCode::format('png')->size(250)->margin(5)->generate($visitor->qr_code);

        return response()->stream(
            function () use ($qrCode) {
                echo $qrCode;
            },
            200,
            [
                'Content-Type' => 'image/png',
                'Content-Disposition' => 'attachment; filename="qr_code.png"',
            ]
        );
    }

    public function session($name)
    {
        $checkInTimes = CheckInTime::with('visitor')->where('room', $name)->get();
        return view('session', compact('name', 'checkInTimes'));
    }

    

    

}
