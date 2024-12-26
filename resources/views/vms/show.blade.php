@extends('layouts.app')

@section('content')

    <div class="container">
        <h1>Detalles de la Máquina Virtual</h1>

        <table class="table">
            <tr>
                <th>Nombre</th>
                <td>{{ $vm->name }}</td>
            </tr>
            <tr>
                <th>Box</th>
                <td>{{ $vm->box }}</td>
            </tr>
            <tr>
                <th>Memoria (MB)</th>
                <td>{{ $vm->memory }}</td>
            </tr>
            <tr>
                <th>CPUs</th>
                <td>{{ $vm->cpus }}</td>
            </tr>

            <tr>
                <th>Almacenamiento (GB)</th>
                <td>{{ $vm->storage }}</td>
            </tr>

            <tr>
                <th>IP</th>
                <td>{{ $vm_ip }}</td>
        </table>




    </div>


@endsection
