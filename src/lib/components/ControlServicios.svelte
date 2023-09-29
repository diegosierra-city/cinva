<script lang="ts">
	import type { Servicio } from '$lib/types/Servicio';
	import { onMount } from 'svelte/internal';
	import { cookie_info, apiKey, userNow } from '../../store';
	import { Moon } from 'svelte-loading-spinners';
	import Messages from '$lib/components/Messages.svelte';
	import type { Message } from '$lib/types/Message';
	import ControlFicha from './ControlFicha.svelte';
	import { numberFormat } from '$lib/utilities/NumberFormat';
	import type { City } from '$lib/utilities/Cities';
	import { Cities } from '$lib/utilities/Cities';
	import type { ListFilterCiudad } from '$lib/types/List';

	import Fuse from 'fuse.js';
	import ControlServicio from './ControlServicio.svelte';

	let m_show: boolean = false;
	let message: Message;

	let listServicios: Array<Servicio> = [];
	let listAllServicios: Array<Servicio> = [];	
	let listProveedores: Array<ListFilterCiudad>;
		let listCities: Array<any> =[]

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
			'&folder=cinva_servicios&orden=nombre'
	); //&campo=tipo&campoV=tecnico

	// Función de comparación personalizada
function ordenarCiudadServicio(a:any, b:any) {
  // Primero, compara por ciudad
  if (a.ciudad < b.ciudad) return -1;
  if (a.ciudad > b.ciudad) return 1;

  // Si las ciudades son iguales, compara por servicio
  if (a.tipo_servicio < b.tipo_servicio) return -1;
  if (a.tipo_servicio > b.tipo_servicio) return 1;

  // Si ambas ciudades y servicios son iguales, no hay cambio en el orden
  return 0;
}

