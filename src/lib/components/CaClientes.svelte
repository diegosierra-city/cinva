<script lang="ts">
	import type { Cliente } from '$lib/types/Cliente';
	import { onMount } from 'svelte/internal';
	import { cookie_info, apiKey, userNow } from '../../store';
	import { Moon } from 'svelte-loading-spinners';
	import Messages from '$lib/components/Messages.svelte';
	import type { Message } from '$lib/types/Message';
	import CstNewPassword from './CaNewPassword.svelte';

	let m_show: boolean = false;
	let message: Message;

	let listClientes: Array<Cliente> = [];

	const urlAPI = $apiKey.urlAPI;
	const urlFiles = $apiKey.urlFiles;
	//// GET
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
	onMount(async () => {
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
				console.log('recibiendo:');

				listClientes = result;
				console.log(listClientes);
			})
			.catch((error) => console.log(error.message));
	});

	function addCliente() {
		let newCliente: Cliente;

		newCliente = {
			id: Date.now(),
			nombre: '',
			nit: '',
			pais: 'Colombia',
			ciudad: 'Bogotá',
			direccion: '',
			telefono: 0,
			contacto: '',
			cargo_contacto: '',
			activo: true
		};
		listClientes = [...listClientes, newCliente];
	}

	const saveClientes = async () => {
		console.log(listClientes);
		await fetch(urlAPI + '?ref=save-list', {
			method: 'POST', //POST - PUT - DELETE
			body: JSON.stringify({
				user_id: $userNow.id,
				time_life: $userNow.user_time_life,
				token: $userNow.token,
				list: listClientes,
				folder: 'ca_clientes',
				orden: 'nombre'
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

				//++listClientes = result;
			})

			.catch((error) => console.log(error.message));

		//  });
	};

	let folder = 'ca_clientes';
</script>

<svelte:head>
	<title>Clientes</title>
</svelte:head>

<div class="p-3 w-full">
	<div class="relative overflow-x-auto shadow-md sm:rounded-lg w-full">
		<div class="flex">
			<button class="btn-green mr-2 flex" on:click={saveClientes}>
				<i class="fa fa-save mt-1 mr-2" />
				Guardar</button
			>
			<button class="btn-primary mr-2 flex" on:click={() => addCliente()}>
				<i class="fa fa-plus mt-1 mr-2" />
				Agregar Nuevo Cliente</button
			>
			<button class="btn-primary mr-8 flex" on:click={() => addCliente()}>
				<i class="fa fa-plus mt-1 mr-2" />
				Archivo Excel</button
			>
			<input type="search" class="inputA w-32 relative -top-1	" > <button class="btn-primary flex" on:click={() => addCliente()}>
				<i class="fa fa-plus mt-1 mr-2" />
				Buscar</button
			>
		</div>
		<table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
			<thead class="text-xs text-white uppercase bg-primary dark:bg-gray-700 dark:text-gray-400">
				<th scope="col" class="" />
				<th scope="col" class=""> Nombre</th>
				<th scope="col" class=""> Nit </th>
				<th scope="col" class=""> País </th>
				<th scope="col" class=""> Ciudad </th>
				<th scope="col" class=""> Dirección </th>
				<th scope="col" class=""> Telefono </th>
				<!-- <th scope="col" class=""> Contacto </th> -->
				<th scope="col" class=""> Activo </th>
			</thead>
			<tbody>
				{#each listClientes as cliente, i}
					<tr class="bg-white border-b hover:bg-aliceblue align-top">
						<td class="font-bold">{i + 1}</td>
						<td>
							<input type="text" class="inputA" bind:value={cliente.nombre} placeholder="nombre" />
						</td>
						<td>
							<input type="text" class="inputA" bind:value={cliente.nit} placeholder="nit" />
						</td>
<td>
							<input type="text" class="inputA" bind:value={cliente.pais} placeholder="pais" />
									</td>
						<td>
							<input type="text" class="inputA" bind:value={cliente.ciudad} placeholder="ciudad" />
									</td>
						<td>
							<input
								type="text"
								class="inputA"
								bind:value={cliente.direccion}
								placeholder="direccion"
							/>							
						</td>
						<td class="">
							<input
								type="text"
								class="inputA"
								bind:value={cliente.telefono}
								placeholder="telefono"
							/>
						</td>

						<!-- <td>
							<input
								type="text"
								class="inputA"
								bind:value={cliente.contacto}
								placeholder="nombre contacto"
							/>
							<input
								type="text"
								class="inputA"
								bind:value={cliente.cargo_contacto}
								placeholder="cargo"
							/>
						</td> -->

						<td class="text-center"><input type="checkbox" bind:checked={cliente.activo} /></td>
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
