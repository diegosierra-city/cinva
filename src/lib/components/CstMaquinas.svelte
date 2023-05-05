<script lang="ts">
	import type { Cliente } from '$lib/types/Cliente';
	import type { Maquina } from '$lib/types/Maquina';
	import { onMount } from 'svelte/internal';
	import { cookie_info, apiKey, userNow } from '../../store';
	import { Moon } from 'svelte-loading-spinners';
	import Messages from '$lib/components/Messages.svelte';
	import type { Message } from '$lib/types/Message';
	import CstDireccionMaquina from './CstDireccionMaquina.svelte';

	let m_show: boolean = false;
	let message: Message;

	let listMaquinas: Array<Maquina> = [];
	let listClientes: Array<Cliente> = [];

	const urlAPI = $apiKey.urlAPI;
	const urlFiles = $apiKey.urlFiles;
	//// GET

	const loadMaquinas = async () => {
		
		console.log(
			urlAPI +
				'?ref=load-list&user_id=' +
				$userNow.id +
				'&time_life=' +
				$userNow.user_time_life +
				'&token=' +
				$userNow.token +
				'&folder=cst_cliente_maquinas&orden=cliente_id'
		); //&campo=tipo&campoV=tecnico
		await fetch(
			urlAPI +
				'?ref=load-list&user_id=' +
				$userNow.id +
				'&time_life=' +
				$userNow.user_time_life +
				'&token=' +
				$userNow.token +
				'&folder=cst_cliente_maquinas&orden=cliente_id'
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
				'&folder=cst_clientes&orden=nombre'
		); //&campo=tipo&campoV=tecnico
		await fetch(
			urlAPI +
				'?ref=load-list&user_id=' +
				$userNow.id +
				'&time_life=' +
				$userNow.user_time_life +
				'&token=' +
				$userNow.token +
				'&folder=cst_clientes&orden=nombre'
		)
			.then((response) => response.json())
			.then((result) => {
				console.log('Clientes:');

				listClientes = result;
				console.log(listClientes);
			})
			.catch((error) => console.log(error.message));
	};

	onMount(() => {
		loadMaquinas()
		loadClientes()
	});

	function addMaquina() {
		let newMaquina: Maquina;

		newMaquina = {
			id: Date.now(),
			cliente_id: 0,
			marca: '',
			modelo: '',
			serial: '',
			contador_negro: 0,
			contador_color: 0,
			tipo_contrato: '',
			direccion: '',
			ultima_visita: '0000-00-00',
			activo: true
		};
		listMaquinas = [...listMaquinas, newMaquina];
	}

	const saveMaquinas = async () => {
		console.log(listMaquinas);
		await fetch(urlAPI + '?ref=save-list', {
			method: 'POST', //POST - PUT - DELETE
			body: JSON.stringify({
				user_id: $userNow.id,
				time_life: $userNow.user_time_life,
				token: $userNow.token,
				list: listMaquinas,
				folder: 'cst_cliente_maquinas',
				orden: 'cliente_id'
				//password: pass,
			}),
			headers: {
				'Content-type': 'application/json; charset=UTF-8'
			}
		})
			.then((response) => response.json())
			//.then(result => console.log(result))
			.then((result) => {
				console.log('ok');
				console.log(result);
				message = {
					title: 'Guardar',
					text: 'Se guardó correctamente',
					class: 'message-green',
					accion: ''
				};
				m_show = true;

				//++listMaquinas = result;
			})

			.catch((error) => console.log(error.message));

		//  });
	};

	
</script>

<svelte:head>
	<title>Máquinas</title>
</svelte:head>

<div class="p-3 w-full">
	<div class="relative overflow-x-auto shadow-md sm:rounded-lg w-full">
		<div class="flex">
			<button class="btn-green mr-2 flex" on:click={saveMaquinas}>
				<i class="fa fa-save mt-1 mr-2" />
				Guardar</button
			>
			<button class="btn-primary flex" on:click={() => addMaquina()}>
				<i class="fa fa-plus mt-1 mr-2" />
				Agregar Nueva Maquina</button
			>
		</div>
		<table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
			<thead class="text-xs text-white uppercase bg-primary dark:bg-gray-700 dark:text-gray-400">
				<th scope="col" class="" />
				<th scope="col" class=""> Cliente</th>
				<th scope="col" class=""> Dirección </th>
				<th scope="col" class=""> Marca </th>
				<th scope="col" class=""> Modelo </th>
				<th scope="col" class=""> Serial </th>
				<th scope="col" class=""> Contador Negro </th>
				<th scope="col" class=""> Contador Color </th>
<th scope="col" class=""> Tipo Contrato </th>
<th scope="col" class=""> Ultima Visita </th>
				<th scope="col" class=""> Activo </th>
			</thead>
			<tbody>
				{#each listMaquinas as maquina, i}
					<tr class="bg-white border-b hover:bg-aliceblue align-top">
						<td class="font-bold">{i + 1}</td>
						<td>
							<select class="inputA w-44" bind:value={maquina.cliente_id}>
							{#each listClientes as cliente}
								 <!-- content here -->
									<option value={cliente.id}>{cliente.nombre}</option>
							{/each}
							</select>
							
						</td>
						<td>
							<CstDireccionMaquina bind:direccion={maquina.direccion} bind:cliente_id={maquina.cliente_id} />
						</td>
						<td>
							<input type="text" class="inputA" bind:value={maquina.marca} placeholder="marca" />
						</td>

						<td>
							<input type="text" class="inputA" bind:value={maquina.modelo} placeholder="modelo" />
							</td>
							<td>
							<input type="text" class="inputA" bind:value={maquina.serial} placeholder="serial" />
							</td>
							<td>
							<input type="text" class="inputA" bind:value={maquina.contador_negro} placeholder="contador negro" />
							</td>
							<td>
							<input type="text" class="inputA" bind:value={maquina.contador_color} placeholder="contador color" />
						</td>
						<td>
							<input
								type="text"
								class="inputA"
								bind:value={maquina.tipo_contrato}
								placeholder="tipo contrato"
							/>
							
						</td>
						<td class="text-center">
							{#if maquina.ultima_visita!='0000-00-00'}
								 <!-- content here -->
									{maquina.ultima_visita}
							{/if}
						</td>
						
						<td class="text-center"><input type="checkbox" bind:checked={maquina.activo} /></td>
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
