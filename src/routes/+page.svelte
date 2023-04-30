<script lang="ts">
import { cookie_info, cookie_update, moduleAdmin, userNow } from '../store';
	import type { User } from '$lib/types/User'

 import CstLogin from '$lib/components/CstLogin.svelte';
	import CstTecnicos from '$lib/components/CstTecnicos.svelte';
	import CstClientes from '$lib/components/CstClientes.svelte';

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

	$: if (cookie_info('user')) {
		console.log(cookie_info('user'));

		userN = cookie_info('user');
		user = JSON.parse(userN);
		//console.log(user.name)
		$userNow = user;
		console.log(user.user_time_life + ' < ' + time_now);
		if (user.user_time_life < time_now) {
			cookie_update('user', '');
			user = userNew;
			$userNow = userNew;
			//console.log('Out')
		}
	}
</script>

<svelte:head>
	<title>CST</title>
	<meta name="description" content="sistema para Control de Servicio Técnico" />
	<link rel="stylesheet" href="./css/font-awesome-4.7.0/css/font-awesome.css" />
</svelte:head>

<section class="w-full h-full m-0 " style="min-heigth:100vh; height:100vh">
	{#if $userNow.id == 0 || $userNow.id == undefined}
<CstLogin /> 
{:else if $moduleAdmin == 'Técnicos'}
<CstTecnicos />
{:else if $moduleAdmin == 'Clientes'}
<CstClientes />
{/if}

<footer class="fixed bottom-0">
	<p class="text-center">Todos los Derechos Reservados DISJHER</p>
</footer>

</section>