<?php

namespace App\Http\Controllers;

use App\Models\Period;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
class ReservationController extends Controller
{
    public function __construct()
    {
        Carbon::setLocale('es');
        
    }
    public function index()
    {
        $users = User::select('id')->where('laboratory_id', Auth::user()->laboratory_id)->get();
        $reserva= Reservation::whereIn('id',$users)->get();
        return view('reservas.index',compact('reserva'));
    }


    public function menu(){
        return view('reservations.index');
    }

    public function searched(Request $request, $id){
        $today = Carbon::now('America/Caracas')->today();
        $dateUsed = ["date" => $request['date'], "error" => "No es posible
        realizar una reserva para una fecha pasada a la actual, se mostrara los periodos disponibles para 
        el dia de hoy", "now" => Carbon::now()];
        $periods = Period::where('status',1)->where('laboratory_id', $id)->get();
        if($today == Carbon::parse($request['date'])){
            $periods = Period::where('begin','>', Carbon::now('America/Caracas'))->where('laboratory_id', $id)->where('status',1)->get();
        }
        return view('reservations.index', compact('periods'))->with('dateUsed', $dateUsed);
    }

    public function select($id,$date){
        $usedRooms = Reservation::select('room_id')->where('period_id',$id)->where('date', $date)->where('status',1)->get();
        $room = Room::whereNotIn('id',$usedRooms)->first();
        Reservation::create([
            'date' => $date,
            'user_id' => Auth::user()->id,
            'period_id' => $id,
            'room_id' => $room->id
        ]);  
        return redirect()->route('reservation.myReservations', Auth::user()->id);
    }

    public function myReservations($id){
        $reservations = Reservation::where('user_id',$id)->get();
        $reservations->load('user');
        $reservations->load('period');
        $reservations->load('room');
        return view('reservations.myReservations',compact('reservations'));
    }

    public function desactivate($id){
        $reservation = Reservation::findOrFail($id);
        $reservation->status = 0;
        $reservation->update();
        return redirect()->route('reservation.myReservations', $reservation->user_id);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('reservas.create');
    }

    

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show( Request $request )
    {
        $dateUsed = ["date" => Carbon::now(), "error" => "No es posible
        realizar una reserva para una fecha pasada a la actual, se mostrara los periodos disponibles para 
        el dia de hoy", "now" => Carbon::now()];
        if(Carbon::parse($request['date']) > Carbon::yesterday()){ // si la fecha seleccionada es correcta no hay error
            $dateUsed['date'] = Carbon::parse($request['date']);
            $dateUsed['error'] = null;
        }
       
        $reserved = Reservation::select('period_id')
        ->where('date',  $dateUsed['date'])
        ->where('status','1')->get();
        $periods = Period::whereNotin('id',$reserved)->get();
        return view('reservas.show', compact('periods'))->with('dateUsed',$dateUsed);
    }

    public function register( $period , $date){
           //$room=Room::select('id')->where('status',2)->get();
            
           $room=Room::where('status',1)->get();
             
            return view('reservas.register', [
            "date"=> $date, "period" => $period ,'room'=>$room]);
     }
    

     public function store(Request $request)
     {
        $request->validate([
            'description' => ['required'],
            'room_id' => ['required'],
        ]);
         $id=$request['room_id'];
         $room=Room::findOrfail($id);
        $room->status=1;
        $room->update();

        Reservation::create([
        
            'date' => $request['date'],
            'description' => $request['description'],
            'period_id' => $request['period_id'],
            'room_id' => $request['room_id'],

        ]);
       
        return redirect()->route('reservations');
     }
     public function update($id){
         $reservation=Reservation::findOrfail($id);
         $reservation->status=1;
         $reservation->update();
         $room=Room::findOrfail($reservation->room_id);
         $room->status=1;
         $room->update();
         return redirect()->route('reservations');
     }

    public function destroy($id)
    {
        $reservation=Reservation::findOrfail($id);
        $reservation->delete();
        return redirect()->route('reservations');
    }
}
