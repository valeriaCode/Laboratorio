<?php

namespace App\Http\Controllers;

use App\Models\Binnacle;
use App\Models\Room;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sala=Room::where('laboratory_id', Auth::user()->laboratory_id)->get();
        return view('salas.index',compact('sala'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('salas.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $name=request('name');
        $status=request('status');

        $sala=Room::create([
            'name'=> request('nombre'),
            'laboratory_id' => Auth::user()->laboratory_id
        ]);
        Binnacle::setInsert($sala->nombre,"salas", Auth::user());
        return redirect()->route('rooms.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $sala=Room::findOrfail($id);
        return view('salas.edit',compact('sala'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $dato=request()->validate([
            'name'=>['required'],
            'status'=>['required'],
            ]);
        DB::table('rooms')->where('id',$id)->update([
            'name'=>$dato['nombre'],
            ]);
        Binnacle::setUpdate($dato['nombre'],"salas", Auth::user());
            return redirect()->route('rooms.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $sala = Room::findOrFail($id);
        Room::destroy($id);
        Binnacle::setDelete($sala->nombre,"salas", Auth::user());
        return redirect()->route('rooms.index');
    }
}
