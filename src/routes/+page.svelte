<script lang="ts">
import { cookie_info, cookie_update, moduleAdmin, userNow } from '../store';
	import type { User } from '$lib/types/User'

 import CstLogin from '$lib/components/CaLogin.svelte';
	import CstTecnicos from '$lib/components/CaTecnicos.svelte';
	import CstClientes from '$lib/components/CaClientes.svelte';
	import CstMaquinas from '$lib/components/CaMaquinas.svelte';
	import CstProgramador from '$lib/components/CaProgramador.svelte';
	import CstMisVisitas from '$lib/components/CaMisVisitas.svelte';
	import { onMount } from 'svelte';
	import CaProveedores from '$lib/components/CaProveedores.svelte';

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

	const updateUser =(u:any)=>{
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

	$: updateUser (cookie_info('user'))
	
</script>

<svelte:head>
	<title>CA</title>
	<meta name="description" content="Sistema para Control de Agencia de Good Trips Colombia" />
	<link rel="stylesheet" href="./css/font-awesome-4.7.0/css/font-awesome.css" />
</svelte:head>

<section class="w-full h-full m-0 " style="min-heigth:100vh; height:100vh">

	{#if $userNow.id == 0 || $userNow.id == undefined}
<CstLogin /> 
{:else if $moduleAdmin == 'Técnicos'}
<CstTecnicos />
{:else if $moduleAdmin == 'Clientes'}
<CstClientes />
{:else if $moduleAdmin == 'Proveedores'}
<CaProveedores />
{:else if $moduleAdmin == 'Programador'}
<CstProgramador />
{:else if $moduleAdmin == 'Mis Visitas'}
<CstMisVisitas />
{/if}

<footer class="fixed bottom-0 p-1">
	<small class="text-center ml-8">Todos los Derechos Reservados <strong>Good Trips Colombia</strong></small>
</footer>

</section>