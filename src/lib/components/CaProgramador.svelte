<script lang="ts">
	import type { Cliente } from '$lib/types/Cliente';
	import type { Maquina } from '$lib/types/Maquina';
	import type { Tecnico } from '$lib/types/Tecnico';
	import type { Visita } from '$lib/types/Visita';
	import { onMount } from 'svelte/internal';
	import { cookie_info, apiKey, userNow } from '../../store';
	import { Moon } from 'svelte-loading-spinners';
	import Messages from '$lib/components/Messages.svelte';
	import type { Message } from '$lib/types/Message';
	import CstDireccionMaquina from './CaDireccionMaquina.svelte';

	let m_show: boolean = false;
	let message: Message;

	let listMaquinas: Array<Maquina> = [];
	let listClientes: Array<Cliente> = [];
	let listTecnicos: Array<Tecnico> = [];
	let listProgramacion: Array<Visita> = [];

	const urlAPI = $apiKey.urlAPI;
	const urlFiles = $apiKey.urlFiles;
	//// GET
	const fechaActual = new Date();

	// Obtener los componentes de la fecha (año, mes y día)
	const año = fechaActual.getFullYear();
	const mes = fechaActual.getMonth() + 1; // Los meses van de 0 a 11, así que hay que sumar 1
	const dia = fechaActual.getDate() + 1;
	const hoy = fechaActual.getDate();

	// Formatear la fecha como 'YYYY-mm-dd'
	let fecha = `${año}-${mes < 10 ? '0' + mes : mes}-${dia < 10 ? '0' + dia : dia}`;
	let fechaHoy = `${año}-${mes < 10 ? '0' + mes : mes}-${hoy < 10 ? '0' + hoy : hoy}`;

	const loadMaquinas = async () => {
		console.log(
			urlAPI +
				'?ref=load-list&user_id=' +
				$userNow.id +
				'&time_life=' +
				$userNow.user_time_life +
				'&token=' +
				$userNow.token +
				'&folder=ca_cliente_maquinas&orden=cliente_id'
		); //&campo=tipo&campoV=tecnico
		await fetch(
			urlAPI +
				'?ref=load-list&user_id=' +
				$userNow.id +
				'&time_life=' +
				$userNow.user_time_life +
				'&token=' +
				$userNow.token +
				'&folder=ca_cliente_maquinas&orden=cliente_id'
		)
			.then((response) => response.json())
			.then((result) => {
				console.log('recibiendo:');

				listMaquinas = result;
				console.log(listMaquinas);
			})
			.catch((error) => console.log(error.message));
	};

	const loadClientes = async () => {
		console.log(
			urlAPI +
				'?ref=load-list&user_id=' +
				$userNow.id +
				'&time_life=' +
				$userNow.user_time_life +
				'&token=' +
				$userNow.token +
				'&folder=ca_clientes&orden=nombre'
		); //&campo=tipo&campoV=tecnico
		await fetch(
			urlAPI +
				'?ref=load-list&user_id=' +
				$userNow.id +
				'&time_life=' +
				$userNow.user_time_life +
				'&token=' +
				$userNow.token +
				'&folder=ca_clientes&orden=nombre'
		)
			.then((response) => response.json())
			.then((result) => {
				console.log('Clientes:');

				listClientes = result;
				console.log(listClientes);
			})
			.catch((error) => console.log(error.message));
	};

	const loadTecnicos = async () => {
		console.log(
			urlAPI +
				'?ref=load-list&user_id=' +
				$userNow.id +
				'&time_life=' +
				$userNow.user_time_life +
				'&token=' +
				$userNow.token +
				'&folder=ca_tecnicos&orden=nombre&campo=tipo&campoV=tecnico'
		); //
		await fetch(
			urlAPI +
				'?ref=load-list&user_id=' +
				$userNow.id +
				'&time_life=' +
				$userNow.user_time_life +
				'&token=' +
				$userNow.token +
				'&folder=ca_tecnicos&orden=nombre&campo=tipo&campoV=tecnico'
		)
			.then((response) => response.json())
			.then((result) => {
				console.log('Tecnicos:');

				listTecnicos = result;
				console.log(listTecnicos);
			})
			.catch((error) => console.log(error.message));
	};

	const loadProgramacion = async (f: string) => {
		console.log(
			urlAPI +
				'?ref=load-list&user_id=' +
				$userNow.id +
				'&time_life=' +
				$userNow.user_time_life +
				'&token=' +
				$userNow.token +
				'&folder=ca_visita&orden=id&campo=fecha_programada&campoV=' +
				f
		); //
		await fetch(
			urlAPI +
				'?ref=load-list&user_id=' +
				$userNow.id +
				'&time_life=' +
				$userNow.user_time_life +
				'&token=' +
				$userNow.token +
				'&folder=ca_visita&orden=id&campo=fecha_programada&campoV=' +
				f
		)
			.then((response) => response.json())
			.then((result) => {
				console.log('recibiendoProgramacion:');

				listProgramacion = result;
				console.log(listProgramacion);
			})
			.catch((error) => console.log(error.message));
	};

	onMount(() => {
		loadMaquinas();
		loadClientes();
		loadTecnicos();
		loadProgramacion(fecha);
	});

	
	let newProgramacion: Visita = {
		id: 0,
		cliente_id: 0,
		numero: 0,
		tecnico_id: 0,
		maquina_id: 0,
		fecha_solicitud: '',
		fecha_programada: '',
		hora_estimada: '',
		fecha_servicio: '',
		hora_inicio: '',
		hora_final: '',
		fecha_reporte: '',
		descripcion_trabajo: '',
		tipo_servicio: '',
		reporte: '',
		foto1: '',
		foto2: '',
		contador_negro: 0,
		contador_color: 0,
		valor: 0,
		estado: '',
		fecha_pago: ''
	};

	function addProgramacion(cliente_id: number, maquina_id: number) {
		//alert(`cliente_id ${cliente_id} y ${maquina_id}`)
		let er: any;
		listProgramacion.find((visita) => visita.maquina_id === maquina_id)? er=false : er=true;
//alert(er)
if(er==false){
	message = {
					title: 'error',
					text: 'esta máquina ya esta incluida en la programación de '+fecha,
					class: 'message-red',
					accion: ''
				};
				m_show = true;
				return
}
		newProgramacion.id = Date.now();
		newProgramacion.cliente_id = cliente_id;
		newProgramacion.maquina_id = maquina_id;
		newProgramacion.fecha_programada = fecha;
		newProgramacion.fecha_solicitud = fechaHoy;
		//menu_list.push(new_menu)
		listProgramacion = [...listProgramacion, newProgramacion];
		newProgramacion = {
			id: 0,
			cliente_id: 0,
			numero: 0,
			tecnico_id: 0,
			maquina_id: 0,
			fecha_solicitud: '',
			fecha_programada: '',
			hora_estimada: '',
			fecha_servicio: '',
			hora_inicio: '',
			hora_final: '',
			fecha_reporte: '',
			descripcion_trabajo: '',
			tipo_servicio: '',
			reporte: '',
			foto1: '',
			foto2: '',
			contador_negro: 0,
			contador_color: 0,
			valor: 0,
			estado: '',
			fecha_pago: ''
		};
	}

	const deleteProgramacion = (id: number) => {
		if (confirm('Desea Borrar esta Maquina de la Programción?')) {
			listProgramacion = listProgramacion.filter((item) => item.id != id);
			
			message = {
				title: 'Borrar de Programación',
				text: 'Se ha borrado la Maquina del listado de la programación',
				class: 'message-red',
				accion: ''
			};
			m_show = true;

		}

		//return menu_list;
	}

