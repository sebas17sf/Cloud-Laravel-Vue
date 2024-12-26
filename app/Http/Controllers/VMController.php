<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VMConfig;
use phpseclib3\Net\SSH2;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class VMController extends Controller
{
    public function index()
    {
         $vms = auth()->user()->vmConfigs;

        return view('vms.index', compact('vms'));
    }

    public function create()
    {
        return view('vms.create');
    }

    public function store(Request $request)
    {
        // Validación de la solicitud
        $request->validate([
            'name' => 'required|string|max:255',
            'box' => 'required|string|max:255',
            'memory' => 'required|integer',
            'cpus' => 'required|integer',
            'storage' => 'required|integer',
        ]);

        // Calcular el tamaño de almacenamiento en MB
        $storageMB = $request->storage * 1024;

        // Obtener el ID del usuario autenticado
        $userId = Auth::id();

        try {
            // Crear la nueva configuración de VM con el user_id establecido
            $vmConfig = VMConfig::create([
                'name' => $request->name,
                'box' => $request->box,
                'memory' => $request->memory,
                'cpus' => $request->cpus,
                'storage' => $request->storage,
                'user_id' => $userId,
            ]);

            // Generar el contenido del Vagrantfile con la configuración de red bridge y almacenamiento
            $vagrantfileContent = "
            Vagrant.configure('2') do |config|
                config.vm.define '{$vmConfig->name}' do |vm|
                    vm.vm.box = '{$vmConfig->box}'
                    vm.vm.network 'public_network', bridge: true
                    vm.vm.provider 'virtualbox' do |v|
                        v.memory = '{$vmConfig->memory}'
                        v.cpus = '{$vmConfig->cpus}'
                    end
                end
            end
            ";

            // Guardar el Vagrantfile en un directorio específico
            $vagrantDirectory = base_path("vagrant/{$vmConfig->name}");
            if (!file_exists($vagrantDirectory)) {
                mkdir($vagrantDirectory, 0777, true);
            }
            file_put_contents("{$vagrantDirectory}/Vagrantfile", $vagrantfileContent);

            // Crear y adjuntar el disco de almacenamiento
            $vboxManageCreateDisk = shell_exec("VBoxManage createhd --filename {$vagrantDirectory}/storage.vdi --size {$storageMB} 2>&1");
            Log::info('VBoxManage createhd output: ' . $vboxManageCreateDisk);

            $vagrantUpOutput = shell_exec("cd {$vagrantDirectory} && vagrant up --provider=virtualbox 2>&1");
            Log::info('Vagrant up output: ' . $vagrantUpOutput);

            // Adjuntar el disco después de iniciar la VM
            $vboxManageAttachDisk = shell_exec("VBoxManage storageattach {$vmConfig->name} --storagectl 'SATA Controller' --port 0 --device 0 --type hdd --medium {$vagrantDirectory}/storage.vdi 2>&1");
            Log::info('VBoxManage storageattach output: ' . $vboxManageAttachDisk);

            // Verificar si hubo algún error durante vagrant up
            if (strpos($vagrantUpOutput, 'error') !== false || strpos($vagrantUpOutput, 'Error') !== false) {
                return redirect()->route('vms.index')->withErrors('Hubo un problema al crear la VM. Por favor revisa los logs.');
            }

            // Verificar el estado de la VM para asegurar que se inició correctamente
            $vagrantStatusOutput = shell_exec("cd {$vagrantDirectory} && vagrant status 2>&1");
            Log::info('Vagrant status output: ' . $vagrantStatusOutput);

            if (strpos($vagrantStatusOutput, 'running') === false) {
                return redirect()->route('vms.index')->withErrors('La VM fue creada pero no se inició correctamente. Por favor revisa los logs.');
            }

            // Redirigir con mensaje de éxito
            return redirect()->route('vms.index')->with('success', 'VM creada y iniciada exitosamente.');

        } catch (\Exception $e) {
            // Manejar cualquier excepción ocurrida durante el proceso
            Log::error('Error al crear la VM: ' . $e->getMessage());
            return redirect()->route('vms.index')->withErrors('Hubo un error al procesar la solicitud. Por favor revisa los logs.');
        }
    }











    public function show($id)
    {
        $vm = VMConfig::find($id);
        $vagrantDirectory = base_path("vagrant/{$vm->name}");
        $vm_ip = shell_exec("cd {$vagrantDirectory} && vagrant ssh -c 'hostname -I' 2>&1");
        $vnc_port = '5901';
         return view('vms.show', compact('vm', 'vm_ip', 'vnc_port'));
    }


    public function edit($id)
    {
        $vm = VMConfig::find($id);
        return view('vms.edit', compact('vm'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'box' => 'required|string|max:255',
            'memory' => 'required|integer',
            'cpus' => 'required|integer',
            'storage' => 'required|integer',
        ]);

        $vm = VMConfig::find($id);
        $vm->update($request->all());

        $storageInMB = $request->storage * 1024;

        $vagrantfileContent = "
        Vagrant.configure('2') do |config|
            config.vm.define '{$vm->name}' do |vm|
                vm.vm.box = '{$vm->box}'
                vm.vm.provider 'virtualbox' do |vb|
                    vb.memory = '{$vm->memory}'
                    vb.cpus = '{$vm->cpus}'
                    vb.customize ['createhd', '--filename', 'storage.vdi', '--size', '{$storageInMB}'] // Usar el tamaño de almacenamiento calculado
                    vb.customize ['storageattach', :id, '--storagectl', 'IDE Controller', '--port', 1, '--device', 0, '--type', 'hdd', '--medium', 'storage.vdi']
                end
            end
        end
        ";

        $vagrantDirectory = base_path("vagrant/{$vm->name}");
        file_put_contents("{$vagrantDirectory}/Vagrantfile", $vagrantfileContent);

        shell_exec("cd {$vagrantDirectory} && vagrant reload");



        return redirect()->route('vms.index');
    }



    public function destroy($id)
    {
        // Obtener la configuración de VM
        $vm = VMConfig::find($id);

        // Eliminar la VM usando Vagrant
        $vagrantDirectory = base_path("vagrant/{$vm->name}");
        shell_exec("cd {$vagrantDirectory} && vagrant destroy -f");

        // Eliminar la configuración de la base de datos
        $vm->delete();

        // Eliminar el directorio de la VM y en virtualbox
        shell_exec("rm -rf {$vagrantDirectory}");
        shell_exec("VBoxManage unregistervm {$vm->name} --delete");





        return redirect()->route('vms.index');
    }



    ///stop
    public function stop($id)
    {
        // Obtener la configuración de VM
        $vm = VMConfig::find($id);

        // Detener la VM usando Vagrant
        $vagrantDirectory = base_path("vagrant/{$vm->name}");
        shell_exec("cd {$vagrantDirectory} && vagrant halt");

        return redirect()->route('vms.index');
    }

    public function start($id)
    {
         $vm = VMConfig::find($id);

        if (!$vm) {
            return redirect()->route('vms.index')->withErrors('La configuración de la VM no fue encontrada.');
        }

         $vagrantDirectory = base_path("vagrant/{$vm->name}");

        if (!file_exists($vagrantDirectory)) {
            return redirect()->route('vms.index')->withErrors('El directorio de Vagrant no fue encontrado.');
        }

         $output = shell_exec("cd {$vagrantDirectory} && vagrant up 2>&1");

         \Log::info("Vagrant up output for VM '{$vm->name}': " . $output);

         if (strpos($output, 'error') !== false || strpos($output, 'Error') !== false) {
            return redirect()->route('vms.index')->withErrors('Hubo un problema al iniciar la VM. Por favor revisa los logs.');
        }

         $statusOutput = shell_exec("cd {$vagrantDirectory} && vagrant status 2>&1");
        \Log::info("Vagrant status output for VM '{$vm->name}': " . $statusOutput);

        if (strpos($statusOutput, 'running') === false) {
            return redirect()->route('vms.index')->withErrors('La VM fue creada pero no se inició correctamente. Por favor revisa los logs.');
        }

        return redirect()->route('vms.index')->with('success', 'VM iniciada exitosamente.');
    }

}
