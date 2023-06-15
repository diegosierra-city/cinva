<script lang="ts">
	import type { User } from '../types/User';
	import { cookie_update, cookie_info, moduleAdmin, userNow } from '../../store';

	let userNew: User = {
		id: 0,
		nombre: '',
		documento: '',
		email: '',
		tipo: '',
		permisos: '',
		user_time_life: 0,
		token: '',
		activo: true
	};

	const salir = () => {
		cookie_update('user', JSON.stringify(userNew));
		$userNow = userNew;
		$moduleAdmin = '';
	};

	if ($moduleAdmin == '') {
		if ($userNow.tipo == 'admin') {
			$moduleAdmin = 'Proveedores';
		} else if ($userNow.tipo == 'tecnico') {
			$moduleAdmin = '';
		}
	}

	/*
1. Clientes - $500.000
2. Proveedores, Hoteles - $500.000
3. Servicios: Traslados - Tours -$500.000
4. Paquetes: $700.000
5. Cotización: $800.000


*/
</script>

<div class="relative z-10">
	<div class="w-14 md:w-52" />

	<div class="w-14 md:w-52 fixed bg-primary shadow top-0 bottom-0 flex-col justify-between">
		<div class="px-2 md:px-4">
			<div class="h-16 w-full items-center pt-5">
				<small class="text-white hidden md:block">{$userNow.nombre}</small>
				<small class="relative text-silver -top-1">{$userNow.tipo}</small>
			</div>
			<ul class="mt-14 pb-2 boton_admin">
				<!-- svelte-ignore a11y-click-events-have-key-events -->

				<li
					class:boton_admin_active={$moduleAdmin === 'Usuarios'}
					on:click={() => {
						$moduleAdmin = 'Usuarios';
					}}
				>
					<div class="flex items-center">
						<i class="fa fa-user-circle-o" />

						<span class="text-sm ml-2 hidden md:block text-black">Usuarios</span>
					</div>
				</li>
				<li class:boton_admin_active={$moduleAdmin === 'Clientes'}>
					<button
						class="flex items-center w-full"
						on:click={() => {
							$moduleAdmin = 'Clientes';
						}}
					>
						<i class="fa fa-group" />

						<span class="text-sm ml-2 hidden md:block text-black">Clientes</span>
					</button>
				</li>

				<li class:boton_admin_active={$moduleAdmin === 'Proveedores'}>
					<button
						class="flex items-center w-full"
						on:click={() => {
							$moduleAdmin = 'Proveedores';
						}}
					>
						<i class="fa fa-handshake-o" />

						<span class="text-sm ml-2 hidden md:block text-black">Proveedores</span>
					</button>
				</li>

				<li class:boton_admin_active={$moduleAdmin === 'Hoteles'}>
					<button
						class="flex items-center"
						on:click={() => {
							$moduleAdmin = 'Hoteles';
						}}
					>
						<i class="fa fa-hotel" />

						<span class="text-sm ml-2 hidden md:block text-black">Hoteles</span>
					</button>
				</li>

				<li class:boton_admin_active={$moduleAdmin === 'Servicios'}>
					<button
						class="flex items-center"
						on:click={() => {
							$moduleAdmin = 'Servicios';
						}}
					>
						<i class="fa fa-map-o" />

						<span class="text-sm ml-2 hidden md:block text-black">Servicios</span>
					</button>
				</li>

				<li class:boton_admin_active={$moduleAdmin === 'Paquetes'}>
					<button
						class="flex items-center"
						on:click={() => {
							$moduleAdmin = 'Paquetes';
						}}
					>
						<i class="fa fa-gears" />

						<span class="text-sm ml-2 hidden md:block text-black">Paquetes</span>
					</button>
				</li>
			</ul>
		</div>

		<div class="px-0 md:px-8 border-t border-white absolute bottom-0 w-full">
			<ul class="w-full md:flex items-center justify-between">
				<li class="cursor-pointer text-white pt-0 pb-3">
					<button class="icono_admin" on:click={salir}>
						<i class="fa fa-power-off text-red" /> salir
					</button>
				</li>

				<!-- <li class="cursor-pointer text-white pt-0 pb-3">
					<button aria-label="open chats" class="icono_admin">
						<i class="fa fa-user" />
					</button>
				</li>
				<li class="cursor-pointer text-white pt-0 pb-3">
					<button aria-label="open settings" class="icono_admin">
						<i class="fa fa-life-ring" />
					</button>
				</li> -->
			</ul>
		</div>
	</div>
</div>
