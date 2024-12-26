@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Crear Nueva Máquina Virtual</h1>

    <form action="{{ route('vms.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Nombre</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="box">Box</label>
            <input type="text" class="form-control" id="box" name="box" required>
        </div>
        <div class="form-group">
            <label for="memory">Memoria (MB)</label>
            <input type="number" class="form-control" id="memory" name="memory" required>
        </div>
        <div class="form-group">
            <label for="cpus">CPUs</label>
            <input type="number" class="form-control" id="cpus" name="cpus" required>
        </div>
        <div class="form-group">
            <label for="storage">Almacenamiento (GB)</label>
            <input type="number" class="form-control" id="storage" name="storage" required>
        </div>
        <button type="submit" class="btn btn-primary">Crear</button>
    </form>
</div>
@endsection
