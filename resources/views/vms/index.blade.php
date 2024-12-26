@extends('layouts.app')
 

@section('content')
<div class="container">
    <h1>Lista de Máquinas Virtuales</h1>
    <a href="{{ route('vms.create') }}" class="btn btn-primary">Crear Nueva VM</a>
    <table class="table mt-4">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Box</th>
                <th>Memoria (MB)</th>
                <th>CPUs</th>
                <th>Almacenamiento (GB)</th>
                <th>Acciones</th>

            </tr>
        </thead>
        <tbody>
            @foreach($vms as $vm)
                <tr>
                    <td>{{ $vm->name }}</td>
                    <td>{{ $vm->box }}</td>
                    <td>{{ $vm->memory }}</td>
                    <td>{{ $vm->cpus }}</td>
                    <td>{{ $vm->storage }}</td>
                    <td>
                        <a href="{{ route('vms.show', $vm->id) }}" class="btn btn-info">Ver</a>
                        <a href="{{ route('vms.edit', $vm->id) }}" class="btn btn-warning">Editar</a>
                        <form action="{{ route('vms.destroy', $vm->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Eliminar</button>
                        </form>
                         <form action="{{ route('vms.stop', $vm->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-danger">Stop</button>
                        </form>

                         <form action="{{ route('vms.start', $vm->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-danger">Start</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
