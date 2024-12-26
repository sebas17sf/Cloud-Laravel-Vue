@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Máquina Virtual</h1>

    <form action="{{ route('vms.update', $vm->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">Nombre</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $vm->name }}" required>
        </div>
        <div class="form-group">
            <label for="box">Box</label>
            <input type="text" class="form-control" id="box" name="box" value="{{ $vm->box }}" required>
        </div>
        <div class="form-group">
            <label for="memory">Memoria (MB)</label>
            <input type="number" class="form-control" id="memory" name="memory" value="{{ $vm->memory }}" required>
        </div>
        <div class="form-group">
            <label for="cpus">CPUs</label>
            <input type="number" class="form-control" id="cpus" name="cpus" value="{{ $vm->cpus }}" required>
        </div>

        <div class="form-group">
            <label for="storage">Almacenamiento (GB)</label>
            <input type="number" class="form-control" id="storage" name="storage" value="{{ $vm->storage }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
    </form>
</div>
@endsection
