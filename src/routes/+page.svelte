<script lang="ts">
import { cookie_info, cookie_update, moduleAdmin, userNow } from '../store';
	import type { User } from '$lib/types/User'

 import ControlLogin from '$lib/components/ControlLogin.svelte';
	
	import ControlClientes from '$lib/components/ControlClientes.svelte';
			
	import { onMount } from 'svelte';
	import ControlProveedores from '$lib/components/ControlProveedores.svelte';
	import ControlServicios from '$lib/components/ControlServicios.svelte';
	import ControlHoteles from '$lib/components/ControlHoteles.svelte';
	import ControlTemporadas from '$lib/components/ControlTemporadas.svelte';
	import ControlTarifas from '$lib/components/ControlTarifas.svelte';
	import ControlUsuarios from '$lib/components/ControlUsuarios.svelte';

	// import PmsHotel from '$lib/components/PmsHotel.svelte';
	// import PmsHabitaciones from '$lib/components/PmsHabitaciones.svelte';
	// import PmsDistribucion from '$lib/components/PmsDistribucion.svelte';
	// import PmsTarifas from '$lib/components/PmsTarifas.svelte';
	// import PmsTarifasEspeciales from '$lib/components/PmsTarifasEspeciales.svelte';
	// import PmsOcupacion from '$lib/components/PmsOcupacion.svelte';
	// import PmsProductos from '$lib/components/PmsProductos.svelte';


	let userN: any;

	let user: User;
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
	user = userNew;

	let time_now = Date.now() / 1000;

	/* const updateUser =(u:any)=>{
	if(u!=null){
	console.log(cookie_info('user'));

		userN = u;//cookie_info('user')
		user = JSON.parse(userN);
		//console.log(user.name)
		$userNow = user;
		console.log(user.user_time_life + ' < ' + time_now);
		if (user.user_time_life < time_now) {
			cookie_update('user', '');
			user = userNew;
			$userNow = userNew;
			//console.log('Out')
		};	
	}
		
	}

	$: updateUser (cookie_info('user')) */

	if (cookie_info('user')) {
		//console.log('pppp')
		userN = cookie_info('user');
		user = JSON.parse(userN);
		$userNow = user;
		//console.log('hhz',$userNow)
		if (user.user_time_life < time_now) {
			cookie_update('user', '');
			user = userNew;
			$userNow = userNew;
			//console.log('pp')
		}
	}
	
</script>

<svelte:head>
	<title>Cinva Control</title>
	<meta name="description" content="Sistema para Control de Agencia de Good Trips Colombia" />
	<link rel="stylesheet" href="./css/font-awesome-4.7.0/css/font-awesome.css" />
</svelte:head>

<section class="w-full h-full m-0 " style="min-heigth:100vh; max-height:100vh overflow-y-auto">
	
{#if $userNow.id == 0 || $userNow.id == undefined}
<ControlLogin /> 

{:else if $moduleAdmin == 'Usuarios'}
<ControlUsuarios />
{:else if $moduleAdmin == 'Clientes'}
<ControlClientes />
{:else if $moduleAdmin == 'Proveedores'}
<ControlProveedores />

{:else if $moduleAdmin == 'Servicios'}
<ControlServicios />

{:else if $moduleAdmin == 'Temporadas'}
<ControlTemporadas />

{:else if $moduleAdmin == 'Hoteles'}
<ControlHoteles />

{:else if $moduleAdmin == 'Tarifas'}
<ControlTarifas />
{/if}

<footer class="fixed bottom-0 p-1 text-center">
	<small class=" ml-8">Todos los Derechos Reservados <strong>Cinva</strong></small>
</footer>

</section>