let city:number = 0
function filtrarPorCiudad() {
	let ciudadDeseada:number= city
	if(ciudadDeseada==0){
		listServicios= listAllServicios
	}else{
		listServicios= listAllServicios.filter((objeto:any) => objeto.ciudad_cod === ciudadDeseada);
	}
	
}


	const loadServicios = async () => {
		await fetch(
			urlAPI +
				'?ref=load-list&user_id=' +
				$userNow.id +
				'&time_life=' +
				$userNow.user_time_life +
				'&token=' +
				$userNow.token +
				'&folder=cinva_servicios&orden=servicio'
		)
			.then((response) => response.json())
			.then((result) => {
				console.log('recibiendo:');

				listServicios = result;
				listServicios.sort(ordenarCiudadServicio);
				listAllServicios=listServicios
				console.log('LoadServicios', listServicios);
				//
				// Crear un conjunto para almacenar ciudades únicas
const ciudadesUnicasSet = new Set();

// Filtrar y almacenar ciudades únicas en el conjunto
listServicios.forEach(obj => {
  ciudadesUnicasSet.add(JSON.stringify({ ciudad_cod: obj.ciudad_cod, ciudad: obj.ciudad }));
});

// Convertir el conjunto de nuevo en un array y ordenarlo alfabéticamente
const ciudadesUnicas = Array.from(ciudadesUnicasSet).map((str:any) => JSON.parse(str)).sort((a, b) => a.ciudad.localeCompare(b.ciudad));

listCities=[...ciudadesUnicas]
			})
			.catch((error) => console.log(error.message));
	};

	const loadList = async () => {
		await fetch(
			urlAPI +
				'?ref=load-list-filter-ciudad&user_id=' +
				$userNow.id +
				'&time_life=' +
				$userNow.user_time_life +
				'&token=' +
				$userNow.token +
				'&folder=cinva_proveedores&campo=nombre'
		)
			.then((response) => response.json())
			.then((data) => {
				console.log('LoadProveedores', data);
				listProveedores = data;
			})
			.catch((error) => console.log(error.message));
	};

	let newServicio: Servicio;
	newServicio = {
		id: Date.now(),
		ciudad_cod: 0,
		ciudad: '',
		tipo_servicio: 'tour',
		ref: '',
		servicio: '',
		imagen: '',
		children: 0,
		pax1: 0,
		pax2: 0,
		pax3: 0,
		pax4: 0,
		pax7: 0,
		pax10: 0,
		pax15: 0,
		pax20: 0,
		lu: 'si',
		ma: 'si',
		mi: 'si',
		ju: 'si',
		vi: 'si',
		sa: 'si',
		do: 'si',
		compartido: 0,
		clu: 'si',
		cma: 'si',
		cmi: 'si',
		cju: 'si',
		cvi: 'si',
		csa: 'si',
		cdo: 'si',
		proveedor: 0,
		proveedor2: 0,
		proveedor3: 0,
		descripcion: '',
		incluye: '',
		no_incluye: '',
		observaciones: '',
		activo: true
	};

	function addServicio() {
		newServicio = { ...newServicio, id: Date.now() };
	 listServicios = [...listServicios, newServicio];
		}

	const saveServicios = async () => {
		console.log(listServicios);
		await fetch(urlAPI + '?ref=save-list', {
			method: 'POST', //POST - PUT - DELETE
			body: JSON.stringify({
				user_id: $userNow.id,
				time_life: $userNow.user_time_life,
				token: $userNow.token,
				list: listServicios,
				folder: 'cinva_servicios',
				orden: 'servicio'
				//password: pass,
			}),
			headers: {
				'Content-type': 'application/json; charset=UTF-8'
			}
		})
			.then((response) => response.json())
			.then((result) => {
				console.log('GuardoListado', result);
				message = {
					title: 'Guardar',
					text: 'Se guardó correctamente',
					class: 'message-green',
					accion: ''
				};
				m_show = true;
				updateExcel();
			})
			.catch((error) => console.log(error.message));

		//  });
	};

	let folder = 'cinva_servicios';
	let showServicio: boolean = false;
	let servicioActual: Servicio = newServicio;
	let positionEdit: number = -1;
	let showFicha: boolean = false;

	onMount(() => {
		loadServicios();
		loadList();
	});

	function actualizarView(p: Servicio) {
		listServicios[positionEdit] = p;
		updateExcel();
	}

	const updateExcel = async () => {
		await fetch(urlAPI + '?ref=excel-create', {
			method: 'POST', //POST - PUT - DELETE
			body: JSON.stringify({
				user_id: $userNow.id,
				time_life: $userNow.user_time_life,
				token: $userNow.token,
				list: listServicios,
				folder: 'cinva_servicios',
				orden: 'servicio',
				archivo: 'excelServicios.xlsx'
			}),
			headers: {
				'Content-type': 'application/json; charset=UTF-8'
			}
		})
			.then((response) => response.json())
			.then((result) => {
				console.log('excel', result);
			})
			.catch((error) => console.log(error.message));
	};

	//$: console.log('Servicio:',servicioActual)
	let uploadExcel = false;
	let fileExcel: FileList;
	let file: any;

	function upload() {
		uploadExcel = true;
		const dataArray = new FormData();
		dataArray.append('user_id', String($userNow.id));
		dataArray.append('time_life', String($userNow.user_time_life));
		dataArray.append('token', $userNow.token);
		dataArray.append('file', 'excelServicios.xlsx');
		dataArray.append('folder', 'cinva_servicios');
		dataArray.append('uploadFile', fileExcel[0]);

		fetch(urlAPI + '?ref=upload-excel&prefix=', {
			method: 'POST',
			body: dataArray
		})
			.then((response) => response.json())
			.then((result) => {
				// Successfully uploaded
				console.log('upload:', result);
				message = {
					title: 'Subir Archivo',
					text: 'Se ha subido el archivo y se ha actualizado la base de datos',
					class: 'message-green',
					accion: ''
				};
				m_show = true;
				uploadExcel = false;
				loadServicios();
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
		listServicios[position].ciudad_cod = cod;
		listServicios[position].ciudad = ciu;
		filtroCiudades = [];
	}

	var dep: string = '';
	var prevDep: string = '';

	let fileinput: any;
	let image_id: number = 0;
	let image_position: number = 0;

	//function upload(id: number, position: number) {
	const uploadImage = (e: any) => {
		//console.log('FF:'+image_id)
		if (image_id > 1000000) {
			message = {
				title: 'Error',
				text: 'Primero haga click en el botón de guardar servicios',
				class: 'message-red',
				accion: ''
			};
			m_show = true;
		} else {
			let image = e.target.files[0];
			//console.log('imagen',image)
			const dataArray = new FormData();
			dataArray.append('user_id', String($userNow.id));
			dataArray.append('time_life', String($userNow.user_time_life));
			dataArray.append('token', $userNow.token);
			dataArray.append('id', String(image_id));
			dataArray.append('position', '');
			dataArray.append('uploadFile', image);
			dataArray.append('folder', 'cinva_servicios');

			fetch(urlAPI + '?ref=upload&folder=cinva_servicios&prefix=', {
				method: 'POST',
				body: dataArray
			})
				.then((response) => response.json())
				.then((result) => {
					listServicios[image_position] = result[0];
					message = {
						title: 'Subir archivo',
						text: 'El archivo se subió con éxito',
						class: 'message-green',
						accion: ''
					};
					m_show = true;
				})
				.catch((error) => console.log(error.message));
		}
	};
</script>

<svelte:head>
	<title>Servicios</title>
</svelte:head>

<div class="p-3 w-full">
	<div class="relative  w-full pb-6">
		<div class="flex">
			<button class="btn-green mr-2 flex" on:click={saveServicios}>
				<i class="fa fa-save mt-1 mr-2" />
				Guardar</button
			>
			<button class="btn-primary mr-2 flex" on:click={() => addServicio()}>
				<i class="fa fa-plus mt-1 mr-2" />
				Agregar Nuevo Servicio</button
			>
		</div>

		<div class="flex">
			<a
				href="https://cinva.cityciudad.com/cinva-control/api/api-Control.php?ref=download&archivo=3"
				target="_blank"
			>
				<button class="btn-primary mr-4 flex">
					<i class="fa fa-download mt-1 mr-2" />
					Descargar Excel</button
				></a
			>

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
					uploadExcel = true;
					upload();
				}}
			/>
		</div>

		<div>Ciudad: <select class="inputA w-40" bind:value={city} on:change={filtrarPorCiudad}>
			<option value={0}>Todas</option>
		{#each listCities as ct}
			<option value={ct.ciudad_cod}>{ct.ciudad}</option>
		{/each}
		</select>
		</div>

		<table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
			<thead class="text-xs text-white uppercase bg-primary dark:bg-gray-700 dark:text-gray-400">
				<th scope="col" class="" />
				<th scope="col" class=""> Ref </th>
				<th scope="col" class=""> Ciudad </th>
				<th scope="col" class=""> Tipo </th>
				<th scope="col" class=""> Servicio </th>
				<th scope="col" class=""> Proveedor </th>
				<th scope="col" class="">
					<div>Imagen JPG-PNG</div>
					<small>1200-600px</small>
				</th>

				<th scope="col" class="" />
				<th scope="col" class=""> Activo </th>
			</thead>
			<tbody>
				{#if listServicios.length>0}
					
				{#each listServicios as servicio, i}
					<tr class="bg-white border-b hover:bg-aliceblue align-top">
						<td class="font-bold">{i + 1}</td>
						<td class="font-bold">
							<input
								type="text"
								class="inputA w-24"
								bind:value={servicio.ref}
							/>
							</td>
						<td class="relative">
							<input
								type="text"
								class="inputA"
								id={'S' + servicio.id}
								on:input={handleSearchInput}
								bind:value={servicio.ciudad}
							/>

							{#if filtroCiudades?.length > 0 && editar_ciudad === 'S' + servicio.id}
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
								bind:value={servicio.ciudad_cod}
								placeholder="nombre"
							/>
						</td>
						<td>
							<select class="inputA" bind:value={servicio.tipo_servicio}>
								<option value="tour">Tour</option>
								<option value="traslado">Traslado</option>
								<option value="pasadia">Pasadía</option>
								<option value="full day">Full Day</option>
							</select>
						</td>
						<td>
							<input
								type="text"
								class="inputA"
								bind:value={servicio.servicio}
								placeholder="servicio"
							/>
						</td>
						<td>
							{#if listProveedores}
								<select class="inputA" bind:value={servicio.proveedor}>
									{#each listProveedores.filter((prov) => +prov.ciudad_cod == +servicio.ciudad_cod) as pro}
										<option value={pro.id}>{pro.name}</option>
									{/each}
								</select>
							{/if}
						</td>
						<td>
							<div class="flex justify-center">
								{#if servicio.imagen == 'load'}
									<Moon size="20" unit="px" duration="2s" />
								{:else if servicio.imagen != ''}
									<div class="flex items-center">
										<img
											class="w-16 h-auto mr-2"
											src="{urlFiles}imagenes/cinva_servicios/M{servicio.imagen}"
											alt={servicio.servicio}
										/>

										<button
											class="btn-min bg-red mr-2 flex"
											on:click={() => {
												servicio.imagen = '';
											}}
										>
											<i class="fa fa-trash-o m-1" />
										</button>
									</div>
								{/if}
								<div class="flex items-center">
									<button
										class="btn-min bg-primary"
										on:click={() => {
											if (servicio.id > 1000000) {
												message = {
													title: 'Error',
													text: 'Primero haga click en el botón de guardar servicios',
													class: 'message-red',
													accion: ''
												};
												m_show = true;
											} else {
												image_id = servicio.id;
												image_position = i;
												fileinput.click();
												servicio.imagen = 'load';
											}
										}}
									>
										<i class="fa fa-camera m-1" />
									</button>
								</div>
							</div>
						</td>

						<td>
							<button
								class="text-green"
								on:click={() => {
									showFicha = true;
									servicioActual = servicio;
									positionEdit = i;
								}}><i class="fa fa-drivers-license-o mt-2 text-lg" /></button
							>
						</td>
						<td class="text-center"><input type="checkbox" bind:checked={servicio.activo} /></td>
					</tr>
				{:else}
					Sin registros
				{/each}

					{/if}
			</tbody>
		</table>
	</div>
</div>

<input
	style="display:none"
	type="file"
	accept=".jpg, .jpeg, .png"
	on:change={(e) => {
		uploadImage(e);
	}}
	bind:this={fileinput}
/>

{#if showFicha}
	<ControlServicio
		bind:showFicha
		bind:elemento={servicioActual}
		folder="cinva_servicios"
		{actualizarView}
		bind:m_show
		bind:message
		{listProveedores}
	/>
{/if}

{#if m_show == true}
	<Messages bind:m_show bind:message />
{/if}
