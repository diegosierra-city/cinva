<script lang="ts">
	import type { Cliente } from '$lib/types/Cliente';
	import { onMount } from 'svelte/internal';
	import { cookie_info, apiKey, userNow } from '../../store';
	import { Moon } from 'svelte-loading-spinners';
	import Messages from '$lib/components/Messages.svelte';
	import type { Message } from '$lib/types/Message';
	import ControlFicha from './ControlFicha.svelte';
	import {Cities} from '$lib/utilities/Cities'
	import Fuse from 'fuse.js'

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
			'&folder=cinva_clientes&orden=nombre'
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
				'&folder=cinva_clientes&orden=nombre'
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
	id: 0,
 fecha_contacto: '',
 ultimo_contacto: '',
 vendedor_id: $userNow.id,
 tipo_documento: 0,
 documento: '',
 nombre: '',
 nombre_comercial: '',
 cumpleaños: '',
 pais: 'CO',
 departamento: 'Huila',
 ciudad_cod: 0,
 ciudad: '',
	direccion: '',
 barrio: '',
 telefono: '',
 celular: '',
 email: '',
	facebook: '',
 instagram: '',
 tiktok: '',
 x_twitter: '',
 activo: true
	};

	function addCliente() {
		var f = new Date();
f.getFullYear() + "-"+ f.getMonth()+ "-" +f.getDate();

		newCliente.id=Date.now()
		newCliente.fecha_contacto=String(f)
		listClientes = [...listClientes, newCliente];
		newCliente = {
	id: 0,
 fecha_contacto: '',
 ultimo_contacto: '',
 vendedor_id: $userNow.id,
 tipo_documento: 0,
 documento: '',
 nombre: '',
 nombre_comercial: '',
 cumpleaños: '',
 pais: 'CO',
 departamento: 'Huila',
 ciudad_cod: 0,
 ciudad: '',
	direccion: '',
 barrio: '',
 telefono: '',
 celular: '',
 email: '',
 facebook: '',
 instagram: '',
 tiktok: '',
 x_twitter: '',
 activo: true
	}
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
				folder: 'cinva_clientes',
				orden: 'ciudad,nombre'
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

	let folder = 'cinva_clientes';
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
				folder: 'cinva_clientes',
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
		dataArray.append('folder', 'cinva_clientes');
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


	let filtroCiudades: any;
	let editar_ciudad: string = '';
	const options = {
		includeScore: true,
		useExtendedSearch: true,
		//keys: ['author', 'tags']
		keys: ['ciudad', 'codigo']
	};

	function handleSearchInput(event: any) {
		editar_ciudad = event.target.id;
		const fuse = new Fuse(Cities, options);
		filtroCiudades = fuse.search(event.target.value);
	}

	function handleCitySelect(cod: number, ciu: string, position: number) {
		//console.log('Cod',cod)
		listClientes[position].ciudad_cod = cod;
		listClientes[position].ciudad = ciu;
		filtroCiudades = [];
	}
</script>

<svelte:head>
	<title>Clientes</title>
</svelte:head>

<div class="p-3 w-full">
	<div class="relative pb-6  w-full">
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
		<a href="https://cinva.cityciudad.com/cinva-control/api/api-Control.php?ref=download&archivo=2" target="_blank">
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
				<th scope="col" class=""> Tipo Doc. </th>
				<th scope="col" class=""> Documento </th>
				<th scope="col" class=""> Ciudad </th>
				<th scope="col" class=""> Dirección </th>
				<th scope="col" class=""> Telefono </th>
				<th scope="col" class=""> Email </th>
				<th scope="col" class="" />
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
							
						</td>
						<td>
							<select 
								class="inputA"
								bind:value={cliente.tipo_documento}>
							<option value="13">cc</option>
							<option value="31">nit</option>
							</select>

						</td>
						<td>
							<input
								type="text"
								class="inputA"
								bind:value={cliente.documento}
								placeholder="documento"
							/>
						</td>
						
						<td>
							
							<input
								type="text"
								class="inputA"
								id={'P' + cliente.id}
								on:input={handleSearchInput}
								bind:value={cliente.ciudad}
							/>

							{#if filtroCiudades?.length > 0 && editar_ciudad === 'P' + cliente.id}
								<div
									class="absolute z-10"
									style="height:135px; background: #ccc; overflow-y:auto; overflow-x:hidden padding:10px"
								>
									<ul>
										{#each filtroCiudades as ciudad}
											<li style="border-bottom:dotted 1px white">
												<button
													on:click={() =>
														handleCitySelect(ciudad.item.codigo, ciudad.item.ciudad, i)}
												>
													{ciudad.item.ciudad}
													<small><small>- {ciudad.item.departamento}</small></small>
												</button>
											</li>
										{/each}
									</ul>
								</div>
							{/if}

							<input
								type="hidden"
								class="inputA"
								bind:value={cliente.ciudad_cod}
								placeholder="nombre"
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
<td class="">
							<input
								type="email"
								class="inputA"
								bind:value={cliente.email}
								placeholder="email"
							/>
						</td>
						<td class="text-center text-xl">
							<button class="text-green" on:click={()=>{
								showFicha=true
								clienteActual=cliente
								positionEdit=i
							}}><i class="fa fa-drivers-license-o mt-2" /></button>
						</td>
						<!-- <td class="text-center"><input type="checkbox" bind:checked={cliente.activo} />
						</td> -->
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
		<ControlFicha bind:showFicha bind:elemento={clienteActual} folder='cinva_clientes' {actualizarView} bind:m_show bind:message />
{/if}

{#if m_show == true}
	<Messages bind:m_show bind:message />
{/if}