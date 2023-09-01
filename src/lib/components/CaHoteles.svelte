<script lang="ts">
	import type { Hotel } from '$lib/types/Hotel';
	import { onMount } from 'svelte/internal';
	import { cookie_info, apiKey, userNow } from '../../store';
	import { Moon } from 'svelte-loading-spinners';
	import Messages from '$lib/components/Messages.svelte';
	import type { Message } from '$lib/types/Message';
	import CaHotel from './CaHotel.svelte';
	import Fuse from 'fuse.js';
	import type { City } from '$lib/utilities/Cities';
	import { Cities } from '$lib/utilities/Cities';

	let m_show: boolean = false;
	let message: Message;

	let listHoteles: Array<Hotel> = [];

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
			'&folder=ca_hoteles&orden=nombre'
	); //&campo=tipo&campoV=tecnico

	const loadHoteles = async () => {
		await fetch(
			urlAPI +
				'?ref=load-list&user_id=' +
				$userNow.id +
				'&time_life=' +
				$userNow.user_time_life +
				'&token=' +
				$userNow.token +
				'&folder=ca_hoteles&orden=hotel'
		)
			.then((response) => response.json())
			.then((result) => {
				console.log('recibiendo:');

				listHoteles = result;
				console.log(listHoteles);
			})
			.catch((error) => console.log(error.message));
	};

	let newHotel: Hotel;
	newHotel = {
		id: 0,
	tipo_documento: 0,
 documento: '',
 hotel: '',
 categoria: '',
 nombre_comercial: '',
 pais: 'CO',
 departamento: '',
 ciudad_cod: 0,
 ciudad: '',
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

	function addHotel() {
		newHotel = { ...newHotel, id: Date.now() };
		listHoteles = [...listHoteles, newHotel];
		newHotel = {
		id: 0,
	tipo_documento: 0,
 documento: '',
 hotel: '',
 categoria: '',
 nombre_comercial: '',
 pais: 'CO',
 departamento: '',
 ciudad_cod: 0,
 ciudad: '',
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
	}
	}

	const saveHoteles = async () => {
		console.log(listHoteles);
		await fetch(urlAPI + '?ref=save-list', {
			method: 'POST', //POST - PUT - DELETE
			body: JSON.stringify({
				user_id: $userNow.id,
				time_life: $userNow.user_time_life,
				token: $userNow.token,
				list: listHoteles,
				folder: 'ca_hoteles',
				orden: 'hotel'
				//password: pass,
			}),
			headers: {
				'Content-type': 'application/json; charset=UTF-8'
			}
		})
			.then((response) => response.json())
			//.then(result => console.log(result))
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
				//++listHoteles = result;
			})

			.catch((error) => console.log(error.message));

		//  });
	};

	let folder = 'ca_hoteles';
	let showHotel: boolean = false;
	let hotelActual: Hotel = newHotel;
	let positionEdit: number = -1;
	let showFicha: boolean = false;

	onMount(() => {
		loadHoteles();
	});

	function actualizarView(p: Hotel) {
		listHoteles[positionEdit] = p;
		updateExcel();
	}

	const updateExcel = async () => {
		await fetch(urlAPI + '?ref=excel-create', {
			method: 'POST', //POST - PUT - DELETE
			body: JSON.stringify({
				user_id: $userNow.id,
				time_life: $userNow.user_time_life,
				token: $userNow.token,
				list: listHoteles,
				folder: 'ca_hoteles',
				orden: 'nombre',
				archivo: 'excelHoteles.xlsx'
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

	//$: console.log('Hotel:',hotelActual)
	let uploadExcel = false;
	let fileExcel: FileList;
	let file: any;

	function upload() {
		uploadExcel = true;
		const dataArray = new FormData();
		dataArray.append('user_id', String($userNow.id));
		dataArray.append('time_life', String($userNow.user_time_life));
		dataArray.append('token', $userNow.token);
		dataArray.append('file', 'excelHoteles.xlsx');
		dataArray.append('folder', 'ca_hoteles');
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
				loadHoteles();
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
		listHoteles[position].ciudad_cod = cod;
		listHoteles[position].ciudad = ciu;
		filtroCiudades = [];
	}
</script>

<svelte:head>
	<title>Hoteles</title>
</svelte:head>

<div class="p-3 w-full">
	<div class="relative pb-6  w-full">
		<div class="flex">
			<button class="btn-green mr-2 flex" on:click={saveHoteles}>
				<i class="fa fa-save mt-1 mr-2" />
				Guardar</button
			>
			<button class="btn-primary mr-2 flex" on:click={() => addHotel()}>
				<i class="fa fa-plus mt-1 mr-2" />
				Agregar Nuevo Hotel</button
			>

			<!-- <input type="search" class="inputA w-32 relative -top-1" />
			<button class="btn-primary flex" on:click={() => addHotel()}>
				<i class="fa fa-plus mt-1 mr-2" />
				Buscar</button
			> -->
		</div>

		<div class="flex">
			<a
				href="https://goodtripscolombia.com/ca/api/api-CA.php?ref=download&archivo=4"
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
		<table>
			<thead>
				<th scope="col" class="" />
				<th scope="col" class=""> Nombre </th>
				<th scope="col" class=""> Nit </th>

				<th scope="col" class=""> Ciudad </th>
				<th scope="col" class=""> Dirección </th>
				<th scope="col" class=""> Telefono </th>
				<th scope="col" class="" />
				<th scope="col" class=""> Activo </th>
			</thead>
			<tbody>
				{#each listHoteles as hotel, i}
					<tr class="bg-white border-b hover:bg-aliceblue align-top">
						<td class="font-bold">{i + 1}</td>
						<td>
							<input
								type="text"
								class="inputA"
								bind:value={hotel.hotel}
								placeholder="nombre"
							/>
						</td>
						<td>
							<input
								type="text"
								class="inputA"
								bind:value={hotel.documento}
								placeholder="nit"
							/>
						</td>
						<!-- <td>
							<input type="text" class="inputA" bind:value={hotel.pais} placeholder="pais" />
						</td> -->
						<td class="relative">
							<input
								type="text"
								class="inputA"
								id={'P' + hotel.id}
								on:input={handleSearchInput}
								bind:value={hotel.ciudad}
							/>

							{#if filtroCiudades?.length > 0 && editar_ciudad === 'P' + hotel.id}
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
								bind:value={hotel.ciudad_cod}
								placeholder="nombre"
							/>
						</td>
						<td>
							<input
								type="text"
								class="inputA"
								bind:value={hotel.direccion}
								placeholder="direccion"
							/>
						</td>
						<td class="">
							<input
								type="text"
								class="inputA"
								bind:value={hotel.telefono}
								placeholder="telefono"
							/>
						</td>

						<td class="text-center text-xl">
							<button
								class="text-green"
								on:click={() => {
									showFicha = true;
									hotelActual = hotel;
									positionEdit = i;
								}}><i class="fa fa-drivers-license-o mt-2" /></button
							>
						</td>
						<td class="text-center"><input type="checkbox" bind:checked={hotel.activo} /></td>
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
	<CaHotel
		bind:showFicha
		bind:elemento={hotelActual}
		folder="ca_hoteles"
		{actualizarView}
		bind:m_show
		bind:message
	/>
{/if}

{#if m_show == true}
	<Messages bind:m_show bind:message />
{/if}