const saveProgramacion = async () =>{
	//revisamos que todos tengan Tecnico asignado
	let er: any;
		listProgramacion.find((visita) => visita.tecnico_id === 0)? er=false : er=true;
//alert(er)
if(er==false){
	message = {
					title: 'error',
					text: 'Hay Visitas que no tienen Técnico Responsable '+fecha,
					class: 'message-red',
					accion: ''
				};
				m_show = true;
				return
}
//console.log(listProgramacion)
	await fetch(
		urlAPI+'?ref=save-list', {
			method: 'POST', //POST - PUT - DELETE
			body: JSON.stringify({
				user_id: $userNow.id,
				time_life: $userNow.user_time_life,
				token: $userNow.token,
				list: listProgramacion,
				folder: 'ca_visita',
				orden:'id',
				campo: 'fecha_programada',
				campoV: fecha
			}),
			headers: {
				'Content-type': 'application/json; charset=UTF-8'
			}
		})
			.then((response) => response.json())
			//.then(result => console.log(result))
			.then((result) => {
				console.log(result)
					message = {
						title: 'Guardar',
						text: 'La Programación se guardó',
						class: 'message-green',
						accion: ''
					};
					m_show = true;

					//console.log("Muy Bien:"+result[0].ok);
					//++listProgramacion = result;
				
			})

			.catch((error) => console.log(error.message));

		//  });
	}

</script>

<svelte:head>
	<title>Programador</title>
