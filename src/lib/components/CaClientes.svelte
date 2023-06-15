<script lang="ts">
	import type { Cliente } from '$lib/types/Cliente';
	import { onMount } from 'svelte/internal';
	import { cookie_info, apiKey, userNow } from '../../store';
	import { Moon } from 'svelte-loading-spinners';
	import Messages from '$lib/components/Messages.svelte';
	import type { Message } from '$lib/types/Message';
	import CaFicha from './CaFicha.svelte';

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

	const loadClientes = async () => {
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
	}

	let newCliente: Cliente;
	newCliente = {
		id: Date.now(),
 tipo_documento: 0,
 documento: '',
 nombre: '',
 nombre2: '',
 apellido: '',
 apellido2: '',
 nombre_comercial: '',
 pais: 'CO',
 departamento: '',
 ciudad: 'Bogotá',
 telefono: '',
 celular: '',
 direccion: '',
 email: '',
 redes_sociales: '', 
 tipo_cliente: 0,
 activo: true
	};

	function addCliente() {
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
				orden: 'nombre,apellido'
				//password: pass,
			}),
			headers: {
				'Content-type': 'application/json; charset=UTF-8'
			}
		})
			.then((response) => response.json())
			//.then(result => console.log(result))
			.then((result) => {
				console.log('GuardoListado',result);
				message = {
					title: 'Guardar',
					text: 'Se guardó correctamente',
					class: 'message-green',
					accion: ''
				};
				m_show = true;
				updateExcel()
				//++listClientes = result;
			})

			.catch((error) => console.log(error.message));

		//  });
	};

	let folder = 'ca_clientes';
	let showCliente: boolean = false;
	let clienteActual: Cliente = newCliente;
	let positionEdit:number = -1
	let showFicha: boolean = false;

	onMount(()=>{
		loadClientes()
	})

	function actualizarView(p:Cliente){
		listClientes[positionEdit]=p
	updateExcel()
	}

	
const updateExcel= async () => {
		
		await fetch(urlAPI + '?ref=excel-create', {
			method: 'POST', //POST - PUT - DELETE
			body: JSON.stringify({
				user_id: $userNow.id,
				time_life: $userNow.user_time_life,
				token: $userNow.token,
				list: listClientes,
				folder: 'ca_clientes',
				orden: 'nombre,apellido',
				archivo: 'excelClientes.xlsx',
			}),
			headers: {
				'Content-type': 'application/json; charset=UTF-8'
			}
		})
			.then((response) => response.json())
			.then((result) => {
				console.log('excel',result);
				})
			.catch((error) => console.log(error.message));
	}

	
	//$: console.log('Cliente:',clienteActual)
	let uploadExcel=false
	let fileExcel: FileList;
let file: any

	function upload() {
		uploadExcel=true;	
		const dataArray = new FormData();
		dataArray.append('user_id', String($userNow.id));
		dataArray.append('time_life', String($userNow.user_time_life));
		dataArray.append('token', $userNow.token);
		dataArray.append('file', 'excelClientes.xlsx');
		dataArray.append('folder', 'ca_clientes');
		dataArray.append('uploadFile', fileExcel[0]);
	
		fetch(urlAPI + '?ref=upload-excel&prefix=', {
			method: 'POST',
			body: dataArray
		})
			.then((response) => response.json())
			.then((result) => {
				// Successfully uploaded
				console.log('upload:',result);
					message = {
						title: 'Subir Archivo',
						text: 'Se ha subido el archivo y se ha actualizado la base de datos',
						class: 'message-green',
						accion: ''
					};
					m_show = true;
					uploadExcel=false
					loadClientes()
			})
			.catch((error) => console.log(error.message));
	}
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
			
			<!-- <input type="search" class="inputA w-32 relative -top-1" />
			<button class="btn-primary flex" on:click={() => addCliente()}>
				<i class="fa fa-plus mt-1 mr-2" />
				Buscar</button
			> -->
			
		</div>

	<div class="flex">
		<a href="https://goodtripscolombia.com/ca/api/api-CA.php?ref=download&archivo=2" target="_blank">
			<button class="btn-primary mr-4 flex">
				<i class="fa fa-download mt-1 mr-2" />
				Descargar Excel</button
			></a>

			{#if uploadExcel}
									<Moon size="40" unit="px" duration="4s" />
				{/if}
							
				<button
											class="btn-primary px-2 text-xs"
											on:click={() => {
												file.click();
											}}
										>
										<i class="fa fa-upload mx-2" /> Subir Excel
										
										</button>

								<input
									type="file"
									accept=".xlsx"
									class="inputA w-32 hidden"
									placeholder="Subir Excel"
									bind:this={file}
									bind:files={fileExcel}
									on:change={() => {
										uploadExcel=true
										upload();
									}}
								/>
			
		

	</div>
		<table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
			<thead class="text-xs text-white uppercase bg-primary dark:bg-gray-700 dark:text-gray-400">
				<th scope="col" class="" />
				<th scope="col" class=""> Nombre/Razón Social </th>
				<th scope="col" class=""> Nit </th>
				<th scope="col" class=""> País </th>
				<th scope="col" class=""> Ciudad </th>
				<th scope="col" class=""> Dirección </th>
				<th scope="col" class=""> Telefono </th>
				<th scope="col" class="" />
				<th scope="col" class=""> Activo </th>
			</thead>
			<tbody>
				{#each listClientes as cliente, i}
					<tr class="bg-white border-b hover:bg-aliceblue align-top">
						<td class="font-bold">{i + 1}</td>
						<td>
							<input
								type="text"
								class="inputA mr-2"
								bind:value={cliente.nombre}
								placeholder="nombre"
							/>
							<input
								type="text"
								class="inputA"
								bind:value={cliente.apellido}
								placeholder="apellido"
							/>
						</td>
						<td>
							<input
								type="text"
								class="inputA"
								bind:value={cliente.documento}
								placeholder="nit"
							/>
						</td>
						<td>
							<input type="text" class="inputA" bind:value={cliente.pais} placeholder="pais" />
						</td>
						<td>
							<input
								type="text"
								class="inputA"
								bind:value={cliente.ciudad}
								placeholder="ciudad"
							/>
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

						<td class="text-center text-xl">
							<button class="text-green" on:click={()=>{
								showFicha=true
								clienteActual=cliente
								positionEdit=i
							}}><i class="fa fa-drivers-license-o mt-2" /></button>
						</td>
						<td class="text-center"><input type="checkbox" bind:checked={cliente.activo} /></td>
					</tr>
				{:else}
					Sin registros
				{/each}
			</tbody>
		</table>
	</div>
</div>

{#if showFicha}
	 <!-- content here -->
		<CaFicha bind:showFicha bind:elemento={clienteActual} folder='ca_clientes' {actualizarView} bind:m_show bind:message />
{/if}

{#if m_show == true}
	<Messages bind:m_show bind:message />
{/if}