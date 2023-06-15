<script lang="ts">
	import type { Proveedor } from '$lib/types/Proveedor';
	import { onMount } from 'svelte/internal';
	import { cookie_info, apiKey, userNow } from '../../store';
	import { Moon } from 'svelte-loading-spinners';
	import Messages from '$lib/components/Messages.svelte';
	import type { Message } from '$lib/types/Message';
	import CaFicha from './CaFicha.svelte';

	let m_show: boolean = false;
	let message: Message;

	let listProveedores: Array<Proveedor> = [];

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
			'&folder=ca_proveedores&orden=nombre'
	); //&campo=tipo&campoV=tecnico

	const loadProveedores = async () => {
		await fetch(
			urlAPI +
				'?ref=load-list&user_id=' +
				$userNow.id +
				'&time_life=' +
				$userNow.user_time_life +
				'&token=' +
				$userNow.token +
				'&folder=ca_proveedores&orden=nombre'
		)
			.then((response) => response.json())
			.then((result) => {
				console.log('recibiendo:');

				listProveedores = result;
				console.log(listProveedores);
			})
			.catch((error) => console.log(error.message));
	}

	let newProveedor: Proveedor;
	newProveedor = {
		id: Date.now(),
		tipo_documento: 0,
		documento: '',
		nombre: '',
		nombre2: '',
		apellido: '',
		apellido2: '',
		nombre_comercial: '',
		pais: 'CO',
		departamento: 'Bogotá',
		ciudad: 'Bogotá',
		telefono: '',
		celular: '',
		direccion: '',
		email: '',
		redes_sociales: '',
		cuenta_bancaria: '',
		banco: '',
		tipo_cuenta: '',
		titular_cuenta: '',
		titular_tipo_documento: 0,
		titular_documento: '',
		tipo_proveedor: 0,
		activo: true
	};

	function addProveedor() {
		listProveedores = [...listProveedores, newProveedor];
	}

	const saveProveedores = async () => {
		console.log(listProveedores);
		await fetch(urlAPI + '?ref=save-list', {
			method: 'POST', //POST - PUT - DELETE
			body: JSON.stringify({
				user_id: $userNow.id,
				time_life: $userNow.user_time_life,
				token: $userNow.token,
				list: listProveedores,
				folder: 'ca_proveedores',
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
				console.log('GuardoListado',result);
				message = {
					title: 'Guardar',
					text: 'Se guardó correctamente',
					class: 'message-green',
					accion: ''
				};
				m_show = true;
				updateExcel()
				//++listProveedores = result;
			})

			.catch((error) => console.log(error.message));

		//  });
	};

	let folder = 'ca_proveedores';
	let showProveedor: boolean = false;
	let proveedorActual: Proveedor = newProveedor;
	let positionEdit:number = -1
	let showFicha: boolean = false;

	onMount(()=>{
		loadProveedores()
	})

	function actualizarView(p:Proveedor){
		listProveedores[positionEdit]=p
	updateExcel()
	}

	
const updateExcel= async () => {
		
		await fetch(urlAPI + '?ref=excel-create', {
			method: 'POST', //POST - PUT - DELETE
			body: JSON.stringify({
				user_id: $userNow.id,
				time_life: $userNow.user_time_life,
				token: $userNow.token,
				list: listProveedores,
				folder: 'ca_proveedores',
				orden: 'nombre',
				archivo: 'excelProveedores.xlsx',
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

	
	//$: console.log('Proveedor:',proveedorActual)
	let uploadExcel=false
	let fileExcel: FileList;
let file: any

	function upload() {
		uploadExcel=true;	
		const dataArray = new FormData();
		dataArray.append('user_id', String($userNow.id));
		dataArray.append('time_life', String($userNow.user_time_life));
		dataArray.append('token', $userNow.token);
		dataArray.append('file', 'excelProveedores.xlsx');
		dataArray.append('folder', 'ca_proveedores');
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
					loadProveedores()
			})
			.catch((error) => console.log(error.message));
	}
</script>

<svelte:head>
	<title>Proveedores</title>
</svelte:head>

<div class="p-3 w-full">
	<div class="relative overflow-x-auto shadow-md sm:rounded-lg w-full">
		<div class="flex">
			<button class="btn-green mr-2 flex" on:click={saveProveedores}>
				<i class="fa fa-save mt-1 mr-2" />
				Guardar</button
			>
			<button class="btn-primary mr-2 flex" on:click={() => addProveedor()}>
				<i class="fa fa-plus mt-1 mr-2" />
				Agregar Nuevo Proveedor</button
			>
			
			<!-- <input type="search" class="inputA w-32 relative -top-1" />
			<button class="btn-primary flex" on:click={() => addProveedor()}>
				<i class="fa fa-plus mt-1 mr-2" />
				Buscar</button
			> -->
			
		</div>

	<div class="flex">
		<a href="https://goodtripscolombia.com/ca/api/api-CA.php?ref=download&archivo=1" target="_blank">
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
				<th scope="col" class=""> Nombre </th>
				<th scope="col" class=""> Nit </th>
				<th scope="col" class=""> País </th>
				<th scope="col" class=""> Ciudad </th>
				<th scope="col" class=""> Dirección </th>
				<th scope="col" class=""> Telefono </th>
				<th scope="col" class="" />
				<th scope="col" class=""> Activo </th>
			</thead>
			<tbody>
				{#each listProveedores as proveedor, i}
					<tr class="bg-white border-b hover:bg-aliceblue align-top">
						<td class="font-bold">{i + 1}</td>
						<td>
							<input
								type="text"
								class="inputA"
								bind:value={proveedor.nombre}
								placeholder="nombre"
							/>
						</td>
						<td>
							<input
								type="text"
								class="inputA"
								bind:value={proveedor.documento}
								placeholder="nit"
							/>
						</td>
						<td>
							<input type="text" class="inputA" bind:value={proveedor.pais} placeholder="pais" />
						</td>
						<td>
							<input
								type="text"
								class="inputA"
								bind:value={proveedor.ciudad}
								placeholder="ciudad"
							/>
						</td>
						<td>
							<input
								type="text"
								class="inputA"
								bind:value={proveedor.direccion}
								placeholder="direccion"
							/>
						</td>
						<td class="">
							<input
								type="text"
								class="inputA"
								bind:value={proveedor.telefono}
								placeholder="telefono"
							/>
						</td>

						<td class="text-center text-xl">
							<button class="text-green" on:click={()=>{
								showFicha=true
								proveedorActual=proveedor
								positionEdit=i
							}}><i class="fa fa-drivers-license-o mt-2" /></button>
						</td>
						<td class="text-center"><input type="checkbox" bind:checked={proveedor.activo} /></td>
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
		<CaFicha bind:showFicha bind:elemento={proveedorActual} folder='ca_proveedores' {actualizarView} bind:m_show bind:message />
{/if}

{#if m_show == true}
	<Messages bind:m_show bind:message />
{/if}