</svelte:head>

<div class="p-3 w-full grid grid-cols-2 gap-2">
	<div class="relative overflow-x-auto w-full">
		<h3>Programar:</h3>
		<div class="flex">
			<input type="date" class="inputA w-32" bind:value={fecha} />
			<button class="btn-primary mr-2 mt-1 flex" on:click={()=>loadProgramacion(fecha)}>
				<i class="fa fa-angle-double-down mt-1 mr-2" />
				Cargar</button
			>
			<button class="btn-green mr-2 mt-1 flex" on:click={()=>saveProgramacion()}>
				<i class="fa fa-save mt-1 mr-2" />
				Guardar</button
			>
		</div>

		<table>
			<thead class="text-xs text-white uppercase bg-primary dark:bg-gray-700 dark:text-gray-400">
				<th scope="col" class="" />
				<th scope="col" class=""> Reporte</th>
				<th scope="col" class=""> Técnico</th>
				<th scope="col" class=""> Cliente </th>
				<th scope="col" class=""> Maquina </th>
				<th scope="col" class=""> Contadores</th>
			</thead>
			<tbody>
				{#each listProgramacion as pro, i}
					<tr class="bg-white border-b hover:bg-aliceblue align-top">
						<td class="font-bold active:bg-red"
							>{i + 1}<br />
							
							{#if pro.id>1000000000}
								 <!-- content here -->
									<button on:click={()=>{
										deleteProgramacion(pro.id)
									}}>
										<i class="fa fa-minus-square text-red text-xl" />
									</button>
							{/if}
							
						</td>
						<td class="text-red text-center">
							{#if pro.id<10000000}
								 <!-- content here -->
									{pro.id}
							{:else}
								 <!-- else content here -->
									--
							{/if}
						</td>
						<td>
							<select class="inputA" bind:value={pro.tecnico_id}>
								{#each listTecnicos as tecnico}
									<!-- content here -->
									<option value={tecnico.id}>{tecnico.nombre}</option>
								{/each}
							</select>
						</td>
						<td>
							{listClientes.find((cliente) => cliente.id === pro.cliente_id)?.nombre}<br />
							{listMaquinas.find((maquina) => maquina.id === pro.maquina_id)?.direccion}
						</td>
						<td>
							{listMaquinas.find((maquina) => maquina.id === pro.maquina_id)?.marca}-{listMaquinas.find((maquina) => maquina.id === pro.maquina_id)?.modelo}<br>
							{listMaquinas.find((maquina) => maquina.id === pro.maquina_id)?.serial}
						</td>
						<td>
							N: {listMaquinas.find((maquina) => maquina.id === pro.maquina_id)?.contador_negro}<br
							/>
							C: {listMaquinas.find((maquina) => maquina.id === pro.maquina_id)?.contador_color}
						</td>
					</tr>
				{:else}
					Sin registros
				{/each}
			</tbody>
		</table>
	</div>

	<div class="relative overflow-x-auto overflow-y-auto sm:rounded-lg w-full h-full">
		<h3>Listado de Máquinas:</h3>
		<table class="w-full text-sm text-left">
			<thead class="text-xs text-white uppercase bg-primary">
				<th scope="col" class="" />
				<th scope="col" class=""> Cliente</th>
				<th scope="col" class=""> Máquina </th>
				<th scope="col" class=""> Contadores </th>
				<th scope="col" class=""> Contrato </th>
			</thead>
			<tbody>
				{#each listMaquinas as maquina, i}
					<tr class="bg-white border-b hover:bg-aliceblue align-top">
						<td class="font-bold"
							>{i + 1}<br />
							<button
								on:click={() => {
									addProgramacion(maquina.cliente_id, maquina.id);
								}}
							>
								<i class="fa fa-plus-square text-green text-xl" />
							</button>
						</td>
						<td>
							{listClientes.find((cliente) => cliente.id === maquina.cliente_id)?.nombre}<br />
							{maquina.direccion}
						</td>
						<td>
							{maquina.marca} <br />
							{maquina.modelo}<br />
							{maquina.serial}
						</td>
						<td>
							Negro: {maquina.contador_negro}<br />
							Color: {maquina.contador_color}
						</td>

						<td>
							{maquina.tipo_contrato}<br />
							{#if maquina.ultima_visita != '0000-00-00'}
								<!-- content here -->
								{maquina.ultima_visita}
							{/if}
						</td>
					</tr>
				{:else}
					Sin registros
				{/each}
			</tbody>
		</table>
	</div>
</div>

{#if m_show == true}
	<Messages bind:m_show bind:message />
{/if}
