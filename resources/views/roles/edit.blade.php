@extends('adminlte::page')

@section('title', 'Enfermeria-MERLEN')

@section('content_header')
    <h1>Editar Rol</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
            <form action="{{route('roles.update', $role)}}" method="post" novalidate >
                @csrf
                @method('put')
                <label for="name">Ingrese el nombre del Rol</label>
                <input type="text" name="name" class="form-control" value="{{old('name', $role->name)}}"><br>
                @error('name')                    
                <small class="text-danger">*{{$message}}</small>
                <br><br>                                            
                @enderror                                                
                


                <button  class="btn btn-danger btn-sm" type="submit">Actualizar Rol</button>
            </form> 

    </div>
</div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script> console.log('Hi!'); </script>
@stop