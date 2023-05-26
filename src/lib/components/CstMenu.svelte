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

	if($moduleAdmin == ''){
	if($userNow.tipo=='admin'){
		$moduleAdmin = 'Programador'
	}else if ($userNow.tipo=='tecnico'){
		$moduleAdmin = 'Mis Visitas'
	}	
}
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
				{#if $userNow.tipo=='admin'}
					<li
					class:boton_admin_active={$moduleAdmin === 'Técnicos'}
					on:click={() => {
						$moduleAdmin = 'Técnicos';
					}}
				>
					<div class="flex items-center">
						<i class="fa fa-user-circle-o" />

						<span class="text-sm ml-2 hidden md:block">Técnicos</span>
					</div>
					
				</li>
				<li class:boton_admin_active={$moduleAdmin === 'Clientes'}>
					<button
						class="flex items-center w-full"
						on:click={() => {
							$moduleAdmin = 'Clientes';
						}}
					>
						<i class="fa fa-hotel" />

						<span class="text-sm ml-2 hidden md:block">Clientes</span>
					</button>
				</li>
				<li class:boton_admin_active={$moduleAdmin === 'Maquinas'}>
					<button
						class="flex items-center w-full"
						on:click={() => {
							$moduleAdmin = 'Maquinas';
						}}
					>
						<i class="fa fa-print" />

						<span class="text-sm ml-2 hidden md:block">Maquinas</span>
					</button>
				</li>
				<li class:boton_admin_active={$moduleAdmin === 'Programador'}>
					<button
						class="flex items-center"
						on:click={() => {
							$moduleAdmin = 'Programador';
						}}
					>
						<i class="fa fa-calendar-o" />

						<span class="text-sm ml-2 hidden md:block">Programador</span>
					</button>
				</li>
				
				<li class:boton_admin_active={$moduleAdmin === 'Reportes'}>
					<button
						class="flex items-center"
						on:click={() => {
							$moduleAdmin = 'Reportes';
						}}
					>
						<i class="fa fa-line-chart" />

						<span class="text-sm ml-2 hidden md:block">Reportes</span>
					</button>
				</li>
				{:else if $userNow.tipo=='tecnico'}
				<li class:boton_admin_active={$moduleAdmin === 'Mis Visitas'}>
					<button
						class="flex items-center"
						on:click={() => {
							$moduleAdmin = 'Mis Visitas';
						}}
					>
						<i class="fa fa-wrench" />

						<span class="text-sm ml-2 hidden md:block">Mis Visitas</span>
					</button>
				</li>
				{/if}
				

				
				

				

				
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
